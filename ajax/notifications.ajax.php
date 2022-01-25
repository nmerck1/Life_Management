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

echo '<div class="div_element_block">';

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
      while ($row = $stmt->fetch()) {
        $msg_read_date = $row['n_read_date'];
          echo '<td><i><h4>"'.$row['n_subject'].'"</h4></i></td>';
          echo '<td class="message_block"><i style="color:grey;">'.$row['n_message'].'</i></td>';
      }
echo '</div>';

echo '<br>';
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
