<?php
include '../includes/autoloader.inc.php';
include '../includes/function_library.inc.php';

// variables that we will always get
$user_id = $_GET['user_id'];
$icon_file = $_GET['icon_file'];

$sql = "
        UPDATE users
        SET user_icon = '".$icon_file."'
        WHERE user_id = ".$user_id.";
";
echo $sql .'<br>';
$dbh = new Dbh();
$stmt = $dbh->connect()->query($sql);

if ($conn->query($sql) === TRUE) {
  echo 'SUCCESS: UPDATED icon';
} else {
  echo 'ERROR: DID NOT UPDATE icon';
}


header("Location: ../pages/profile.php");
exit();
