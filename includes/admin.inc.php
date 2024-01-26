<?php
declare(strict_types=1);
include '../includes/autoloader.inc.php';
include '../includes/function_library.inc.php';

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../pages/login.php");
    exit;
}

$show_error = false;

$selected_id = '';
if (isset($_GET['selected_id'])){
	$selected_id = $_GET['selected_id'];
  //echo "selected_id: ".$selected_id."<br>";
}

$update_type = '';	// this is either Update or Insert
if (isset($_GET['update_type'])){
	$update_type = $_GET['update_type'];
  //echo "update_type: ".$update_type."<br>";
}

$user_id = '';	// this is either Update or Insert
if (isset($_GET['user_id'])){
	$user_id = $_GET['user_id'];
}

$loggedin = $_SESSION['loggedin'];
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];
$id_role = $_SESSION['id_role'];

// check messages on every page
$messages = library_get_num_notifications($user_id);

// Prepare a select statement
//echo "user_id: ". $user_id."<br>";
//echo "id_role: ". $id_role."<br>";
$sql = "
    SELECT *
    FROM users
    WHERE user_id = '".$user_id."'
    AND is_active = 1
";
//echo $sql;
$dbh = new Dbh();
$stmt = $dbh->connect()->query($sql);
//echo $sql;
// should only populate one row of data
while ($row = $stmt->fetch()) {
  $user_name = $row['user_name'];
  $user_fname = $row['user_fname'];
  $user_lname = $row['user_lname'];
  $user_theme = $row['user_theme'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php
    $header = new Header();
    $header->show_header($user_theme);
  ?>
</head>
<body>

<?php
	$navbar = new Navbar();
	$navbar->show_header_nav($loggedin, $user_fname, $id_role, $messages);

  $navbar->show_section_nav($loggedin, '', $id_role);

  $secondary_tab = '';
  $navbar->show_secondary_nav($loggedin, $secondary_tab);

  $finance_nav = new FinanceNavbar();
  $finance_nav->show_header_nav('', $secondary_tab);
?>

<script type="text/javascript">
	function send_to_ajax(){
    // first check form is good
    if (check_form()) {
  		// setup the ajax request
  		var xhttp = new XMLHttpRequest();
  		// get variables from inputs below:
  		var selected_id = document.getElementById('selected_id');
  		var update_type = document.getElementById('update_type');
  		var user_id = document.getElementById('user_id');

  		// create link to send GET variables through
  		var query_string = "../ajax/diet.ajax.php";
  		query_string += "?selected_id=" + selected_id.innerHTML;
  		query_string += "&update_type=" + update_type.innerHTML;
  		query_string += "&user_id=" + user_id.innerHTML;

  		xhttp.onreadystatechange = function() {
  			if (this.readyState == 4 && this.status == 200) {
  			 document.getElementById("test").innerHTML = this.responseText;
  			}
  		};
  		xhttp.open("GET", query_string, true);
  		xhttp.send();

  		// when the data is returned after ajax, it redirects back to inventory
  		window.location = "../includes/admin.inc.php";
    } else {
      alert('Form needs to be filled out. (Make sure amount and quantity are at least 1.)');
    }
	}

  function check_form(){
    //var name = document.getElementById('name');
		//var amount = document.getElementById("amount");
    //var quantity = document.getElementById("quantity");

  //  if (name.value == '' || amount.value == 0 || quantity.value == 0) {
      //return false;
    //}
    return true;
  }

  function update_element_value(element, value){
    element.value = value;
  }
</script>

<?php
	echo '<p id="selected_id" style="display:none;" value="'.$selected_id.'">'.$selected_id.'</p>';
	echo '<p id="user_id" style="display:none;" value="'.$user_id.'">'.$user_id.'</p>';

	echo '<p id="test"></p>';


  echo '<div class="container" style="width:390px; text-align:right;">';
    $fc_id = 14;  // this is defaulted to other
		$name = "";
    $amount = 0.00;
    $mea_id = '';
    $quantity = 1;
    $calories = 0.00;
    $carbs = 0.00;
    $protein = 0.00;
    $fat = 0.00;
    $meal_time = 'Breakfast';
		$log_date = date('Y-m-d');	// default to today
		$notes = "";
		// check if there is an id, then we are either editing or deleting an existing record
		if ($selected_id != NULL && $update_type == 'Copy') {
      // this is a currently existing record
      echo '<h1>New Food Log (Copy)</h1>';
      $update_type = 'Copy';
      // load variables from selected_id
      // get values from selected id in table:
      $sql = "SELECT * FROM food_logs WHERE fl_id = '".$selected_id."' ";
      $dbh = new Dbh();
      $stmt = $dbh->connect()->query($sql);
      //echo $sql;
      // should only populate one row of data
      while ($row = $stmt->fetch()) {
        $fc_id = $row['id_food_category'];
        $name = $row['fl_name'];
        $amount = $row['fl_amount'];
        $mea_id = $row['id_mea'];
        $quantity = $row['fl_quantity'];
        $calories = $row['fl_calories'];
        $carbs = $row['fl_carbs'];
        $protein = $row['fl_protein'];
        $fat = $row['fl_fat'];
        $meal_time = $row['fl_meal_time'];

        // we aren't using this copied date because this is a new entry for today
        //$get_date = date_create($row['fl_log_date']);
        //$formatted_date = date_format($get_date, 'Y-m-d');
        //$log_date = $formatted_date;

        $notes = $row['fl_notes'];
      }
		} elseif ($selected_id != NULL && $update_type != 'Copy') {
      //echo "selected_id: ".$selected_id."<br>";
      //echo "update_type: ".$update_type."<br>";
      // this is a currently existing record
			echo '<h1>Edit Food Log</h1>';
			$update_type = 'Update';
			// load variables from selected_id
			// get values from selected id in table:
				$sql = "SELECT * FROM food_logs WHERE fl_id = '".$selected_id."' ";
				$dbh = new Dbh();
        $stmt = $dbh->connect()->query($sql);
				//echo $sql;
				// should only populate one row of data
				while ($row = $stmt->fetch()) {
          $fc_id = $row['id_food_category'];
      		$name = $row['fl_name'];
          $amount = $row['fl_amount'];
          $mea_id = $row['id_mea'];
          $quantity = $row['fl_quantity'];
          $calories = $row['fl_calories'];
          $carbs = $row['fl_carbs'];
          $protein = $row['fl_protein'];
          $fat = $row['fl_fat'];
          $meal_time = $row['fl_meal_time'];

          $get_date = date_create($row['fl_log_date']);
					$formatted_date = date_format($get_date, 'Y-m-d');
          $log_date = $formatted_date;

      		$notes = $row['fl_notes'];
				}
		} else {
      // this is a new record we are creating
			echo '<h1>Add New Food Log</h1>';
			$update_type = 'Insert';
    }
		echo '<p id="update_type" value="'.$update_type.'" style="display:none;">'.$update_type.'</p>';
    echo '<i style="color:grey;">
    (If you don\'t know the macros of a food and you can\'t find the food in dropdown, then estimate as best as you can.)
    </i>';
    echo '<br><br>';
		// print the form type here
    //echo '<table class="table table-dark" style="background-color:#3a5774; text-align:center;">';
      //echo '<tr>';

        //echo '<td style="text-align: right;">';
					echo '<br>';

          library_get_food_categories_dropdown($fc_id);

          echo '<br>';
          echo '<label>Quantity: </label>';
					echo '<input type="number" id="quantity" value="'.$quantity.'" style="text-align:right;"  onchange="update_element_value(this, this.value)"></input>';
          echo '<br>';


          library_get_meal_time_dropdown($meal_time);

					echo '<br>';
					echo '<label>Log Date: </label>';
					echo '<input type="date" id="log_date" value="'.$log_date.'"></input>';
					echo '<br>';

        //echo '</td>';

        //echo '<td style="text-align: left;">';
          echo '<br>';
          echo '<label>Name: </label>';
          echo '<input type="text" id="name" value="'.$name.'"  onchange="update_element_value(this, this.value)"></input>';
          echo '<br>';
          echo '<label>Amount: </label>';
          echo '<input type="number" id="amount" value="'.$amount.'" placeholder="x.xx" style="text-align:right;"  onchange="update_element_value(this, this.value)"></input>';

          library_get_measurements_dropdown($mea_id);

          echo '<br>';
          echo '<label>Calories: </label>';
          echo '<input type="number" id="calories" value="'.$calories.'" style="text-align:right;"  onchange="update_element_value(this, this.value)"></input>';
          echo '<br>';
          echo '<label>Carbs: </label>';
          echo '<input type="number" id="carbs" value="'.$carbs.'" style="text-align:right;" onchange="update_element_value(this, this.value)"></input>';
          echo '<br>';
          echo '<label>Protein: </label>';
          echo '<input type="number" id="protein" value="'.$protein.'" style="text-align:right;" onchange="update_element_value(this, this.value)"></input>';
          echo '<br>';
          echo '<label>Fat: </label>';
          echo '<input type="number" id="fat" value="'.$fat.'" style="text-align:right;" onchange="update_element_value(this, this.value)"></input>';
          echo '<br>';
        //echo '</td>';

      //echo '</tr>';
      //echo '<tr>';

        //echo '<td colspan=2>';
          echo '<textarea id="notes" style="height:100px; width:300px;" placeholder="(Notes)">'.$notes.'</textarea>';
        //echo '</td>';

      //echo '</tr>';
    //echo '</table>';

		echo '<button style="margin:auto; display:inherit;" name="save_button" onclick="send_to_ajax();" value="Save" class="btn btn-success btn-md">Save</button>';
    echo '<br>';

  echo '</div>';

  $footer = new Footer();
  $footer->show_footer();
?>

</body>
</html>
