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

//echo "action: ".$action."<br>";
//echo "current_page_num: ".$current_page_num."<br>";

function show_results_table($user_id, $action, $current_page_num, $date_search){
  // add or subtract the page number depending on the action
  $get_current_page_num = 0;
  if ($action == "Next") { $get_current_page_num = $current_page_num + 1; } else { $get_current_page_num = $current_page_num - 1; }
  echo '<p id="current_page_num" style="text-align:center; display:none;" value="'.$get_current_page_num.'">'.$get_current_page_num.'</p>'; //style="display:none;"
  echo '<p id="page_show" style="text-align:center; color:grey;">(Page '.$get_current_page_num.')</p>';

  //echo "current_page_num: ".$get_current_page_num."<br>";
  $show_num_records_per_page = 5;
  $get_sql_limit_min = ($show_num_records_per_page * $get_current_page_num) - $show_num_records_per_page;
  $get_sql_limit_max = ($show_num_records_per_page * $get_current_page_num);

  $sql = "
      SELECT fe.fe_id,
          fe.fe_company,
          fe.id_category,
          cat.cat_name,
          fe.fe_name,
          fe.fe_amount,
          fe.fe_date
      FROM finance_expenses fe
      LEFT JOIN categories cat ON fe.id_category = cat.cat_id
      LEFT JOIN users u ON fe.id_user = u.user_id
      WHERE fe.is_active = 1
      AND u.user_id = ".$user_id."
      AND MONTH(fe.fe_date)=MONTH('".$date_search."')
      AND YEAR(fe.fe_date)=YEAR('".$date_search."')

      ORDER BY fe.fe_date DESC
      LIMIT ".$get_sql_limit_min.",".$get_sql_limit_max .";
  ";
  $dbh = new Dbh();
  $stmt = $dbh->connect()->query($sql);
  echo '<table class="table table-dark" style="background-color:#3a5774; text-align:center;">';
    echo '<tr>';
      echo '<th>Company</th>';
      //echo '<th>Category</th>';
      echo '<th>Name</th>';
      echo '<th>Date</th>';
      echo '<th style="text-align:right;">Amount</th>';
      echo '<th class="end_row_options">';
        echo '<a href="../includes/finances.inc.php?form_type=Expense&user_id='.$user_id.'"><i class="actions"><p class="bi-plus-circle"></p></i></a>';
      echo '</th>';
    echo '</tr>';
    $total_expenses_amount = 0;
    $total_not_shown_expenses = 0;
    $show_limit = 5;                      // this limit variable is helpful for make next and previous eventually...
    $counter = 1;
    $additional_rows = 0;
    $is_alternate_row = false;
    $add_alternating_class = '';
    while ($row = $stmt->fetch()) {

      if ($counter <= $show_limit){
          echo '<tr>';

          if ($is_alternate_row == false) {
            $add_alternating_class = '';
            $is_alternate_row = true;
          } else {
            $add_alternating_class = 'class="alternating_row"';
            $is_alternate_row = false;
          }
          echo '<td '.$add_alternating_class.' style="color:grey;">' .$row['fe_company']. '</td>';
          //echo '<td style="background:rgb(25, 29, 32); color:grey;">' .$row['cat_name']. '</td>';
          echo '<td '.$add_alternating_class.'>' .$row['fe_name']. '</td>';
          $date_string = strtotime($row['fe_date']);
          echo '<td '.$add_alternating_class.' style="color:grey;">' .date('M, d', $date_string). '</td>';
          echo '<td '.$add_alternating_class.' style="text-align:right;">' .number_format((float)$row['fe_amount'], 2). '</td>';
          echo '<td class="end_row_options">';
            echo '<span>'; //style="display:flex;"
              echo '<a href="../includes/finances.inc.php?selected_id='.$row['fe_id'].'&update_type=Edit&form_type=Expense&user_id='.$user_id.'"><i class="actions"><p class="bi-pencil-fill"></p></i></a>';
              echo '<a href="../ajax/finances.ajax.php?selected_id='.$row['fe_id'].'&update_type=Delete&form_type=Expense&user_id='.$user_id.'"><i class="actions"><p class="bi-trash-fill"></p></i></a>';
            echo '</span>';
          echo '</td>';
        echo '</tr>';
      // get variables for savings:
      $total_expenses_amount += (float)$row['fe_amount'];

      } else {
          //$additional_rows++;
      }
      // always add to the total amount for all the rows
      $total_not_shown_expenses += (float)$row['fe_amount'];
      $counter++;
    }
    echo '<tr>';
      echo '<td colspan=4 class="end_row_options" style="text-align:left;">Total: <p style="float:right;">$'.number_format($total_expenses_amount, 2).'</p></td>';
      //echo '<td style="text-align:right; background-color:rgb(33, 37, 46);">$'.number_format($total_expenses_amount, 2).'</td>';
      echo '<td class="end_row_options"></td>';
    echo '</tr>';
    echo '<tr>';
      if ($additional_rows > 0) {
        echo '<td colspan=4 class="end_row_options" style="text-align:left;"><i>('.$additional_rows.' more rows...)</i> <p style="float:right;">($'.number_format($total_not_shown_expenses, 2).')</p></td>';
        //echo '<td style="background:rgb(33, 37, 46);">($'.number_format($total_not_shown_expenses, 2).')</td>';
        echo '<td class="end_row_options"></td>';
      }
    echo '</tr>';
  echo '</table>';


}

