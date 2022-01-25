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
}

$form_type = '';		// this is either Expense, Income, Bill, Passive, Budget, etc.
if (isset($_GET['form_type'])){
	$form_type = $_GET['form_type'];
} else {
	$show_error = true;
}

$update_type = '';	// this is either Update or Insert
if (isset($_GET['update_type'])){
	$update_type = $_GET['update_type'];
}

//echo "update_type: ".$update_type."<br>";

$user_id = '';
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
	//use Style\Navbar;
	$navbar = new Navbar();
	$navbar->show_header_nav($loggedin, $user_fname, $id_role, $messages);
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
  		var form_type = document.getElementById('form_type');
  		var user_id = document.getElementById('user_id');

      var reason = document.getElementById('reason');
      var amount_owed = document.getElementById('amount_owed');
      if (selected_id.innerHTML != null && selected_id.innerHTML != '') {
        var amount_paid = document.getElementById('amount_paid');
      }
      var user = document.getElementById('user');
      var user_value = user.options[user.selectedIndex].value;
      if (update_type.innerHTML != "Update" && update_type.innerHTML != "Edit") {
        var owe_date = document.getElementById('owe_date');
      }

  		// create link to send GET variables through
  		var query_string = "../ajax/ious.ajax.php";
  		query_string += "?selected_id=" + selected_id.innerHTML;
  		query_string += "&update_type=" + update_type.innerHTML;
  		query_string += "&form_type=" + form_type.innerHTML;
  		query_string += "&user_id=" + user_id.innerHTML;

      query_string += "&reason=" + reason.value;
      query_string += "&amount_owed=" + amount_owed.value;
      if (selected_id.innerHTML != null && selected_id.innerHTML != '') {
        query_string += "&amount_paid=" + amount_paid.value;
      }
      query_string += "&user=" + user_value;
      if (update_type.innerHTML != "Update" && update_type.innerHTML != "Edit") {
        query_string += "&owe_date=" + owe_date.value;
      }

  		xhttp.onreadystatechange = function() {
  			if (this.readyState == 4 && this.status == 200) {
  			 document.getElementById("test").innerHTML = this.responseText;
  			}
  		};
  		xhttp.open("GET", query_string, true);
  		xhttp.send();

  		// when the data is returned after ajax, it redirects back to inventory
  		window.location = "../pages/ious.php";
    } else {
      alert('Form needs to be filled out');
    }

	}

  function check_form(){
		var amount_owed = document.getElementById("amount_owed");
  	var reason = document.getElementById("reason");

    if (amount_owed.value == 0 || amount_owed.value < 0) {
      return false;
    }
    if (reason.value == '' || reason.value == null) {
      return false;
    }
    return true;
  }

  function update_element_value(element, value){
    element.value = value;
  }
</script>

<?php
	echo '<p id="selected_id" style="display:none;" value="'.$selected_id.'">'.$selected_id.'</p>';
  echo '<p id="form_type" style="display:none;" value="'.$form_type.'">'.$form_type.'</p>';
	echo '<p id="user_id" style="display:none;" value="'.$user_id.'">'.$user_id.'</p>';

	echo '<p id="test"></p>';


