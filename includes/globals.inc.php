<?php
date_default_timezone_set('America/New_York');

// "global variables"
$g_servername = "localhost";
//$g_servername = "lifemanagement.me";
$g_username = "root";
$g_password = "";
$g_database = "lifement_life_management";
$g_port = 3306;

$is_server = false;	// remember to change this before pushing to production.

if ($is_server == true) {
	$g_username = "lifement_test";
	$g_password = "poopy";
	$g_database = "lifement_life_management";
	$g_port = 3306;
}