function show_yearly_table($user_id, $action, $current_num, $date_search) {

  if ($action == "Next") { $current_num = $current_num + 1; } else { $current_num = $current_num - 1; }
  echo '<h3 id="current_year_num" style="text-align:center;" value="'.$current_num.'">'.$current_num.'</h3>'; //style="display:none;"

  $new_date_string = $current_num.'-01-01';
  $date_search = date('Y-m-d', strtotime($new_date_string));
  //echo 'date_search: '.$date_search.'<br>';

  echo '<p id="date_search" style="display:none;" value="'.$date_search.'">'.$date_search.'</p>';

  $this_year = date('Y', strtotime($date_search));
  $first_month = date('Y-m-d', strtotime('first day of January'.$this_year));
  $months_of_year = array();
  for ($i = 0; $i < 12; $i++) {
       // echo date('F Y', $month);
       $next_month = strtotime("+".$i." month", strtotime($first_month));
       $show_month = date('M', $next_month);
       //echo "month: ".$show_month."<br>";
       array_push($months_of_year, $show_month);
  }


  echo '<table class="table table-dark" style="text-align:center;">'; // table where rows are incomes, expenses and savings, and columns are months
    echo '<tr>';
      echo '<th></th>';
      echo '<th><i class="bi-plus-square"></i></th>'; // Incomes
      echo '<th><i class="bi-dash-square"></i></th>'; // Expenses
      echo '<th><i class="bi-currency-dollar"></i></th>'; // Savings
      echo '<th><i class="bi-currency-dollar"></i> (+Loans)</th>'; // Savings (If all Loans are paid)
    echo '</tr>';
    //echo '<tr>';
    //echo '<td style="background:rgb(25, 29, 32);">Incomes</td>';
    $income_monthly_totals = array();
    $sql = "
            SELECT SUM(f.fi_amount) AS 'fi_amount',
                   f.fi_date,
                   f.is_active
            FROM finance_incomes f
            LEFT JOIN users u ON f.id_user = u.user_id
            WHERE f.is_active = 1
            AND u.user_id = ".$user_id."
            AND YEAR(f.fi_date)=YEAR('".$date_search."')

            GROUP BY MONTH(f.fi_date)
            ORDER BY f.fi_date ASC;
    ";
    //echo $sql .'<br>';
    $dbh = new Dbh();
    $stmt = $dbh->connect()->query($sql);

    while ($row = $stmt->fetch()) {
      $fi_date = strtotime($row['fi_date']);
      $get_formatted_date = date('M', $fi_date);

      // check if fi_amount is zero or null
      if ($row['fi_amount'] == 0 || $row['fi_amount'] == null) {
        $income_monthly_totals[$get_formatted_date] = 0;
      } else {
        $income_monthly_totals[$get_formatted_date] = $row['fi_amount'];
      }


    }

  //echo '<tr>';
    //echo '<td style="background:rgb(25, 29, 32);">Expenses</td>';
    $expense_monthly_totals = array();
    $sql = "
          SELECT bl.*,
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
                  WHERE DATE(bl_valid_date) <= DATE('2022-01-01')
                  AND is_active = 1
                  GROUP BY bl_id_bill
                ) bl2
            ON bl.bl_valid_date = bl2.MaxDateTime
            LEFT JOIN users u ON bl.id_user = u.user_id
            WHERE bl.is_active = 1
            AND u.user_id = ".$user_id."

            GROUP BY bl.bl_id_bill;
    ";
    $dbh = new Dbh();
    $stmt = $dbh->connect()->query($sql);

    $total_history_bills = 0;
    while ($row = $stmt->fetch()) {
      $total_history_bills += $row['bl_amount'];
    }

    $sql = "
            SELECT SUM(fe.fe_amount) AS 'fe_amount',
                   fe.fe_date,
                   fe.is_active
            FROM finance_expenses fe
            LEFT JOIN users u ON fe.id_user = u.user_id
            WHERE fe.is_active = 1
            AND u.user_id = ".$user_id."
            AND YEAR(fe.fe_date)=YEAR('".$date_search."')

            GROUP BY MONTH(fe.fe_date)
            ORDER BY fe.fe_date ASC;
    ";
    //echo $sql.'<br>';
    $dbh = new Dbh();
    $stmt = $dbh->connect()->query($sql);

    while ($row = $stmt->fetch()) {
      $fe_date = strtotime($row['fe_date']);
      $get_formatted_date = date('M', $fe_date);

      $expense_monthly_totals[$get_formatted_date] = $row['fe_amount'];
    }
  //  get the total savings if all your loans are paid off
  $sql = "
          SELECT SUM(i.iou_amount_owed) AS 'amount_owed',
                 SUM(i.iou_amount_paid) AS 'amount_paid',
                 i.iou_loaner_id,
                 i.iou_is_active
          FROM ious i

          WHERE i.iou_is_active = 1
          AND i.iou_loaner_id = ".$user_id."
          AND YEAR(i.iou_owe_date)=YEAR('".$date_search."');
  ";
  //echo $sql.'<br>';
  $dbh = new Dbh();
  $stmt = $dbh->connect()->query($sql);

  $total_loan_amount_owed = 0;
  //$total_loan_amount_paid = 0;
  while ($row = $stmt->fetch()) {
    $total_loan_amount_owed = $row['amount_owed'];
    //$total_loan_amount_paid = $row['amount_paid'];
  }
  // get calculation of what is remaining:
  //$total_loan_amount_remaining = ($total_loan_amount_owed - $total_loan_amount_paid);

  //$savings_total_string = '';
  $total_yearly_expenses = 0;
  $total_yearly_incomes = 0;
  $total_yearly_savings = 0;
  $total_yearly_loan_savings = 0;
  $is_alternate_row = false;
  $add_alternating_class = '';
  foreach ($months_of_year as $month) {
    $this_total = '~';
    $color = 'grey';

    $month_savings = 0;
    $month_savings_with_loans = 0;

    if ($is_alternate_row == false) {
      $add_alternating_class = '';
      $is_alternate_row = true;
    } else {
      $add_alternating_class = 'class="alternating_row"';
      $is_alternate_row = false;
    }

    echo '<tr>';
      // month name
      echo '<td '.$add_alternating_class.' style="color:grey;">'.$month.'</td>';
      // incomes
      if (array_key_exists($month, $income_monthly_totals)) {
        $total_yearly_incomes += $income_monthly_totals[$month];

        $this_total = $income_monthly_totals[$month];
        echo '<td '.$add_alternating_class.'>$'.number_format($this_total, 2).'</td>';
      } else {
        echo '<td '.$add_alternating_class.' style="color:grey;">~</td>';
      }
      // expenses
      if (array_key_exists($month, $expense_monthly_totals)) {
        $add_bills_total = $total_history_bills + $expense_monthly_totals[$month];
        $total_yearly_expenses += $add_bills_total;

        if (array_key_exists($month, $income_monthly_totals)) {
          $month_savings = ($income_monthly_totals[$month] - $add_bills_total);
        } else {
          $month_savings = 0 - $add_bills_total;
        }
        $month_savings_with_loans = ($month_savings + $total_loan_amount_owed); // regardless we add loans to savings
        $total_yearly_savings += $month_savings;
        $total_yearly_loan_savings += $month_savings_with_loans;
        // check if positive
        $save_color1 = 'red';
        if ($month_savings >= 0) {
          $save_color1 = 'green';
        }
        //$savings_total_string .= '<td style="color:'.$save_color.';">$'.number_format($month_savings, 2).'</td>';
        echo '<td '.$add_alternating_class.'>$'.number_format($add_bills_total, 2).'</td>';
        echo '<td '.$add_alternating_class.' style="color:'.$save_color1.';">$'.number_format($month_savings, 2).'</td>';
        $save_color2 = 'red';
        if ($month_savings_with_loans >= 0) {
          $save_color2 = 'green';
        }
        echo '<td '.$add_alternating_class.' style="color:'.$save_color2.';">$'.number_format($month_savings_with_loans, 2).'</td>';
      } else {
        //$savings_total_string .= '<td style="color:grey;">~</td>';
        echo '<td '.$add_alternating_class.' style="color:grey;">~</td>';
        echo '<td '.$add_alternating_class.' style="color:grey;">~</td>';
        echo '<td '.$add_alternating_class.' style="color:grey;">~</td>';
      }
    echo '</tr>';
  }


  echo '<tr>';
    echo '<td class="end_row_options" style="color:grey;">(Totals)</td>';
    echo '<td class="end_row_options">$'.number_format($total_yearly_incomes, 2).'</td>';
    echo '<td class="end_row_options">$'.number_format($total_yearly_expenses, 2).'</td>';
    $save_color = 'red';
    if ($total_yearly_savings >= 0) {
      $save_color = 'green';
    }
    echo '<td class="end_row_options" style="color:'.$save_color.';">$'.number_format($total_yearly_savings, 2).'</td>';
    $save_color = 'red';
    if ($total_yearly_loan_savings >= 0) {
      $save_color = 'green';
    }
    echo '<td class="end_row_options" style="color:'.$save_color.';">$'.number_format($total_yearly_loan_savings, 2).'</td>';
  echo '</tr>';


  echo '</table>';
}

