<?php
////declare(strict_types = 1);
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
  $user_theme = $row['user_theme'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php
    $header = new Header();
    $header->show_header($user_theme);
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
  <script type="text/javascript">
  	function scroll_loans(next_prev_num){
        // setup the ajax request
    		var xhttp = new XMLHttpRequest();
        // get variables from inputs below:
    		var current_page_num = document.getElementById('current_loan_page');
        var user_id = document.getElementById('user_id');
        var date_search = document.getElementById('date_search');
        var table_scroll = 'Loans'

    		var action = 'Next';
        if (next_prev_num == 0) {
          action = 'Prev';
        }

        var can_scroll = true;
        if (action == 'Prev') {
          if (current_page_num.innerHTML == 1) {
            can_scroll = false;
          }
        }

        if ( can_scroll == true ) {
          // create link to send GET variables through
          var query_string = "../ajax/scroll.ajax.php";
          query_string += "?current_num=" + current_page_num.innerHTML;
          //query_string += "&form_type=" + "Expense";
          query_string += "&user_id=" + user_id.innerHTML;
          query_string += "&action=" + action;
          query_string += "&date_search=" + date_search.innerHTML;
          query_string += "&table_scroll=" + table_scroll;

          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
             document.getElementById("scroll_div_loans").innerHTML = this.responseText;
            }
          };
          xhttp.open("GET", query_string, true);
          xhttp.send();

          // when the data is returned after ajax, it redirects back to inventory
          //window.location = "../pages/finances.php";
        }
  	}
    function scroll_debts(next_prev_num){
        // setup the ajax request
    		var xhttp = new XMLHttpRequest();
        // get variables from inputs below:
    		var current_page_num = document.getElementById('current_debt_page');
        var user_id = document.getElementById('user_id');
        var date_search = document.getElementById('date_search');
        var table_scroll = 'Debts'

    		var action = 'Next';
        if (next_prev_num == 0) {
          action = 'Prev';
        }

        var can_scroll = true;
        if (action == 'Prev') {
          if (current_page_num.innerHTML == 1) {
            can_scroll = false;
          }
        }

        if ( can_scroll == true ) {
          // create link to send GET variables through
          var query_string = "../ajax/scroll.ajax.php";
          query_string += "?current_num=" + current_page_num.innerHTML;
          //query_string += "&form_type=" + "Expense";
          query_string += "&user_id=" + user_id.innerHTML;
          query_string += "&action=" + action;
          query_string += "&date_search=" + date_search.innerHTML;
          query_string += "&table_scroll=" + table_scroll;

          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
             document.getElementById("scroll_div_debts").innerHTML = this.responseText;
            }
          };
          xhttp.open("GET", query_string, true);
          xhttp.send();

          // when the data is returned after ajax, it redirects back to inventory
          //window.location = "../pages/finances.php";
        }
  	}
  </script>
      <?php
        echo '<p id="user_id" style="display:none;" value="'.$user_id.'">'.$user_id.'</p>';
        // this is for looking at previous finance dates in the system
        $date_search = date('Y-m-d');
        if (isset($_POST['date_search'])) {
          $date_search = $_POST['date_search'];
        }

        echo '<p id="date_search" style="display:none;" value="'.$date_search.'">'.$date_search.'</p>';
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
          $color = '';
          $form_type = 'Loan';
          // check to see if this is owed by current user or not:
          if ($current_user_owes == true) {
            $loaner = '!=';
            $debtor = '=';
            $color = 'color:red;';
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

            ORDER BY i.iou_owe_date DESC
            LIMIT 0, 5;
          ";
          // loaners.user_name AS 'loaner_user_name',      # this is the current user's info who is the loaner
          // debtors.user_name AS 'debtor_user_name',      # this is the debtor's info of the current user
          //echo $sql;
          $dbh = new Dbh();
          $stmt = $dbh->connect()->query($sql);
          echo '<table class="table table-dark" style="text-align:center;">';
          echo '<tr>';
            echo '<th>Reason</th>';
            if ($current_user_owes == true) {
              echo '<th>Loaner</th>';
            } else {
              echo '<th>Debtor</th>';
            }
            if ($paid_off == false) {
              echo '<th style="text-align:right;">Owed</th>'; //echo '<th>Amount Owed</th>';
            }
            echo '<th style="text-align:right;">Paid</th>'; // echo '<th>Amount Paid</th>';
            if ($paid_off == false) {
              echo '<th style="text-align:right;">Remaining</th>'; // echo '<th>Amount Left</th>';
            }
            echo '<th>Owe Date</th>';
            if ($paid_off == true) {
              echo '<th>Paid Off Date</th>';  // this is only visible in the paid off tables
            }
            echo '<th class="end_row_options">';
            if ($paid_off == false) {
              echo '<a href="../includes/ious.inc.php?form_type='.$form_type.'&user_id='.$user_id.'"><i class="actions"><p class="bi-plus-circle"></p></i></a>';
            }
            echo '</th>';
          echo '</tr>';
            $total_owed_amount = 0;
            $total_paid_amount = 0;
            $total_left_amount = 0;
            $is_alternate_row = false;
            $add_alternating_class = '';
            while ($row = $stmt->fetch()) {
              echo '<tr>';

              if ($is_alternate_row == false) {
                $add_alternating_class = '';
                $is_alternate_row = true;
              } else {
                $add_alternating_class = 'class="alternating_row"';
                $is_alternate_row = false;
              }

                echo '<td '.$add_alternating_class.' style="color:grey;">' .$row['iou_reason']. '</td>';
                if ($current_user_owes == true) {
                  echo '<td '.$add_alternating_class.'>' .$row['loaner_user_name']. '</td>';
                } else {
                  echo '<td '.$add_alternating_class.'>' .$row['debtor_user_name']. '</td>';
                }
                if ($paid_off == false) {
                  echo '<td '.$add_alternating_class.' style="'.$color.' text-align:right; ">' .number_format((float)$row['iou_amount_owed'], 2). '</td>';
                }
                echo '<td '.$add_alternating_class.' style="color:green; text-align:right;">' .number_format((float)$row['iou_amount_paid'], 2). '</td>';
                if ($paid_off == false) {
                  $amount_left = ($row['iou_amount_owed'] - $row['iou_amount_paid']);
                  echo '<td '.$add_alternating_class.' style="'.$color.' text-align:right;">' .number_format($amount_left, 2). '</td>';
                }
                $date_string1 = strtotime($row['iou_owe_date']);
                echo '<td '.$add_alternating_class.' style="color:grey;">' .date('M d, Y', $date_string1). '</td>';
                if ($row['iou_is_paid_off'] == 1) {
                  $date_string2 = strtotime($row['iou_paid_off_date']); // only visible when paid off is equal to true or 1
                  echo '<td '.$add_alternating_class.' style="color:grey;">' .date('M d, Y', $date_string2). '</td>';
                }

                // below options/actions are only visible when the created by is the current user
                echo '<td class="end_row_options">';
                  if ($row['iou_created_by'] == $user_id && $row['iou_is_paid_off'] == 0) {
                    echo '<span>'; //style="display:flex;"
                      echo '<a href="../includes/ious.inc.php?selected_id='.$row['iou_id'].'&update_type=Update&form_type='.$form_type.'&user_id='.$user_id.'"><i class="actions"><p class="bi-pencil-fill"></p></i></a>';
                      echo '<a href="../ajax/ious.ajax.php?selected_id='.$row['iou_id'].'&update_type=Delete&form_type='.$form_type.'&user_id='.$user_id.'" onclick="return confirm(\'Delete: '.$row['iou_reason'].' '.$form_type.'?\')"><i class="actions"><p class="bi-trash-fill"></p></i></a>';
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
              echo '<td colspan=2 class="end_row_options" style="text-align:left;">Totals:</td>';
              if ($paid_off == false) {
                echo '<td class="end_row_options" style="text-align:right;">$'.number_format($total_owed_amount, 2).'</td>';
              }
              echo '<td class="end_row_options" style="text-align:right;">$'.number_format($total_paid_amount, 2).'</td>';
              if ($paid_off == false) {
                echo '<td class="end_row_options" style="text-align:right;">$'.number_format($total_left_amount, 2).'</td>';
              }
              echo '<td class="end_row_options" colspan=3></td>';
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

          echo '<div class="div_element_block">'; // div for owed to you
            echo '<h4 style="text-align:center;">Your Loans</h4>';
            echo '<p style="width:95%; margin:0px; text-align:center;">';
              echo '<button name="prev_button" onclick="scroll_loans(0);" style="float:left; background:none; border:none; font-size:20px; height:32px;">';
                echo '<i class="actions"><p class="bi-arrow-left-square"></p></i>';
              echo '</button>';
              echo '<button name="next_button" onclick="scroll_loans(1);" style="float:right; background:none; border:none; font-size:20px; height:32px;">';
                echo '<i class="actions"><p class="bi-arrow-right-square"></p></i>';
              echo '</button>';
            echo '</p>';

            echo '<div id="scroll_div_loans">';
              echo '<p id="current_loan_page" style="text-align:center; display:none;" value="1">1</p>'; //style="display:none;"
              echo '<p id="page_show" style="text-align:center; color:grey;">(Page 1)</p>';
              get_ious_table($user_id, false, false);
            echo '</div>';

          echo '</div>';

          echo '<br>';

          echo '<div class="div_element_block">'; // div for you owe
            echo '<h4 style="text-align:center;">Your Debts</h4>';
            echo '<p style="width:95%; margin:0px; text-align:center;">';
              echo '<button name="prev_button" onclick="scroll_debts(0);" style="float:left; background:none; border:none; font-size:20px; height:32px;">';
                echo '<i class="actions"><p class="bi-arrow-left-square"></p></i>';
              echo '</button>';
              echo '<button name="next_button" onclick="scroll_debts(1);" style="float:right; background:none; border:none; font-size:20px; height:32px;">';
                echo '<i class="actions"><p class="bi-arrow-right-square"></p></i>';
              echo '</button>';
            echo '</p>';

            echo '<div id="scroll_div_debts">';
              echo '<p id="current_debt_page" style="text-align:center; display:none;" value="1">1</p>'; //style="display:none;"
              echo '<p id="page_show" style="text-align:center; color:grey;">(Page 1)</p>';
              get_ious_table($user_id, true, false);
              echo '</div>';

          echo '</div>';

          echo '<br>';
          echo '<br>';

          echo '<div class="div_element_block">'; // div for your paid loans
            echo '<h4 style="text-align:center;">Your PAID Loans</h4>';

            get_ious_table($user_id, false, true);

          echo '</div>';

          echo '<br>';

          echo '<div class="div_element_block">'; // div for your paid debts
            echo '<h4 style="text-align:center;">Your PAID Debts</h4>';

            get_ious_table($user_id, true, true);

          echo '</div>';

          echo '<br>';

        echo '</div>';  // end main div
      ?>

<?php
  $footer = new Footer();
  $footer->show_footer();
?>

</body>
</html>
