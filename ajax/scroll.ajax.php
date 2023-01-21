<?php
include '../includes/autoloader.inc.php';
include '../includes/function_library.inc.php';

// variables that we will always get
$current_num = $_GET['current_num'];
//$form_type = $_GET['form_type'];
$user_id = $_GET['user_id'];
$action = $_GET['action'];
$date_search = $_GET['date_search'];
$table_scroll = $_GET['table_scroll'];
$show_per_page = $_GET['show_per_page'];

if ($table_scroll == 'Expenses') {
  library_expenses_table($user_id, $action, $current_num, $date_search, $show_per_page);
} elseif ($table_scroll == 'Incomes') {
  library_incomes_table($user_id, $action, $current_num, $date_search, $show_per_page);
} elseif ($table_scroll == 'Yearly') {
  library_yearly_table($user_id, $action, $current_num, $date_search, $show_per_page);
} elseif ($table_scroll == 'Loans') {
  library_ious_table($user_id, $action, $current_num, $date_search, $show_per_page, false, false);
} elseif ($table_scroll == 'Debts') {
  library_ious_table($user_id, $action, $current_num, $date_search, $show_per_page, true, false);
} elseif ($table_scroll == 'Notifications') {
  library_notifications_table($user_id, $action, $current_num, $date_search, $show_per_page, $conn);
} elseif ($table_scroll == 'Monthly') {
  library_monthly_tables($action, $date_search, $user_id);
}

//header("Location: ../pages/finances.php");
//exit();
