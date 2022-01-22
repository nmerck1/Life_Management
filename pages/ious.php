<?php
//declare(strict_types = 1);
include '../includes/autoloader.inc.php';
include '../includes/function_library.inc.php';

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../pages/login.php");
    exit;
}

$loggedin = $_SESSION['loggedin'];
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];
$id_role = $_SESSION['id_role'];

// check messages on every page
$messages = library_get_num_notifications($user_id);

// Prepare a select statement
//echo "user_id: ". $user_id."<br>";
//echo "id_role: ". $id_role."<br>";
$sql = "
    SELECT *
    FROM users
    WHERE user_id = '".$user_id."'
    AND is_active = 1
";
//echo $sql;
$dbh = new Dbh();
$stmt = $dbh->connect()->query($sql);
//echo $sql;
// should only populate one row of data
while ($row = $stmt->fetch()) {
  $user_name = $row['user_name'];
  $user_fname = $row['user_fname'];
  $user_lname = $row['user_lname'];
  $pass_word = $row['pass_word'];
  //echo "user_fname: ".$user_fname."<br>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php
    $header = new Header();
    $header->show_header();
  ?>
</head>
<body>


<?php
  //use Style\Navbar;
  $navbar = new Navbar();
  $navbar->show_header_nav($loggedin, $user_fname, $id_role, $messages);

  $finance_nav = new FinanceNavbar();
  $finance_nav->show_header_nav();
?>

      <?php

        // this makes it more modular and we can add in simple parameters to change the query
        // $paid_off: this is whether or not we want to show the paid off table
        // $current_user_owes: this is whether or not we are filtering the current user owes or is owed
        // $editable: true for if it is not paid off and if created_by is this user
        // $insertable: true if not paid off
        function get_ious_table($user_id, $current_user_owes = false, $paid_off = false){
          $editable = false;  // true for if it is not paid off and if created_by is this user
          $insertable = false;  // true if not paid off

          $loaner = '=';
          $debtor = '!=';
          $color = 'white';
          $form_type = 'Loan';
          // check to see if this is owed by current user or not:
          if ($current_user_owes == true) {
            $loaner = '!=';
            $debtor = '=';
            $color = 'red';
            $form_type = 'Debt';
          }

          $filter_paid_off = 0;
          // check if we want to see paid off or not:
          if ($paid_off == true) {
            $filter_paid_off = 1;
          }
          // start the sql here for owed_to_you_build_table
          $sql = "
            SELECT i.iou_id,
                i.iou_reason,
                i.iou_loaner_id,
                i.iou_debtor_id,
                i.iou_amount_owed,
                i.iou_amount_paid,
                i.iou_owe_date,
                i.iou_created_by,
                i.iou_updated_date,
                i.iou_is_paid_off,
                i.iou_paid_off_date,
                i.iou_is_active,

                loaners.user_name AS 'loaner_user_name',
                loaners.user_fname AS 'loaner_user_fname',
                loaners.user_lname AS 'loaner_user_lname',

                debtors.user_name AS 'debtor_user_name',
                debtors.user_fname AS 'debtor_user_fname',
                debtors.user_lname AS 'debtor_user_lname'

            FROM ious i
            LEFT JOIN users loaners ON i.iou_loaner_id = loaners.user_id
            LEFT JOIN users debtors ON i.iou_debtor_id = debtors.user_id

            WHERE i.iou_is_active = 1
            AND i.iou_is_paid_off = ".$filter_paid_off."
            AND i.iou_loaner_id ".$loaner." '".$user_id."'
            AND i.iou_debtor_id ".$debtor." '".$user_id."'

            ORDER BY i.iou_owe_date DESC;
          ";
          // loaners.user_name AS 'loaner_user_name',      # this is the current user's info who is the loaner
          // debtors.user_name AS 'debtor_user_name',      # this is the debtor's info of the current user
          //echo $sql;
          $dbh = new Dbh();
          $stmt = $dbh->connect()->query($sql);
          echo '<table class="table table-dark" style="background-color:#3a5774; text-align:center;">';
          echo '<tr>';
            echo '<th>Reason</th>';
            if ($current_user_owes == true) {
              echo '<th>Loaner</th>';
            } else {
              echo '<th>Debtor</th>';
            }
            if ($paid_off == false) {
              echo '<th>Owed</th>'; //echo '<th>Amount Owed</th>';
            }
            echo '<th>Paid</th>'; // echo '<th>Amount Paid</th>';
            if ($paid_off == false) {
              echo '<th>Left</th>'; // echo '<th>Amount Left</th>';
            }
            echo '<th>Owe Date</th>';
            if ($paid_off == true) {
              echo '<th>Paid Off Date</th>';  // this is only visible in the paid off tables
            }
            echo '<th style="background-color: rgb(33, 37, 46);">';
            if ($paid_off == false) {
              echo '<a href="../includes/ious.inc.php?form_type='.$form_type.'&user_id='.$user_id.'"><p class="bi-plus-circle" style="color:white;"></p></a>';
            }
            echo '</th>';
          echo '</tr>';
            $total_owed_amount = 0;
            $total_paid_amount = 0;
            $total_left_amount = 0;
            while ($row = $stmt->fetch()) {
              echo '<tr>';
                echo '<td style="background:rgb(25, 29, 32); color:grey;">' .$row['iou_reason']. '</td>';
                if ($current_user_owes == true) {
                  echo '<td style="background:rgb(25, 29, 32);">' .$row['loaner_user_name']. '</td>';
                } else {
                  echo '<td style="background:rgb(25, 29, 32);">' .$row['debtor_user_name']. '</td>';
                }
                if ($paid_off == false) {
                  echo '<td style="color:'.$color.'; text-align:right; background:rgb(25, 29, 32);">' .number_format((float)$row['iou_amount_owed'], 2). '</td>';
                }
                echo '<td style="color:green; text-align:right; background:rgb(25, 29, 32);">' .number_format((float)$row['iou_amount_paid'], 2). '</td>';
                if ($paid_off == false) {
                  $amount_left = ($row['iou_amount_owed'] - $row['iou_amount_paid']);
                  echo '<td style="color:'.$color.'; text-align:right; background:rgb(25, 29, 32);">' .number_format($amount_left, 2). '</td>';
                }
                $date_string1 = strtotime($row['iou_owe_date']);
                echo '<td style="background:rgb(25, 29, 32); color:grey;">' .date('M d, Y', $date_string1). '</td>';
                if ($row['iou_is_paid_off'] == 1) {
                  $date_string2 = strtotime($row['iou_paid_off_date']); // only visible when paid off is equal to true or 1
                  echo '<td style="background:rgb(25, 29, 32); color:grey;">' .date('M d, Y', $date_string2). '</td>';
                }

                // below options/actions are only visible when the created by is the current user
                echo '<td style="background:rgb(33, 37, 46);">';
                  if ($row['iou_created_by'] == $user_id && $row['iou_is_paid_off'] == 0) {
                    echo '<span>'; //style="display:flex;"
                      echo '<a href="../includes/ious.inc.php?selected_id='.$row['iou_id'].'&update_type=Update&form_type='.$form_type.'&user_id='.$user_id.'"><p class="bi-pencil-fill" style="color:white;"></p></a>';
                      echo '<a href="../ajax/ious.ajax.php?selected_id='.$row['iou_id'].'&update_type=Delete&form_type='.$form_type.'&user_id='.$user_id.'"><p class="bi-trash-fill" style="color:white;"></p></a>';
                    echo '</span>';
                  }
                echo '</td>';
              echo '</tr>';
              // get variables for owed and paid:
              $total_owed_amount += (float)$row['iou_amount_owed'];
              $total_paid_amount += (float)$row['iou_amount_paid'];
              if ($paid_off == false) {
                $total_left_amount += $amount_left;
              }
            }
            echo '<tr>';
              echo '<td colspan=2 style="text-align:left; background-color:rgb(33, 37, 46);">Totals:</td>';
              if ($paid_off == false) {
                echo '<td style="text-align:right; background-color:rgb(33, 37, 46);">$'.number_format($total_owed_amount, 2).'</td>';
              }
              echo '<td style="text-align:right; background-color:rgb(33, 37, 46);">$'.number_format($total_paid_amount, 2).'</td>';
              if ($paid_off == false) {
                echo '<td style="text-align:right; background-color:rgb(33, 37, 46);">$'.number_format($total_left_amount, 2).'</td>';
              }
              echo '<td colspan=3 style="background:rgb(33, 37, 46);"></td>';
            echo '</tr>';
          echo '</table>';
        }

        // start the outer table
        echo '<div class="container">';
          echo '<h1 style="text-align:center;">IOUs</h1>';
          echo '<i style="color:grey;">';
            echo '(This is where you can manage your loans to people.
                    You can also view the debts that have been paid off to you.
                    It will also show you what debts have been paid off both by you and your previous debtors.)';
          echo '</i>';

          echo '<div>'; // div for owed to you
            echo '<p style="text-align:center; background: rgb(33, 37, 46); border-right:2px solid rgb(33, 37, 46); border-top:2px solid rgb(33, 37, 46);">Your Loans</p>';

            get_ious_table($user_id, false, false);

          echo '</div>';

          echo '<div>'; // div for you owe
            echo '<p style="text-align:center; background: rgb(33, 37, 46); border-right:2px solid rgb(33, 37, 46); border-top:2px solid rgb(33, 37, 46);">Your Debts</p>';

            get_ious_table($user_id, true, false);

          echo '</div>';

          echo '<br>';
          echo '<br>';

          echo '<div>'; // div for your paid loans
            echo '<p style="text-align:center; background: rgb(33, 37, 46); border-right:2px solid rgb(33, 37, 46); border-top:2px solid rgb(33, 37, 46);">Your PAID Loans</p>';

            get_ious_table($user_id, false, true);

          echo '</div>';

          echo '<div>'; // div for your paid debts
            echo '<p style="text-align:center; background: rgb(33, 37, 46); border-right:2px solid rgb(33, 37, 46); border-top:2px solid rgb(33, 37, 46);">Your PAID Debts</p>';

            get_ious_table($user_id, true, true);

          echo '</div>';

        echo '</div>';  // end main div
      ?>

<?php
  $footer = new Footer();
  $footer->show_footer();
?>

</body>
</html>
