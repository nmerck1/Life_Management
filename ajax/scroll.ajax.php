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

$cat_id = 0;
if (isset($_GET['cat_id'])) {
  $cat_id = $_GET['cat_id'];
}

$comp_id = 0;
if (isset($_GET['comp_id'])) {
  $comp_id = $_GET['comp_id'];
}

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
} elseif ($table_scroll == 'DetailedCat') {
  if ($action == 'SelectCategoryTable') {   // In this specific case, we are going to use current_num as the cat_id so I don't have to update a bunch of GETs...
      library_detailed_category_spending_table($user_id, $date_search, $current_num, $action, $current_num, $show_per_page);
  } else {  // elseif ($action == 'ScrollCategoryTable')
      library_detailed_category_spending_table($user_id, $date_search, $cat_id, $action, $current_num, $show_per_page);
  }
} elseif ($table_scroll == 'DetailedComp') {
  if ($action == 'SelectCompanyTable') {   // In this specific case, we are going to use current_num as the cat_id so I don't have to update a bunch of GETs...
      library_detailed_company_spending_table($user_id, $date_search, $current_num, $action, $current_num, $show_per_page);
  } else {  // elseif ($action == 'ScrollCategoryTable')
      library_detailed_company_spending_table($user_id, $date_search, $comp_id, $action, $current_num, $show_per_page);
  }
}


//header("Location: ../pages/finances.php");
//exit();