?>
<div class="container" style="height:600px; text-align:right; width:390px;">
    <div>
				<?php
					if (!$show_error) {
            // check form to set
						if ($form_type == 'Loan') {
								// default variables
								$update_type = "";
                $reason = "";
								$amount_owed = 0.00;
                $amount_paid = 0.00;
                $user = "";
								$owe_date = date('Y-m-d');	// default to today
                // element for editing:
                $paid_off_element = "";
                $editable_owe_amount = "";
								// check if there is an id, then we are either editing or deleting an existing record
								if ($selected_id != NULL) {// this is a currently existing record
									echo '<h1>Edit Loan</h1>';
									$update_type = 'Update';
									// load variables from selected_id
									// get values from selected id in table:
										$sql = "SELECT * FROM ious WHERE iou_id = '".$selected_id."' ";
										$dbh = new Dbh();
										$stmt = $dbh->connect()->query($sql);
										//echo $sql;
										// should only populate one row of data
										while ($row = $stmt->fetch()) {
											// format date
											$get_date = date_create($row['iou_owe_date']);
											$formatted_date = date_format($get_date, 'Y-m-d');

                      $reason = $row['iou_reason'];
      								$amount_owed = $row['iou_amount_owed'];
                      $amount_paid = $row['iou_amount_paid'];
                      $user = $row['iou_debtor_id'];
                      $owe_date = $formatted_date;
										}
                    $paid_off_element = '<label>Amount Paid: </label>';
  									$paid_off_element .= '<input type="number" id="amount_paid" value="'.$amount_paid.'" placeholder="x.xx" style="text-align:right;" onchange="update_element_value(this, this.value)"></input>';
  								  $paid_off_element .=  '<br>';

                    $editable_owe_amount = 'readonly';
								} else {
									// this is a new record we are creating
									echo '<h1>Add New Loan</h1>';
									$update_type = 'Insert';
								}
								echo '<p id="update_type" value="'.$update_type.'" style="display:none;">'.$update_type.'</p>';
                echo '<i style="color:grey;">
                (This is the page where you create a loan for your new debtor to pay you back.)
                </i>';
                echo '<br><br>';

								// print the form type here
								echo '<div class="container">';

                  echo '<label>Loaner: </label>';
                  echo '<input type="text" value="'.$user_name.' (You)" readonly></input>';
                  echo '<br>';

                  echo '<label>Debtor: </label>';
						         library_get_users_exclude_current($user_id, $user);
                  echo '<br>';

									echo '<label>Amount Owed: </label>';
									echo '<input type="number" id="amount_owed" value="'.$amount_owed.'" placeholder="x.xx" style="text-align:right;" onchange="update_element_value(this, this.value)" '.$editable_owe_amount.'></input>';
									echo '<br>';

                  echo $paid_off_element;

                  //echo '<label>Reason for Debt: </label>';
									//echo '<input type="text" id="reason" value="'.$reason.'" onchange="update_element_value(this, this.value)"></input>';
									//echo '<br>';
                  if ($update_type != "Update" && $update_type != "Edit") {
                    echo '<label>Owe Date: </label>';
  									echo '<input type="date" id="owe_date" value="'.$owe_date.'" onchange="update_element_value(this, this.value)"></input>';
  									echo '<br>';
                  }

									echo '<textarea id="reason" style="height:100px; width:300px;" placeholder="(Reason)" onchange="update_element_value(this, this.value)" value="">'.$reason.'</textarea>';
									echo '<br>';

									echo '<button style="margin:auto; display:inherit;" name="save_button" onclick="send_to_ajax();" value="Save" class="btn btn-success btn-md">Save</button>';
								echo '</div>';
              } elseif ($form_type == 'Debt') {
                // default variables
								$update_type = "";
                $reason = "";
								$amount_owed = 0.00;
                $amount_paid = 0.00;
                $user = "";
								$owe_date = date('Y-m-d');	// default to today
                // element for editing:
                $paid_off_element = "";
                $editable_owe_amount = "";
								// check if there is an id, then we are either editing or deleting an existing record
								if ($selected_id != NULL) {// this is a currently existing record
									echo '<h1>Edit Debt</h1>';
									$update_type = 'Update';
									// load variables from selected_id
									// get values from selected id in table:
										$sql = "SELECT * FROM ious WHERE iou_id = '".$selected_id."' ";
										$dbh = new Dbh();
										$stmt = $dbh->connect()->query($sql);
										//echo $sql;
										// should only populate one row of data
										while ($row = $stmt->fetch()) {
											// format date
											$get_date = date_create($row['iou_owe_date']);
											$formatted_date = date_format($get_date, 'Y-m-d');

                      $reason = $row['iou_reason'];
      								$amount_owed = $row['iou_amount_owed'];
                      $amount_paid = $row['iou_amount_paid'];
                      $user = $row['iou_loaner_id'];
                      $owe_date = $formatted_date;
										}
                    $paid_off_element = '<label>Amount Paid: </label>';
  									$paid_off_element .= '<input type="number" id="amount_paid" value="'.$amount_paid.'" placeholder="x.xx" style="text-align:right;" onchange="update_element_value(this, this.value)"></input>';
  								  $paid_off_element .=  '<br>';

                    $editable_owe_amount = 'readonly';
								} else {
									// this is a new record we are creating
									echo '<h1>Add New Debt</h1>';
									$update_type = 'Insert';
								}
								echo '<p id="update_type" value="'.$update_type.'" style="display:none;">'.$update_type.'</p>';
                echo '<i style="color:grey;">
                (This is the page where you create a debt for you to pay back your new loaner.)
                </i>';
                echo '<br><br>';

								// print the form type here
								echo '<div class="container">';

                  echo '<label>Debtor: </label>';
                  echo '<input type="text" value="'.$user_name.' (You)" readonly></input>';
                  echo '<br>';

                  echo '<label>Loaner: </label>';
									library_get_users_exclude_current($user_id, $user);
                  echo '<br>';

									echo '<label>Amount Owed: </label>';
									echo '<input type="number" id="amount_owed" value="'.$amount_owed.'" placeholder="x.xx" style="text-align:right;" onchange="update_element_value(this, this.value)" '.$editable_owe_amount.'></input>';
									echo '<br>';

                  echo $paid_off_element;

                  //echo '<label>Reason for Debt: </label>';
									//echo '<input type="text" id="reason" value="'.$reason.'" onchange="update_element_value(this, this.value)"></input>';
									//echo '<br>';
                  if ($update_type != "Update" && $update_type != "Edit") {
                    echo '<label>Owe Date: </label>';
  									echo '<input type="date" id="owe_date" value="'.$owe_date.'" onchange="update_element_value(this, this.value)"></input>';
  									echo '<br>';
                  }
									echo '<textarea id="reason" style="height:100px; width:300px;" placeholder="(Reason)">'.$reason.'</textarea>';
									echo '<br>';

									echo '<button style="margin:auto; display:inherit;" name="save_button" onclick="send_to_ajax();" value="Save" class="btn btn-success btn-md">Save</button>';
								echo '</div>';
              }
					} else {
						$error_msg = new Library();
						echo '<h2 style="color:red; text-align:center;">'. $error_msg->get_error_msg(). '</h2>';
					}
				?>
    </div>
</div>

<?php
  $footer = new Footer();
  $footer->show_footer();
?>

</body>
</html>
