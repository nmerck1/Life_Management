<?php
include '../includes/autoloader.inc.php';
include '../includes/function_library.inc.php';

// variables that we will always get
$selected_id;
if (isset($_GET['selected_id'])) {
  $selected_id = $_GET['selected_id'];
  echo "selected_id: ".$selected_id."<br>";
}
$update_type = $_GET['update_type'];
$form_type = $_GET['form_type'];
$user_id = $_GET['user_id'];

$reason;
if (isset($_GET['reason'])) {
  $reason = $_GET['reason'];
}

$amount_owed;
if (isset($_GET['amount_owed'])) {
  $amount_owed = $_GET['amount_owed'];
}

$amount_paid;
$update_paid = false;
if (isset($_GET['amount_paid'])) {
  $amount_paid = $_GET['amount_paid'];
  $update_paid = true;
}

$user;
if (isset($_GET['user'])) {
  $user = $_GET['user'];
}

$owe_date = null;
if (isset($_GET['owe_date'])) {
  $owe_date = $_GET['owe_date'];
}

$loaner_id;
$debtor_id;
// check for form type so we can set the proper loaner and debtors
if ($form_type == 'Loan'){
  $loaner_id = $user_id;
  if (isset($_GET['user'])) {
    $debtor_id = $user;
  }
} elseif ($form_type == 'Debt'){
  if (isset($_GET['user'])) {
    $loaner_id = $user;
  }
  $debtor_id = $user_id;
}

// here we check these variables
if ($update_type == 'Delete') {
  // get form type so we know the table:
  // find selected id in table, then make is_active equal zero
  $sql = "
          UPDATE ious
          SET iou_is_active = 0
          WHERE iou_id = ".$selected_id.";
  ";
  $dbh = new Dbh();
  $stmt = $dbh->connect()->query($sql);
  echo $sql;
  if ($conn->query($sql) === TRUE) {
    echo "Record deleted successfully";
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }

} elseif ($update_type == 'Update') {
    // check if it is paid off:
    $paid_off = 0;
    $paid_off_date = '1970-01-01';
    if ($amount_paid >= $amount_owed) {
      $paid_off = 1;
      $paid_off_date = date('Y-m-d');
      $amount_paid = $amount_owed;
    }
    $paid_sql_string = '';
    if ($update_paid == true) {
      $paid_sql_string = "iou_amount_paid = ".$amount_paid;
    }

    $sql = "
            UPDATE ious
            SET iou_reason = '".$reason."',
                iou_amount_owed = ".$amount_owed.",
                ".$paid_sql_string.",
                iou_loaner_id = ".$loaner_id.",
                iou_debtor_id = ".$debtor_id.",
                iou_updated_date = CURRENT_TIMESTAMP,
                iou_is_paid_off = ".$paid_off.",
                iou_paid_off_date = '".$paid_off_date."'
            WHERE iou_id = ".$selected_id.";
    ";
    echo $sql;
    if ($conn->query($sql) === TRUE) {
      echo "Record updated successfully";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
} elseif ($update_type == 'Insert') {
  $sql = "";
  if ($owe_date == null) {
    $sql = "
            INSERT INTO ious (iou_reason, iou_loaner_id, iou_debtor_id, iou_amount_owed, iou_created_by)
            VALUES ('".$reason."', '".$loaner_id."', '".$debtor_id."', ".$amount_owed.", '".$user_id."');
    ";
  } else {
    $sql = "
            INSERT INTO ious (iou_reason, iou_loaner_id, iou_debtor_id, iou_amount_owed, iou_owe_date, iou_created_by)
            VALUES ('".$reason."', '".$loaner_id."', '".$debtor_id."', ".$amount_owed.", '".$owe_date."', '".$user_id."');
    ";
  }

  echo $sql. "<br>";
  if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
  } else {
    echo 'ERROR: DID NOT INSERT';
  }

} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

header("Location: ../pages/ious.php");
header("Location: ../pages/ious.php");
exit();