function show_ious_table($user_id, $action, $current_page_num, $date_search, $current_user_owes = false, $paid_off = false){
  // add or subtract the page number depending on the action
  $get_current_page_num = 0;
  if ($action == "Next") { $get_current_page_num = $current_page_num + 1; } else { $get_current_page_num = $current_page_num - 1; }
  echo '<p id="page_show" style="text-align:center; color:grey;">(Page '.$get_current_page_num.')</p>';

  //echo "current_page_num: ".$get_current_page_num."<br>";
  $show_num_records_per_page = 5;
  $get_sql_limit_min = ($show_num_records_per_page * $get_current_page_num) - $show_num_records_per_page;
  $get_sql_limit_max = ($show_num_records_per_page * $get_current_page_num);

  $editable = false;  // true for if it is not paid off and if created_by is this user
  $insertable = false;  // true if not paid off

  $loaner = '=';
  $debtor = '!=';
  $color = '';
  $form_type = 'Loan';
  $current_page_name = "current_loan_page";
  // check to see if this is owed by current user or not:
  if ($current_user_owes == true) {
    $loaner = '!=';
    $debtor = '=';
    $color = 'color:red;';
    $form_type = 'Debt';
      $current_page_name = "current_debt_page";
  }

  echo '<p id="'.$current_page_name.'" style="text-align:center; display:none;" value="'.$get_current_page_num.'">'.$get_current_page_num.'</p>'; //style="display:none;"

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
    LIMIT ".$get_sql_limit_min.",".$get_sql_limit_max .";
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
              echo '<a href="../ajax/ious.ajax.php?selected_id='.$row['iou_id'].'&update_type=Delete&form_type='.$form_type.'&user_id='.$user_id.'"><i class="actions"><p class="bi-trash-fill"></p></i></a>';
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

function show_notifications_table($user_id, $action, $current_num, $date_search, $conn) {
  // add or subtract the page number depending on the action
  $get_current_page_num = 0;
  if ($action == "Next") { $get_current_page_num = $current_num + 1; } else { $get_current_page_num = $current_num - 1; }
  echo '<p id="current_page_num" style="text-align:center; display:none;" value="'.$get_current_page_num.'">'.$get_current_page_num.'</p>'; //style="display:none;"
  echo '<p id="page_show" style="text-align:center; color:grey;">(Page '.$get_current_page_num.')</p>';

  //echo "current_page_num: ".$get_current_page_num."<br>";
  $show_num_records_per_page = 5;
  $get_sql_limit_min = ($show_num_records_per_page * $get_current_page_num) - $show_num_records_per_page;
  $get_sql_limit_max = ($show_num_records_per_page * $get_current_page_num);

      $sql = "
              SELECT
                n.n_id,
                n.n_subject,
                n.n_message,
                n.n_type,
                n.n_send_date,
                n.n_read_date,
                n.is_active,
                n.n_to_user,

                ur.role_name AS 'from_role_name',
                ur.role_color AS 'from_role_color',

                fu.id_role AS 'from_role',
                fu.user_name AS 'from_username',
                fu.user_icon AS 'from_icon',
                fu.user_fname AS 'from_fname',
                fu.user_lname AS 'from_lname'

              FROM notifications n
              LEFT JOIN users fu ON n.n_from_user = fu.user_id
              LEFT JOIN user_roles ur ON fu.id_role = ur.role_id

              WHERE n.is_active = 1
              AND n.n_to_user = ".$user_id." OR n.n_to_user = 0

              ORDER BY n.n_send_date DESC
              LIMIT ".$get_sql_limit_min.",".$get_sql_limit_max .";
      ";
      //echo $sql;
      $dbh = new Dbh();
      $stmt = $dbh->connect()->query($sql);
      // get num rows to check
      $num_stmt = $conn->prepare($sql);
      $num_stmt->execute();
      /* store the result in an internal buffer */
      $num_stmt->store_result();
      if ($num_stmt->num_rows > 0) {
        echo '<table class="table table-dark" style="width:100%;">'; // mini table to display months
          echo '<tr>';
            echo '<th>Type</th>';
            echo '<th>From</th>';
            echo '<th>Subject</th>';
            echo '<th>Sent</th>';
            echo '<th class="end_row_options">';
              //echo '<a href="../ajax/messages.ajax.php?form_type=Income&user_id='.$user_id.'"><p class="bi-plus-circle" style="color:white;"></p></a>';
            echo '</th>';
          echo '</tr>';
      } else {
        echo '<p style="text-align:center;">(There are no notifications)</p>';
      }



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
          //echo '<td style="display:none;"><p id="msg_id" name="msg_id" value="'.$row['msg_id'].'">'.$row['msg_id'].'</p></td>';
          //echo '<td>'.$row['from_fname'].' '.$row['from_lname'].'</td>';
          $font_weight = 'font-weight:normal;';
          if ($row['n_read_date'] < date('2020-01-01 00:00:00')) {
            $font_weight = 'font-weight:bold;';
          }

          echo '<td '.$add_alternating_class.' style="'.$font_weight.'">'.$row['n_type'].'</td>';
          echo '<td '.$add_alternating_class.' style="'.$font_weight.'">';
            echo '<i style="color:'.$row['from_role_color'].'; style="'.$font_weight.'"">'.$row['from_username'].' ('.$row['from_role_name'].')</i>';
          echo '</td>';
          echo '<td '.$add_alternating_class.' style="'.$font_weight.'">'.$row['n_subject'].'</td>';
          $date_string = strtotime($row['n_send_date']);
          echo '<td '.$add_alternating_class.' style="color:grey;">' .date('M, d', $date_string). '</td>';
          echo '<td class="end_row_options">';
            //echo '<a href="../includes/messages.ajax.php?user_id='.$user_id.'"><p class="bi-eye-fill" style="color:white;"></p></a>';
            echo '<button class="end_row_options" style="text-align:center; margin:auto; color:white; background-color:black; border:none;" name="view" onclick="view_msg('.$row['n_id'].');" value="View"><i class="actions"><p class="bi-eye-fill"></p></i></button>';
          echo '</td>';
        echo '</tr>';
      }

  echo '</table>';
  echo '<br>';
}


if ($table_scroll == 'Expenses') {
  show_results_table($user_id, $action, $current_num, $date_search);
} elseif ($table_scroll == 'Yearly') {
  show_yearly_table($user_id, $action, $current_num, $date_search);
} elseif ($table_scroll == 'Loans') {
  show_ious_table($user_id, $action, $current_num, $date_search, false, false);
} elseif ($table_scroll == 'Debts') {
  show_ious_table($user_id, $action, $current_num, $date_search, true, false);
} elseif ($table_scroll == 'Notifications') {
  show_notifications_table($user_id, $action, $current_num, $date_search, $conn);
}


//header("Location: ../pages/finances.php");
//exit();
