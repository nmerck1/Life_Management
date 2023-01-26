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

$action = '';	// this is either Update or Insert
if (isset($_GET['action'])){
	$action = $_GET['action'];
  //echo "update_type: ".$update_type."<br>";
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
	//use Style\Navbar;
	$navbar = new Navbar();
	$navbar->show_header_nav($loggedin, $user_fname, $id_role, $messages);

  $secondary_tab = '';
  $navbar->show_secondary_nav($loggedin, $secondary_tab);

  $finance_nav = new FinanceNavbar();
  $finance_nav->show_header_nav('', $secondary_tab);
?>

<script type="text/javascript">
	function send_to_ajax(){
    // first check form is good
    if (check_form() == 1) {
      alert('Form is good, sending to ajax.');
  		// setup the ajax request
  		var xhttp = new XMLHttpRequest();
  		// get variables from inputs below:
  		var action = document.getElementById('action');
  		var user_id = document.getElementById('user_id');

      var new_password = document.getElementById('new_password');

  		// create link to send GET variables through
  		var query_string = "../ajax/profile.ajax.php";
  		query_string += "?user_id=" + user_id.innerHTML;
  		query_string += "&action=" + action.innerHTML;

      query_string += "&new_password=" + new_password.value;

  		xhttp.onreadystatechange = function() {
  			if (this.readyState == 4 && this.status == 200) {
  			 document.getElementById("test").innerHTML = this.responseText;
  			}
  		};
  		xhttp.open("GET", query_string, true);
  		xhttp.send();

  		// when the data is returned after ajax, it redirects back to inventory
  		window.location = "../pages/profile.php";

    } else if (check_form() == 0) {
      alert('Form needs to be filled out.');
    } else if (check_form() == 2) {
      alert('New password cannot be old password.');
    } else if (check_form() == 3) {
      alert('New password inputs do not match.');
    } else if (check_form() == 4) {
      alert('New password needs to have at least 1 special character, uppercase letter, and a number.');
    } else if (check_form() == 5) {
      alert('Incorrect old password.');
    } else {
      alert('Unknown return number code.');
    }
	}

  function check_form(){
    //var old_password = document.getElementById('old_password');
		var new_password = document.getElementById("new_password");
    var confirm_password = document.getElementById("confirm_password");
    //var saved_old_password = document.getElementById("saved_old_password");

    if (new_password.value == '' || confirm_password.value == '') {
      return 0;
    }
    //if (old_password.value == new_password.value) {
    //  return 2;
    //}
    if (new_password.value != confirm_password.value) {
      return 3;
    }
    //if (old_password.value != saved_old_password.value) {
    //  return 5;
    //}

    return 1;
  }

  function update_element_value(element, value){
    element.value = value;
  }
</script>

<?php
	//echo '<p id="selected_id" style="display:none;" value="'.$selected_id.'">'.$selected_id.'</p>';
	echo '<p id="user_id" style="display:none;" value="'.$user_id.'">'.$user_id.'</p>';

	echo '<p id="test"></p>';

  echo '<br>';
  echo '<br>';
  echo '<br>';
  echo '<br>';
  echo '<br>';

  echo '<div class="mainContentContainer">';
    echo '<div class="container text-center"  style="height:600px;">';

      echo '<div class="div_element_block">';
        echo '<label>Themes: </label>';
        echo '<br>';
        // get values from selected id in table:
        $sql = "SELECT * FROM themes WHERE theme_is_active = 1; ";
        $dbh = new Dbh();
        $stmt = $dbh->connect()->query($sql);
        // should only populate one row of data
        while ($row = $stmt->fetch()) {
            echo '<a class="btn btn-primary" href="../ajax/theme.ajax.php?user_id='.$user_id.'&theme_file='.$row['theme_file'].'">'.$row['theme_name'].'</a>';
            echo '<br>';
            echo '<br>';
        }
      echo '</div>';

    echo '</div>';
  echo '</div>';

  $footer = new Footer();
  $footer->show_footer();
?>

</body>
</html>
