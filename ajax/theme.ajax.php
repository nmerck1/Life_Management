<?php
include '../includes/autoloader.inc.php';
include '../includes/function_library.inc.php';

// variables that we will always get
$user_id = $_GET['user_id'];
$theme_file = $_GET['theme_file'];

$sql = "
        UPDATE users
        SET user_theme = '".$theme_file."'
        WHERE user_id = ".$user_id.";
";
echo $sql .'<br>';
$dbh = new Dbh();
$stmt = $dbh->connect()->query($sql);

if ($conn->query($sql) === TRUE) {
  echo 'SUCCESS: UPDATED theme';
} else {
  echo 'ERROR: DID NOT UPDATE theme';
}


header("Location: ../pages/profile.php");
exit();
