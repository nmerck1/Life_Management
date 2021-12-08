<?php
include '../includes/autoloader.inc.php';
include '../includes/function_library.inc.php';

// variables that we will always get
$user_id = $_GET['user_id'];
$new_password = $_GET['new_password'];


$sql = "
        UPDATE users
        SET pass_word = '".$new_password."'
        WHERE user_id = ".$user_id.";
";
$dbh = new Dbh();
$stmt = $dbh->connect()->query($sql);

if ($conn->query($sql) === TRUE) {
  echo 'SUCCESS: UPDATED password';
} else {
  echo 'ERROR: DID NOT UPDATE password';
}


header("Location: ../pages/profile.php");
exit();
