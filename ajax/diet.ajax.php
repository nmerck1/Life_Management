<?php
include '../includes/autoloader.inc.php';
include '../includes/function_library.inc.php';

// variables that we will always get
$selected_id;
if (isset($_GET['selected_id'])) {
  $selected_id = $_GET['selected_id'];
}
$update_type = $_GET['update_type'];
$user_id = $_GET['user_id'];

// here we check these variables
if ($update_type == 'Delete') {
  // get form type so we know the table:
  // find selected id in table, then make is_active equal zero
  $sql = "UPDATE food_logs
          SET is_active = 0
          WHERE fl_id = $selected_id
          AND id_user = $user_id;
  ";
  $dbh = new Dbh();
  $stmt = $dbh->connect()->query($sql);

} elseif ($update_type == 'Update') {

    $name = $_GET['name'];
    $food_category = $_GET['food_category'];
    $amount = $_GET['amount'];
    $measurement = $_GET['measurement'];
    $quantity = $_GET['quantity'];
    $calories = $_GET['calories'];
    $carbs = $_GET['carbs'];
    $protein = $_GET['protein'];
    $fat = $_GET['fat'];
    $meal_time = $_GET['meal_time'];
    $log_date = $_GET['log_date'];
    $notes = $_GET['notes'];

    $sql = "UPDATE food_logs
            SET fl_name = '$name',
            id_food_category = '$food_category',
            fl_amount = $amount,
            id_mea = $measurement,
            fl_quantity = $quantity,
            fl_calories = $calories,
            fl_carbs = $carbs,
            fl_protein = $protein,
            fl_fat = $fat,
            fl_meal_time = '$meal_time',
            fl_log_date = '$log_date',
            fl_notes = '$notes'
            WHERE fl_id = $selected_id
            AND id_user = $user_id;
    ";
    //echo $sql;
    if ($conn->query($sql) === TRUE) {
      echo 'SUCCESS: UPDATED RECORD';
    } else {
      echo 'ERROR: DID NOT UPDATE';
    }


} elseif ($update_type == 'Insert') {

      $name = $_GET['name'];
      $food_category = $_GET['food_category'];
      $amount = $_GET['amount'];
      $measurement = $_GET['measurement'];
      $quantity = $_GET['quantity'];
      $calories = $_GET['calories'];
      $carbs = $_GET['carbs'];
      $protein = $_GET['protein'];
      $fat = $_GET['fat'];
      $meal_time = $_GET['meal_time'];
      $log_date = $_GET['log_date'];
      $notes = $_GET['notes'];

      $sql = "
              INSERT INTO food_logs (fl_name, id_food_category, fl_amount, id_mea, fl_quantity,
                                    fl_calories, fl_carbs, fl_protein, fl_fat, fl_meal_time, fl_log_date, fl_notes, id_user)

              VALUES ('".$name."', '".$food_category."', '".$amount."', ".$measurement.", '".$quantity."',
              '".$calories."', '".$carbs."', '".$protein."', '".$fat."', '".$meal_time."', '".$log_date."', '".$notes."', '".$user_id."');
      ";
      echo $sql. "<br>";
      if ($conn->query($sql)) {
        echo 'SUCCESS: INSERTED NEW RECORD';
        // we want to check the user id and then give a notification to an admin for a specific case of Other company selection
        if ($user_id != 1 && ($food_category == 14 || $measurement == 22) ) {
          // set notification for admins
          $subject = 'New Food Category or Measurement';
          $message = 'User ID: '.$user_id.' is requesting a new food_category and/or measurement record be added. Check notes for new name.';

          $sql = "
                  INSERT INTO notifications (n_subject, n_message, n_type, n_from_user, n_to_user)
                  VALUES ('".$subject."', '".$message."', 'Request', '".$user_id."', '1');
          ";
          //echo $sql. "<br>";
          if ($conn->query($sql) === TRUE) {
            echo 'SUCCESS: INSERTED NEW Notification';
          } else {
            echo 'ERROR: DID NOT INSERT Notification';
          }
        }
      } else {
        echo 'ERROR: DID NOT INSERT';
      }

}elseif ($update_type == 'Copy') {

  $name = $_GET['name'];
  $food_category = $_GET['food_category'];
  $amount = $_GET['amount'];
  $measurement = $_GET['measurement'];
  $quantity = $_GET['quantity'];
  $calories = $_GET['calories'];
  $carbs = $_GET['carbs'];
  $protein = $_GET['protein'];
  $fat = $_GET['fat'];
  $meal_time = $_GET['meal_time'];
  $log_date = $_GET['log_date'];
  $notes = $_GET['notes'];

  $sql = "
          INSERT INTO food_logs (fl_name, id_food_category, fl_amount, id_mea, fl_quantity,
                                fl_calories, fl_carbs, fl_protein, fl_fat, fl_meal_time, fl_log_date, fl_notes, id_user)

          VALUES ('".$name."', '".$food_category."', '".$amount."', ".$measurement.", '".$quantity."',
          '".$calories."', '".$carbs."', '".$protein."', '".$fat."', '".$meal_time."', '".$log_date."', '".$notes."', '".$user_id."');
  ";
  echo $sql. "<br>";
  if ($conn->query($sql)) {
    echo 'SUCCESS: INSERTED NEW RECORD';
    // we want to check the user id and then give a notification to an admin for a specific case of Other company selection
    if ($user_id != 1 && ($food_category == 14 || $measurement == 22) ) {
      // set notification for admins
      $subject = 'New Food Category or Measurement';
      $message = 'User ID: '.$user_id.' is requesting a new food_category and/or measurement record be added. Check notes for new name.';

      $sql = "
              INSERT INTO notifications (n_subject, n_message, n_type, n_from_user, n_to_user)
              VALUES ('".$subject."', '".$message."', 'Request', '".$user_id."', '1');
      ";
      //echo $sql. "<br>";
      if ($conn->query($sql) === TRUE) {
        echo 'SUCCESS: INSERTED NEW Notification';
      } else {
        echo 'ERROR: DID NOT INSERT Notification';
      }
    }
  } else {
    echo 'ERROR: DID NOT INSERT';
  }
}


header("Location: ../pages/diet.php");
header("Location: ../pages/diet.php");
exit();
