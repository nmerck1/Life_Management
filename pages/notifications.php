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
  //use Style\Navbar;
  $navbar = new Navbar();
  $navbar->show_header_nav($loggedin, $user_fname, $id_role, $messages);
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

  function scroll_notifications(next_prev_num){
      // setup the ajax request
      var xhttp = new XMLHttpRequest();
      // get variables from inputs below:
      var current_page_num = document.getElementById('current_page_num');
      var user_id = document.getElementById('user_id');
      var date_search = document.getElementById('date_search');
      var table_scroll = 'Notifications'

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

        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
           document.getElementById("scroll_div").innerHTML = this.responseText;
          }
        };
        xhttp.open("GET", query_string, true);
        xhttp.send();

        // when the data is returned after ajax, it redirects back to inventory
        //window.location = "../pages/finances.php";
      }
    }
  </script>



<div class="container text-center">
  <!--
  <div class="row content">
    <div id="left_sidenav" class="col-sm-2 sidenav">
      <p class="bi-card-list" style="font-size: 1rem; color: white;"><a href="#"> Plans</a></p>
      <p class="bi-list-check" style="font-size: 1rem; color: white;"><a href="#"> Goals</a></p>
      <p class="bi-lightbulb" style="font-size: 1rem; color: white;"><a href="#"> Ideas</a></p>
    </div>
  -->
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
          echo '<button name="prev_button" onclick="scroll_notifications(0);" style="float:left; background:none; border:none; font-size:20px; height:32px;">';
            echo '<i class="actions"><p class="bi-arrow-left-square"></p></i>';
          echo '</button>';
          echo '<button name="next_button" onclick="scroll_notifications(1);" style="float:right; background:none; border:none; font-size:20px; height:32px;">';
            echo '<i class="actions"><p class="bi-arrow-right-square"></p></i>';
          echo '</button>';
        echo '</p>';

        echo '<div id="scroll_div">';
          echo '<p id="current_page_num" style="text-align:center; display:none;" value="1">1</p>'; //style="display:none;"
          echo '<p id="page_show" style="text-align:center; color:grey;">(Page 1)</p>';
            $sql = "
                    SELECT
                      n.n_id,
                      n.n_subject,
                      n.n_message,
                      n.n_type,
                      n.n_send_date,
                      n.n_read_date,
                      n.is_active,
                      n.n_to_user,

                      ur.role_name AS 'from_role_name',
                      ur.role_color AS 'from_role_color',

                      fu.id_role AS 'from_role',
                      fu.user_name AS 'from_username',
                      fu.user_icon AS 'from_icon',
                      fu.user_fname AS 'from_fname',
                      fu.user_lname AS 'from_lname'

                    FROM notifications n
                    LEFT JOIN users fu ON n.n_from_user = fu.user_id
                    LEFT JOIN user_roles ur ON fu.id_role = ur.role_id

                    WHERE n.is_active = 1
                    AND n.n_to_user = ".$user_id." OR n.n_to_user = 0

                    ORDER BY n.n_send_date DESC
                    LIMIT 0, 5;
            ";
            //echo $sql;
            $dbh = new Dbh();
            $stmt = $dbh->connect()->query($sql);
            // get num rows to check
            $num_stmt = $conn->prepare($sql);
            $num_stmt->execute();
            /* store the result in an internal buffer */
            $num_stmt->store_result();
            if ($num_stmt->num_rows > 0) {
              echo '<table class="table table-dark" style="width:100%;">'; // mini table to display months
                echo '<tr>';
                  echo '<th>Type</th>';
                  echo '<th>From</th>';
                  echo '<th>Subject</th>';
                  echo '<th>Sent</th>';
                  echo '<th class="end_row_options">';
                    //echo '<a href="../ajax/messages.ajax.php?form_type=Income&user_id='.$user_id.'"><p class="bi-plus-circle" style="color:white;"></p></a>';
                  echo '</th>';
                echo '</tr>';
            } else {
              echo '<p style="text-align:center;">(There are no notifications)</p>';
            }



            $is_alternate_row = false;
            $add_alternating_class = '';
            while ($row = $stmt->fetch()) {
              echo '<tr>';

              if ($is_alternate_row == false) {
                $add_alternating_class = '';
                $is_alternate_row = true;
              } else {
                $add_alternating_class = 'class="alternating_row"';
                $is_alternate_row = false;
              }
                //echo '<td style="display:none;"><p id="msg_id" name="msg_id" value="'.$row['msg_id'].'">'.$row['msg_id'].'</p></td>';
                //echo '<td>'.$row['from_fname'].' '.$row['from_lname'].'</td>';
                $font_weight = 'font-weight:normal;';
                if ($row['n_read_date'] < date('2020-01-01 00:00:00')) {
                  $font_weight = 'font-weight:bold;';
                }

                echo '<td '.$add_alternating_class.' style="'.$font_weight.'">'.$row['n_type'].'</td>';
                echo '<td '.$add_alternating_class.' style="'.$font_weight.'">';
                  echo '<i style="color:'.$row['from_role_color'].'; style="'.$font_weight.'"">'.$row['from_username'].' ('.$row['from_role_name'].')</i>';
                echo '</td>';
                echo '<td '.$add_alternating_class.' style="'.$font_weight.'">'.$row['n_subject'].'</td>';
                $date_string = strtotime($row['n_send_date']);
                echo '<td '.$add_alternating_class.' style="color:grey;">' .date('M, d', $date_string). '</td>';
                echo '<td class="end_row_options">';
                  //echo '<a href="../includes/messages.ajax.php?user_id='.$user_id.'"><p class="bi-eye-fill" style="color:white;"></p></a>';
                  echo '<button class="end_row_options" style="text-align:center; margin:auto; color:white; background-color:black; border:none;" name="view" onclick="view_msg('.$row['n_id'].');" value="View"><i class="actions"><p class="bi-eye-fill"></p></i></button>';
                echo '</td>';
              echo '</tr>';
            }

        echo '</table>';
        echo '<br>';
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
