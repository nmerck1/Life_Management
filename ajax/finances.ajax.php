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
$user_id = $_GET['user_id'];

//echo 'SENT TO AJAX...';

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

// here we are going to check if company name is new so we can insert new company name into the database
if ($_GET['company']) {
  $company = $_GET['company'];
  $sql = "
          SELECT *
          FROM companies
          WHERE comp_name = '".$company."'
          AND is_active = 1
  ";
  $dbh = new Dbh();
  $stmt = $dbh->connect()->query($sql);

  if ($result = mysqli_query($conn, $sql)) {
    // Return the number of rows in result set
    $num_rows = mysqli_num_rows( $result );
    //echo "num_rows: ".$num_rows."<br>";
    if ($num_rows == 0) {
      // create new company here
      $sql_insert = "
            INSERT INTO companies (comp_name)
              VALUES ('".$company."');
      ";
      //echo $sql_insert;
      if ($conn->query($sql_insert)) {
          echo "Added new company record!";
      }
    }
  }
}

// here we check these variables
if ($update_type == 'Delete') {
  // we need to make a delete exception for budgets since there are issues when we keep them on is_active: 0
  if ($form_type == 'Budget') {
    $sql = "DELETE FROM $table
            WHERE $table_id = $selected_id
            AND id_user = $user_id;
    ";
    $dbh = new Dbh();
    $stmt = $dbh->connect()->query($sql);
  } else {
    // get form type so we know the table:
    // find selected id in table, then make is_active equal zero
    $sql = "UPDATE $table
            SET is_active = 0
            WHERE $table_id = $selected_id
            AND id_user = $user_id;
    ";
    $dbh = new Dbh();
    $stmt = $dbh->connect()->query($sql);
  }

} elseif ($update_type == 'Update') {

  if ($form_type == 'Expense'){
    $company = $_GET['company'];
    if ($company == "Other") {
      $company = $_GET['new_company_name'];
    }
    $name = $_GET['name'];
    $category = $_GET['category'];
    $amount = $_GET['amount'];
    $date = $_GET['date'];
    $notes = $_GET['notes'];

    $sql = "UPDATE $table
            SET fe_company='$company', fe_name='$name', id_category='$category', fe_amount=$amount,
            fe_date='$date', fe_notes='$notes'
            WHERE $table_id = $selected_id
            AND id_user = $user_id;
    ";
    //echo $sql;
    if ($conn->query($sql) === TRUE) {
      echo "New expense record created successfully";
        if ($_GET['company'] == "Other") {
          $sql_insert = "
                INSERT INTO companies (comp_name)
                  VALUES ('".$company."');
          ";
          //echo $sql_insert;
          if ($conn->query($sql_insert)) {
            echo "Added new company record!";
            // we want to check the user id and then give a notification to an admin for a specific case of Other company selection
            if ($user_id != 1 && $_GET["company"] == 'Other') {
              // set notification for admins
              $message = ' User ID: '.$user_id.' added a new company.<br> ';
              $message .= ' Company Name: '.$company.'.';
              $message .= '<br><br>';

              $sql = "
                      INSERT INTO notifications (n_subject, n_message, n_type, n_from_user, n_to_user)
                      VALUES ('New Company', '".$message."', 'Message', '".$user_id."', '1');
              ";
              //echo $sql. "<br>";
              if ($conn->query($sql) === TRUE) {
                echo "New record created successfully";
              } else {
                echo 'ERROR: DID NOT INSERT';
              }
            }
          } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
          }
        } else {
          echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

  } elseif ($form_type == 'Income') {
    $company = $_GET['company'];
    if ($company == "Other") {
      $company = $_GET['new_company_name'];
    }
    $name = $_GET['name'];
    //$category = $_GET['category'];    // there is no category for incomes, only expenses
    $amount = $_GET['amount'];
    $date = $_GET['date'];
    $notes = $_GET['notes'];

    $sql = "UPDATE $table
            SET fi_company='$company', fi_name='$name', fi_amount=$amount,
            fi_date='$date', fi_notes='$notes'
            WHERE $table_id = $selected_id
            AND id_user = $user_id;
    ";
    if ($conn->query($sql) === TRUE) {
      echo "New income record created successfully";
        if ($_GET['company'] == "Other") {
          $sql_insert = "
                INSERT INTO companies (comp_name)
                  VALUES ('".$company."');
          ";
          //echo $sql_insert;
          if ($conn->query($sql_insert)) {
            echo "Added new company record!";
            // we want to check the user id and then give a notification to an admin for a specific case of Other company selection
            if ($user_id != 1 && $_GET["company"] == 'Other') {
              // set notification for admins
              $message = ' User ID: '.$user_id.' added a new company.<br> ';
              $message .= ' Company Name: '.$company.'.';
              $message .= '<br><br>';

              $sql = "
                      INSERT INTO notifications (n_subject, n_message, n_type, n_from_user, n_to_user)
                      VALUES ('New Company', '".$message."', 'Message', '".$user_id."', '1');
              ";
              //echo $sql. "<br>";
              if ($conn->query($sql) === TRUE) {
                echo "New record created successfully";
              } else {
                echo 'ERROR: DID NOT INSERT';
              }
            }
          } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
          }
        } else {
          echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }

  } elseif ($form_type == 'Notification_Expense') {
    $new_company = $_GET['new_company'];
    echo 'new_company: '.$new_company.'<br>';
    echo 'selected_id: '.$selected_id.'<br>';
    echo 'user_id: '.$user_id.'<br>';

    $sql = "UPDATE finance_expenses
            SET fe_company='$new_company'
            WHERE fe_id = $selected_id
            AND id_user = $user_id;
    ";
    //echo $sql;
    if ($conn->query($sql) === TRUE) {
      echo "New record created successfully";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  } elseif ($form_type == 'Notification_Income') {
    $new_company = $_GET['new_company'];
    echo 'new_company: '.$new_company.'<br>';
    echo 'selected_id: '.$selected_id.'<br>';
    echo 'user_id: '.$user_id.'<br>';

    $sql = "UPDATE finance_incomes
            SET fi_company='$new_company'
            WHERE fi_id = $selected_id
            AND id_user = $user_id;
    ";
    //echo $sql;
    if ($conn->query($sql) === TRUE) {
      echo "New record created successfully";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  } elseif ($form_type == 'Bill') {
    $name = $_GET['name'];
    $amount = $_GET['amount'];
    //$freq = $_GET['freq'];

    $sql = "UPDATE $table
            SET bill_name='$name', bill_amount=$amount, bill_freq='M'
            WHERE $table_id = $selected_id
            AND id_user = $user_id;
    ";
    if ($conn->query($sql) === TRUE) {
      //echo "New record created successfully";
    } else {
    //  echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // select amount from latest bill log and see if it is different, if so, add new record
    /*
    $sql = "
            SELECT
              bl.bl_id_bill,
              MAX(bl_valid_date) AS MaxDateTime,
              bl.id_user,
              bl.bl_amount,
              cb.bill_name,
              bl.is_active
            FROM bill_logs bl
            LEFT JOIN current_bills cb ON bl.bl_id_bill = cb.bill_id
            WHERE bl.id_user = '".$user_id."'
            AND cb.bill_name = '".$name."'
            AND bl.is_active = 1;
    ";
    */
    $sql = "
            SELECT bl.*,
                   cb.bill_id,
                   cb.bill_name,
                   cb.bill_freq
            FROM bill_logs bl

            INNER JOIN current_bills cb ON bl.bl_id_bill = cb.bill_id

            INNER JOIN
                (SELECT bl_id,
                        bl_id_bill,
                        bl_amount,
                        MAX(bl_valid_date) AS MaxDateTime
                  FROM bill_logs
                  WHERE is_active = 1
                  GROUP BY bl_id_bill
                ) bl2
            ON bl.bl_valid_date = bl2.MaxDateTime

            LEFT JOIN users u ON bl.id_user = u.user_id

            WHERE cb.bill_freq = 'M'
            AND cb.is_active = 1
            AND u.user_id = ".$user_id."
            AND cb.bill_name = '".$name."'

            GROUP BY bl.bl_id_bill;
    ";
    //echo $sql;

    if ($conn->query($sql)) {
      //echo "selection query is good! <br>";
      $dbh = new Dbh();
      $stmt = $dbh->connect()->query($sql);
      $bill_id = '';
      while ($row = $stmt->fetch()) {
        $bill_id = $row['bl_id_bill'];
        $old_bl_amount = $row['bl_amount'];
      }
      //echo "bill_id: ". $bill_id ."<br>";
      //echo "old_bl_amount: ". $old_bl_amount ."<br>";
      //echo "amount: ". $amount ."<br>";
      // check if amount is different
      if ($old_bl_amount != $amount) {
        echo "amount is different! <br>";
        // because amount is new, we are going to add a new record for most recent valid date
        $sql_insert = "
              INSERT INTO bill_logs (bl_id_bill, bl_amount, id_user)
                VALUES ('".$bill_id."', '".$amount."', '".$user_id."');
        ";
        //echo $sql_insert;
        if ($conn->query($sql_insert)) {
          //echo "New record created successfully";
        } else {
          echo "ERROR: DID NOT INSERT <br>";
        }
      }


      //echo "New record created successfully";
    } else {
    //  echo "Error: " . $sql . "<br>" . $conn->error;
    }





  } elseif ($form_type == 'Budget') {
    //$category = $_GET['category'];
    $amount = $_GET['amount'];

    $sql = "UPDATE $table
            SET bud_amount=$amount
            WHERE $table_id = $selected_id
            AND id_user = $user_id;
    ";
    if ($conn->query($sql) === TRUE) {
      //echo "New record created successfully";
    } else {
    //  echo "Error: " . $sql . "<br>" . $conn->error;
    }
  }

} elseif ($update_type == 'Insert') {
  // $sql = "INSERT INTO finance_expenses $column_names_string";\
    if ($form_type == 'Expense'){
      $company = $_GET["company"];
      if ($_GET["company"] == "Other") {
        $company = $_GET["new_company_name"];
      }
      $sql = "
              INSERT INTO $table (fe_company, fe_name, id_category, fe_amount, fe_date, fe_notes, id_user)
              VALUES ('".$company."', '".$_GET["name"]."', '".$_GET["category"]."', ".$_GET['amount'].", '".$_GET["date"]."', '".$_GET["notes"]."', '".$_GET["user_id"]."');
      ";
      //echo $sql. "<br>";
      if ($conn->query($sql)) {
        echo "New expense record created successfully";
          if ($_GET["company"] == "Other") {
            $sql_insert = "
                  INSERT INTO companies (comp_name)
                    VALUES ('".$company."');
            ";
            echo $sql_insert;
            if ($conn->query($sql_insert)) {
              echo "Added new company record!";
            } else {
              echo "Error: " . $sql . "<br>" . $conn->error;
            }
          } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
          }
          // we want to check the user id and then give a notification to an admin for a specific case of Other company selection
          if ($user_id != 1 && $_GET["company"] == 'Other') {
            // set notification for admins
            $message = ' User ID: '.$user_id.' added a new company.<br> ';
            $message .= ' Company Name: '.$company.'.';
            $message .= '<br><br>';

            $sql = "
                    INSERT INTO notifications (n_subject, n_message, n_type, n_from_user, n_to_user)
                    VALUES ('New Company', '".$message."', 'Message', '".$user_id."', '1');
            ";
            //echo $sql. "<br>";
            if ($conn->query($sql) === TRUE) {
              echo "New record created successfully";
            } else {
              echo 'ERROR: DID NOT INSERT';
            }
          }
      } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
      }

    } elseif ($form_type == 'Income') {
      $company = $_GET["company"];
      if ($_GET["company"] == "Other") {
        $company = $_GET["new_company_name"];
      }
      $sql = "
              INSERT INTO $table (fi_company, fi_name, fi_amount, fi_date, fi_notes, id_user)
              VALUES ('".$company."', '".$_GET["name"]."', ".$_GET['amount'].", '".$_GET["date"]."', '".$_GET["notes"]."', '".$_GET["user_id"]."');
      ";
      //echo $sql. "<br>";
      if ($conn->query($sql)) {
        echo "New income record created successfully";
          if ($_GET["company"] == "Other") {
            $sql_insert = "
                  INSERT INTO companies (comp_name)
                    VALUES ('".$company."');
            ";
            //echo $sql_insert;
            if ($conn->query($sql_insert)) {
              echo "Added new company record!";
            } else {
              echo "Error: " . $sql . "<br>" . $conn->error;
            }
          } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
          }

        // we want to check the user id and then give a notification to an admin for a specific case of Other company selection
        if ($user_id != 1 && $_GET["company"] == 'Other') {
          // set notification for admins
          $message = ' User ID: '.$user_id.' added a new company.<br> ';
          $message .= ' Company Name: '.$company.'.';
          $message .= '<br><br>';

          $sql = "
                  INSERT INTO notifications (n_subject, n_message, n_type, n_from_user, n_to_user)
                  VALUES ('New Company', '".$message."', 'Message', '".$user_id."', '1');
          ";
          //echo $sql. "<br>";
          if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
          } else {
            echo 'ERROR: DID NOT INSERT';
          }
        }

      } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
      }

    } elseif ($form_type == 'Passive') {
      //$sql = "INSERT INTO passive_incomes SET is_active = 0;";


    } elseif ($form_type == 'Bill') {
      $freq = 'M';  // this is the default for now
      $sql = "
              INSERT INTO $table (bill_name, bill_amount, bill_freq, id_user)
              VALUES ('".$_GET["name"]."', ".$_GET['amount'].", '".$freq."', '".$_GET["user_id"]."');
      ";
      echo $sql.'<br>';
      if ($conn->query($sql) === TRUE) {
        echo "New Bill created successfully";
        $last_id = $conn->insert_id;
        // because amount is new, we are going to add a new record for most recent valid date
        $sql_insert = "
              INSERT INTO bill_logs (bl_id_bill, bl_amount, id_user)
                VALUES ('".$last_id."', ".$_GET['amount'].", '".$_GET["user_id"]."');
        ";
        //echo $sql_insert;
        if ($conn->query($sql_insert)) {
          echo "Added first bill log attachment for current date";
        } else {
          echo "Error: " . $sql . "<br>" . $conn->error;
        }
      } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
      }


    } elseif ($form_type == 'Budget') {
      $freq = 'M';  // this is the default for now
      $sql = "
              INSERT INTO $table (id_category, bud_amount, bud_notes, bud_freq, id_user)
              VALUES ('".$_GET["category"]."', ".$_GET['amount'].", '', '".$freq."', '".$_GET["user_id"]."');
      ";
      if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
      } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
      }
    }

} elseif ($update_type == 'Next') {

} elseif ($update_type == 'Previous') {

}



header("Location: ../pages/manage.php");
exit();
