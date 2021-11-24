<?php
include '../includes/autoloader.inc.php';
include '../includes/function_library.inc.php';

// variables that we will always get
$n_id;
if (isset($_GET['n_id'])) {
  $n_id = $_GET['n_id'];
}
$user_id;
if (isset($_GET['user_id'])) {
  $user_id = $_GET['user_id'];
}

//echo "msg_id: ".$msg_id."<br>";
//echo "user_id: ".$user_id."<br>";

echo '<div>';

    $sql = "
            SELECT
              n.n_id,
              n.n_subject,
              n.n_message,
              n.n_type,
              n.n_send_date,
              n.n_read_date,
              n.is_active,

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
            AND n.n_to_user = ".$user_id."
            AND n.n_id = ".$n_id.";
    ";
    //echo $sql;
    $dbh = new Dbh();
    $stmt = $dbh->connect()->query($sql);

    $msg_read_date = '';
    echo '<table class="table table-dark" style="background-color:#3a5774; width:100%;">';
      while ($row = $stmt->fetch()) {
        $msg_read_date = $row['n_read_date'];
        echo '<tr style="text-align:left; border:1px solid rgb(47, 115, 152);">';
          echo '<td><i style="color:grey;">Subject: </i>"'.$row['n_subject'].'"</td>';
        echo '</tr>';
        echo '<tr style="text-align:left; border:1px solid rgb(47, 115, 152);">';
          echo '<td><i style="color:grey;">From: </i>'.$row['from_icon'].' <span style="color:'.$row['from_role_color'].';">'.$row['from_username'].' ('.$row['from_role_name'].')</span></td>';
        echo '</tr>';
        echo '<tr style="text-align:left; border:1px solid rgb(47, 115, 152);">';
          echo '<td><i style="color:grey;">Message: </i><br>'.$row['n_message'].'</td>';
        echo '</tr>';
      }
    echo '</table>';
echo '</div>';

// set the date of being read and viewed now only if it hasn't been read
//echo "msg_read_date: ".$msg_read_date."<br>";
if ($msg_read_date < date('2020-01-01 00:00:00')) {
  $sql = "
          UPDATE notifications
          SET n_read_date = TIMESTAMP(CURRENT_TIMESTAMP)
          WHERE n_id = $n_id;
  ";
  $dbh = new Dbh();
  $stmt = $dbh->connect()->query($sql);
}




//header("Location: ../pages/messages.php");
//exit();
