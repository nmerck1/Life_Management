<?php
// Initialize the session
session_start();
$_SESSION['loggedin'] = false;

header("location: ../pages/login.php");
exit;
