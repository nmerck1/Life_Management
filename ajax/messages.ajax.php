<?php
include '../includes/autoloader.inc.php';
include '../includes/function_library.inc.php';

// variables that we will always get
$msg_id;
if (isset($_GET['msg_id'])) {
  $msg_id = $_GET['msg_id'];
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
            AND m.msg_id = ".$msg_id.";
    ";
    //echo $sql;
    $dbh = new Dbh();
    $stmt = $dbh->connect()->query($sql);

    $msg_read_date = '';
    echo '<table>';
      while ($row = $stmt->fetch()) {
        $msg_read_date = $row['msg_read_date'];
        echo '<tr style="text-align:left; border:1px solid rgb(47, 115, 152);">';
          echo '<td><i style="color:grey;">Subject: </i>"'.$row['msg_subject'].'"</td>';
        echo '</tr>';
        echo '<tr style="text-align:left; border:1px solid rgb(47, 115, 152);">';
          echo '<td><i style="color:grey;">From: </i>'.$row['from_icon'].' <span style="color:'.$row['from_role_color'].';">'.$row['from_username'].' ('.$row['from_role_name'].')</span></td>';
        echo '</tr>';
        echo '<tr style="text-align:left; border:1px solid rgb(47, 115, 152);">';
          echo '<td><i style="color:grey;">Message: </i><br>'.$row['msg_message'].'</td>';
        echo '</tr>';
      }
    echo '</table>';
echo '</div>';

// set the date of being read and viewed now only if it hasn't been read
//echo "msg_read_date: ".$msg_read_date."<br>";
if ($msg_read_date < date('2020-01-01 00:00:00')) {
  $sql = "
          UPDATE messages
          SET msg_read_date = TIMESTAMP(CURRENT_TIMESTAMP)
          WHERE msg_id = $msg_id;
  ";
  $dbh = new Dbh();
  $stmt = $dbh->connect()->query($sql);
}




//header("Location: ../pages/messages.php");
//exit();
