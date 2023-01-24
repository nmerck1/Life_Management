<?php
////declare(strict_types = 1);
include '../includes/autoloader.inc.php';
include '../includes/function_library.inc.php';

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../pages/login.php");
    exit;
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

  $secondary_tab = '';
  $navbar->show_secondary_nav($loggedin, $secondary_tab);

?>

<script type="text/javascript">
	function view_msg(n_id){
		// setup the ajax request
		var xhttp = new XMLHttpRequest();
    // get variables on page:
    var user_id = document.getElementById('user_id');
    var action = 'View';
		// create link to send GET variables through
		var query_string = "../ajax/notifications.ajax.php";
		query_string += "?n_id=" + n_id;
    query_string += "&user_id=" + user_id.innerHTML;
    query_string += "&action=" + action;

		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
			 document.getElementById("display_message").innerHTML = this.responseText;
			}
		};
		xhttp.open("GET", query_string, true);
		xhttp.send();

		// when the data is returned after ajax, it redirects back to inventory
		//window.location = "../pages/messages.php";
	}

  function send_to_ajax(){
    //alert("calling function to send to ajax");
    // first check form is good
    if (check_form()) {
      // setup the ajax request
  		var xhttp = new XMLHttpRequest();
  		// get variables from inputs below:
  		var finance_id = document.getElementById('finance_id');
  		var update_type = document.getElementById('update_type');
  		var form_type = document.getElementById('form_type');
  		var to_user = document.getElementById('to_user');

      var new_company = document.getElementById('new_company');

  		// create link to send GET variables through
  		var query_string = "../ajax/finances.ajax.php";
  		query_string += "?selected_id=" + finance_id.innerHTML;
  		query_string += "&update_type=" + update_type.innerHTML;
  		query_string += "&form_type=" + form_type.innerHTML;
  		query_string += "&user_id=" + to_user.innerHTML;

      query_string += "&new_company=" + new_company.value;

      /*
  		xhttp.onreadystatechange = function() {
  			if (this.readyState == 4 && this.status == 200) {
  			 document.getElementById("test").innerHTML = this.responseText;
       } else {
         alert("error");
       }
  		};
      */
      xhttp.open("GET", query_string, true);
      xhttp.onreadystatechange = function (oEvent) {
          if (xhttp.readyState === 4) {
              if (xhttp.status === 200) {
                document.getElementById("test").innerHTML = this.responseText;
                //alert("Success");
              } else {
                 alert("Error", xhttp.statusText);
              }
          }
      };
  		xhttp.send();

  		// when the data is returned after ajax, it redirects back to inventory
  		//window.location = "../pages/notifications.php";
    } else {
      alert('Form needs to be filled out');
    }

	}

  function check_form(){
    var new_company = document.getElementById('new_company');
    if (new_company.value == '') {
      return false;
    }
    return true;
  }

  function update_element_value(element, value){
    element.value = value;
  }

  function scroll_notifications(next_prev_num, table_scroll){
      // setup the ajax request
      var xhttp = new XMLHttpRequest();
      // get variables from inputs below:
      var current_page_num = document.getElementById(table_scroll + '_current_page_num');
      var user_id = document.getElementById('user_id');
      var date_search = document.getElementById('date_search');
      var scroll_div_name = table_scroll + "_scroll_div";
      var show_per_page = 5;

      var action = 'Next';
      if (next_prev_num == 0) {
        action = 'Prev';
      }

      var can_scroll = true;
      if (action == 'Prev') {
        if (current_page_num.innerHTML == 1) {
          can_scroll = false;
        }
      }

      if ( can_scroll == true ) {
        // create link to send GET variables through
        var query_string = "../ajax/scroll.ajax.php";
        query_string += "?current_num=" + current_page_num.innerHTML;
        //query_string += "&form_type=" + "Expense";
        query_string += "&user_id=" + user_id.innerHTML;
        query_string += "&action=" + action;
        query_string += "&date_search=" + date_search.innerHTML;
        query_string += "&table_scroll=" + table_scroll;
        query_string += "&show_per_page=" + show_per_page;

        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
           document.getElementById(scroll_div_name).innerHTML = this.responseText;
          }
        };
        xhttp.open("GET", query_string, true);
        xhttp.send();

        // when the data is returned after ajax, it redirects back to inventory
        //window.location = "../pages/manage.php";
      }
    }
  </script>



<div class="container text-center">
    <div class="container" style="height:600px; display:contents;">
      <?php
      echo '<p id="user_id" style="display:none;" value="'.$user_id.'">'.$user_id.'</p>';
      // this is for looking at previous finance dates in the system
      $date_search = date('Y-m-d');
      if (isset($_POST['date_search'])) {
        $date_search = $_POST['date_search'];
      }

      echo '<p id="date_search" style="display:none;" value="'.$date_search.'">'.$date_search.'</p>';

      echo '<p id="test" name="test"></p>';//style="display:none;"
      echo '<p style="display:none;" id="update_type" name="update_type" value="Update">Update</p>';

      echo '<div class="div_element_block">';
        echo '<h4 style="text-align:center;"><i class="bi-mailbox"> </i>Inbox</h4>';
        echo '<p style="width:95%; margin:0px; text-align:center;">';
          echo '<button name="prev_button" onclick="scroll_notifications(0, \'Notifications\');" style="float:left; background:none; border:none; font-size:20px; height:32px;">';
            echo '<i class="actions"><p class="bi-arrow-left-square"></p></i>';
          echo '</button>';
          echo '<button name="next_button" onclick="scroll_notifications(1, \'Notifications\');" style="float:right; background:none; border:none; font-size:20px; height:32px;">';
            echo '<i class="actions"><p class="bi-arrow-right-square"></p></i>';
          echo '</button>';
        echo '</p>';

        echo '<div id="Notifications_scroll_div">';
            library_notifications_table($user_id, "First", 1, $date_search, 5, $conn);
        echo '</div>';
      echo '</div>';
      echo '<br>';
      echo '<div class="container" id="display_message"></div>';

      ?>
    </div>
</div>

<?php
  $footer = new Footer();
  $footer->show_footer();
?>

</body>
</html>
