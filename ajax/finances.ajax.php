<?php
include '../includes/autoloader.inc.php';
include '../includes/function_library.inc.php';

// variables that we will always get
$selected_id;
if (isset($_GET['selected_id'])) {
  $selected_id = $_GET['selected_id'];
}
$update_type = $_GET['update_type'];
$form_type = $_GET['form_type'];

$table = '';
$table_id = '';
if ($form_type == 'Expense'){
  $table = 'finance_expenses';
  $table_id = 'fe_id';
} elseif ($form_type == 'Income') {
  $table = 'finance_incomes';
  $table_id = 'fi_id';
} elseif ($form_type == 'Passive') {
  $table = 'passive_incomes';
  $table_id = 'pi_id';
} elseif ($form_type == 'Bill') {
  $table = 'current_bills';
  $table_id = 'bill_id';
} elseif ($form_type == 'Budget') {
  $table = 'budgets';
  $table_id = 'bud_id';
}

// here we check these variables
if ($update_type == 'Delete') {
  // get form type so we know the table:
  // find selected id in table, then make is_active equal zero
  $sql = "UPDATE $table SET is_active = 0 WHERE $table_id = $selected_id;";
  $dbh = new Dbh();
  $stmt = $dbh->connect()->query($sql);

} elseif ($update_type == 'Update') {

  if ($form_type == 'Expense'){
    $company = $_GET['company'];
    $name = $_GET['name'];
    $category = $_GET['category'];
    $amount = $_GET['amount'];
    $date = $_GET['date'];
    $notes = $_GET['notes'];

    $sql = "UPDATE $table
            SET fe_company='$company', fe_name='$name', id_category='$category', fe_amount=$amount,
            fe_date='$date', fe_notes='$notes'
            WHERE $table_id = $selected_id;
    ";
    //echo $sql;
    if ($conn->query($sql) === TRUE) {
      //echo "New record created successfully";
    } else {
    //  echo "Error: " . $sql . "<br>" . $conn->error;
    }

  } elseif ($form_type == 'Income') {
    $company = $_GET['company'];
    $name = $_GET['name'];
    $category = $_GET['category'];
    $amount = $_GET['amount'];
    $date = $_GET['date'];
    $notes = $_GET['notes'];

    $sql = "UPDATE $table
    SET fi_company='$company', fi_name='$name', fi_amount=$amount,
    fi_date='$date', fi_notes='$notes'
            WHERE $table_id = $selected_id;
    ";
    if ($conn->query($sql) === TRUE) {
      //echo "New record created successfully";
    } else {
    //  echo "Error: " . $sql . "<br>" . $conn->error;
    }

  } elseif ($form_type == 'Passive') {

  } elseif ($form_type == 'Bill') {

  } elseif ($form_type == 'Budget') {

  }

} elseif ($update_type == 'Insert') {
  // $sql = "INSERT INTO finance_expenses $column_names_string";\
    if ($form_type == 'Expense'){
      $sql = "INSERT INTO $table (fe_company, fe_name, id_category, fe_amount, fe_date, fe_notes)
              VALUES ('".$_GET["company"]."', '".$_GET["name"]."', '".$_GET["category"]."', ".$_GET['amount'].", '".$_GET["date"]."', '".$_GET["notes"]."');
      ";
      if ($conn->query($sql) === TRUE) {
        //echo "New record created successfully";
      } else {
      //  echo "Error: " . $sql . "<br>" . $conn->error;
      }

    } elseif ($form_type == 'Income') {
      $sql = "INSERT INTO $table (fi_company, fi_name, fi_amount, fi_date, fi_notes)
              VALUES ('".$_GET["company"]."', '".$_GET["name"]."', ".$_GET['amount'].", '".$_GET["date"]."', '".$_GET["notes"]."');
      ";
      if ($conn->query($sql) === TRUE) {
        //echo "New record created successfully";
      } else {
      //  echo "Error: " . $sql . "<br>" . $conn->error;
      }

    } elseif ($form_type == 'Passive') {
      //$sql = "INSERT INTO passive_incomes SET is_active = 0;";
    } elseif ($form_type == 'Bill') {
      //$sql = "INSERT INTO current_bills SET is_active = 0;";
    } elseif ($form_type == 'Budget') {
      //$sql = "INSERT INTO budgets SET is_active = 0;";
    }

  }


header("Location: ../pages/finances.php");
exit();
