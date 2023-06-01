<?php
//declare(strict_types = 1);
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
$messages = library_get_num_messages($user_id);

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


  $navbar->show_section_nav($loggedin, '', $id_role);

?>

<script type="text/javascript">
	function view_msg(msg_id){
		// setup the ajax request
		var xhttp = new XMLHttpRequest();
    // get variables on page:
    var user_id = document.getElementById('user_id');
    var action = 'View';
		// create link to send GET variables through
		var query_string = "../ajax/messages.ajax.php";
		query_string += "?msg_id=" + msg_id;
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
    <div class="container" style="height:600px;">
      <?php

      echo '<p style="display:none;" id="user_id" name="user_id" value="'.$user_id.'">'.$user_id.'</p>';

      echo '<h1>Inbox</h1>';
      echo '<table class="table table-dark" style="background-color:#3a5774;">'; // mini table to display months
        echo '<tr>';
          echo '<th>From</th>';
          echo '<th>Subject</th>';
          echo '<th>Sent</th>';
          echo '<th style="background-color: rgb(33, 37, 46);">';
            //echo '<a href="../ajax/messages.ajax.php?form_type=Income&user_id='.$user_id.'"><p class="bi-plus-circle" style="color:white;"></p></a>';
          echo '</th>';
        echo '</tr>';


          $sql = "
                  SELECT
                    m.msg_id,
                    m.msg_subject,
                    m.msg_message,
                    m.msg_send_date,
                    m.msg_read_date,
                    m.is_active,

                    ur.role_name AS 'from_role_name',
                    ur.role_color AS 'from_role_color',

                    fu.id_role AS 'from_role',
                    fu.user_name AS 'from_username',
                    fu.user_icon AS 'from_icon',
                    fu.user_fname AS 'from_fname',
                    fu.user_lname AS 'from_lname'
                  FROM messages m
                  LEFT JOIN users fu ON m.from_user = fu.user_id
                  LEFT JOIN user_roles ur ON fu.id_role = ur.role_id
                  WHERE m.is_active = 1
                  AND m.to_user = ".$user_id."

                  ORDER BY m.msg_send_date DESC;
          ";
          //echo $sql;
          $dbh = new Dbh();
          $stmt = $dbh->connect()->query($sql);

          while ($row = $stmt->fetch()) {
            echo '<tr>';
              //echo '<td style="display:none;"><p id="msg_id" name="msg_id" value="'.$row['msg_id'].'">'.$row['msg_id'].'</p></td>';
              //echo '<td>'.$row['from_fname'].' '.$row['from_lname'].'</td>';
              $list_style = 'none';
              if ($row['msg_read_date'] < date('2020-01-01 00:00:00')) {
                $list_style = 'square';
              }
              echo '<td>';
                echo '<ul>';
                  echo '<li style="list-style-type:'.$list_style.'; color:rgb(0 114 255);">';
                    echo '<p style="color:'.$row['from_role_color'].';">'.$row['from_username'].' ('.$row['from_role_name'].')</p>';
                  echo '</li>';
                echo '</ul>';
              echo '</td>';
              echo '<td>'.$row['msg_subject'].'</td>';
              $date_string = strtotime($row['msg_send_date']);
              echo '<td style="color:grey;">' .date('M, d', $date_string). '</td>';
              echo '<td style="background:rgb(33, 37, 46);">';
                //echo '<a href="../includes/messages.ajax.php?user_id='.$user_id.'"><p class="bi-eye-fill" style="color:white;"></p></a>';
                echo '<button style="text-align:center; margin:auto; background-color:rgb(33, 37, 46); color:white; border:none;" name="view" onclick="view_msg('.$row['msg_id'].');" value="View" class="bi-eye-fill"></button>';
              echo '</td>';
            echo '</tr>';
          }

      echo '</table>';
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
