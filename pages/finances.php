<?php
//declare(strict_types = 1);
include '../includes/autoloader.inc.php';

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
  $navbar->show_header_nav();
?>


  <!--
  <div class="row content">
    <div id="left_sidenav">
      <p class="bi-coin" style="font-size: 1rem; color: white;"><a href="#"> Overview</a></p>
      <p class="bi-piggy-bank-fill" style="font-size: 1rem; color: white;"><a href="#"> Budgets</a></p>
      <p class="bi-receipt" style="font-size: 1rem; color: white;"><a href="#"> Bills</a></p>
    </div>
  -->
      <?php
      // this is for looking at previous finance dates in the system
        //$date = 'now()';
        //if (isset($_POST['date_range'])) {
        //  $date = $_POST['date_range'];
        //}

        // get all months of the year and store them into an array for using in two places
        /*
        $months = array();
        for ($i = 0; $i < 12; $i++) {
            $timestamp = mktime(0, 0, 0, date('n') - $i, 1);
            $months[date('n', $timestamp)] = date('F', $timestamp);
        }
        foreach ($months as $num => $name) {
            printf('<option value="%u">%s</option>', $num, $name);
        }
        */
        $first_month = date('Y-m-d', strtotime('first day of january this year'));
        $this_year = date('Y', strtotime($first_month));
        //$next_year = date('Y-m-d', strtotime('+1 year'));

        //echo "first_day_of_year: ".$first_day_of_year."<br><br>";
      //  echo "next_year: ".$next_year."<br>";
        //echo "month: ".$month."<br>";
        $months_of_year = array();
        for ($i = 0; $i < 12; $i++) {
             // echo date('F Y', $month);
             $next_month = strtotime("+".$i." month", strtotime($first_month));
             $show_month = date('M', $next_month);
             //echo "month: ".$show_month."<br>";
             array_push($months_of_year, $show_month);
        }
        //var_dump($months_of_year);
        // start the outer table
        echo '<table>'; // for the entirety of the main three sections: incomes (left), overview (middle), expenses (right)
          echo '<tr>';

            echo '<td style="background:rgb(25, 29, 32); text-align:center;">';
              // loop through each month of the year for this year and display the amount for each month

              echo '<h2>Incomes</h2>';
              echo '<h2>'.$this_year.'</h2>';
              echo '<table>'; // mini table to display months

              $monthly_totals = array();

              $sql = "SELECT SUM(f.fi_amount) AS 'fi_amount',
                             f.fi_date,
                             f.is_active
                      FROM finance_incomes f
                      WHERE is_active = 1
                      AND YEAR(f.fi_date)=YEAR(now())
                      GROUP BY MONTH(f.fi_date)
                      ORDER BY f.fi_date ASC;
              ";
              $dbh = new Dbh();
              $stmt = $dbh->connect()->query($sql);

              while ($row = $stmt->fetch()) {
                $fi_date = strtotime($row['fi_date']);
                $get_formatted_date = date('M', $fi_date);

                $monthly_totals[$get_formatted_date] = $row['fi_amount'];

                //echo "get_formatted_date: ".$get_formatted_date."<br>";
                //echo "this month: ".$months_of_year[$counter]."<br>";

              }

              foreach ($months_of_year as $month) {
                  $this_total = '(No data)';
                  $color = 'grey';
                  //echo "month: ".$month."<br>";
                  //echo "get_dates: ".$get_dates[$counter]."<br>";
                  foreach($monthly_totals as $each_month => $total) {
                    //echo "each_month: ".$each_month."<br>";
                    if ($month == $each_month) {
                      //echo "picked! <br>";
                      $this_total = '$'.$total;
                      $color = 'white';
                      break;
                    }
                  }
                  echo '<tr>';
                    echo '<td style="color:grey;">'.$month.'</td>';
                    echo '<td style="color:'.$color.';">'.$this_total.'</td>';
                  echo '</tr>';

              }
              echo '</table>'; // mini table to display months

            echo '</td>';
            echo '<td>';
              //echo '<div class="container">';

                echo '<h1 style="text-align:center;">Finances Overview</h1>';
                echo '<h2 style="text-align:center;">'.date('F, Y').'</h2>';
                echo '<table class="table table-dark" style="background-color:#3a5774;">';
                echo '<tr>';
                  echo '<td style="background: rgb(33, 37, 46); border-right:2px solid rgb(33, 37, 46); border-top:2px solid rgb(33, 37, 46);">Incomes</td>';
                  echo '<td style="background: rgb(33, 37, 46); border-left:2px solid rgb(33, 37, 46); border-top:2px solid rgb(33, 37, 46);">Expenses</td>';
                echo '</tr>';
                echo '<tr>';
                  echo '<td style="border:2px solid rgb(33, 37, 46); padding:0px; margin:0px;">';
                    // check which table:
                    $sql = "SELECT fi.fi_id,
                                fi.fi_company,
                                fi.fi_name,
                                fi.fi_amount,
                                fi.fi_date
                            FROM finance_incomes fi
                            WHERE MONTH(fi.fi_date)=MONTH(now())
                            AND YEAR(fi.fi_date)=YEAR(now())
                            AND is_active = 1;
                    ";
                    $dbh = new Dbh();
                    $stmt = $dbh->connect()->query($sql);
                    echo '<table class="table table-dark" style="background-color:#3a5774; text-align:center;">';
                    echo '<tr>';
                      echo '<th>Company</th>';
                      echo '<th>Name</th>';
                      echo '<th>Date</th>';
                      echo '<th>Amount</th>';
                      echo '<th style="background-color: rgb(33, 37, 46);">';
                        echo '<a href="../includes/finances.inc.php?form_type=Income"><p class="bi-plus-circle" style="color:white;"></p></a>';
                      echo '</th>';
                    echo '</tr>';
                    $total_incomes_amount = 0;
                    while ($row = $stmt->fetch()) {
                      echo '<tr>';
                        echo '<td style="background:rgb(25, 29, 32); color:grey;">' .$row['fi_company']. '</td>';
                        echo '<td style="background:rgb(25, 29, 32); color:grey;">' .$row['fi_name']. '</td>';
                        $date_string = strtotime($row['fi_date']);
                        echo '<td style="background:rgb(25, 29, 32); color:grey;">' .date('M, d', $date_string). '</td>';
                        echo '<td style="text-align:right; background:rgb(25, 29, 32);">' .number_format((float)$row['fi_amount'], 2). '</td>';
                        echo '<td style="background:rgb(33, 37, 46);">';
                          echo '<span style="display:flex;">';
                            echo '<a href="../includes/finances.inc.php?selected_id='.$row['fi_id'].'&update_type=Edit&form_type=Income"><p class="bi-pencil-fill" style="color:white;"></p></a>';
                            echo '<a href="../ajax/finances.ajax.php?selected_id='.$row['fi_id'].'&update_type=Delete&form_type=Income"><p class="bi-trash-fill" style="color:white;"></p></a>';
                          echo '</span>';
                        echo '</td>';
                      echo '</tr>';
                      // get variables for savings:
                      $total_incomes_amount += (float)$row['fi_amount'];
                    }
                    echo '<tr>';
                      echo '<td colspan=3 style="text-align:left; background-color:rgb(33, 37, 46);">Total:</td>';
                      echo '<td style="text-align:right; background-color:rgb(33, 37, 46);">$'.number_format($total_incomes_amount, 2).'</td>';
                      echo '<td style="background:rgb(33, 37, 46);"></td>';
                    echo '</tr>';
                    echo '</table>';
                  echo '</td>';
                  echo '<td style="border:2px solid rgb(33, 37, 46); padding:0px; margin:0px;">';
                    $sql = "SELECT fe.fe_id,
                                fe.fe_company,
                                fe.id_category,
                                cat.cat_name,
                                fe.fe_name,
                                fe.fe_amount,
                                fe.fe_date
                            FROM finance_expenses fe
                            LEFT JOIN categories cat ON fe.id_category = cat.cat_id
                            WHERE MONTH(fe.fe_date)=MONTH(now())
                            AND YEAR(fe.fe_date)=YEAR(now())
                            AND fe.is_active = 1

                            ORDER BY fe.fe_date DESC;
                            #LIMIT 5;
                    ";
                    $dbh = new Dbh();
                    $stmt = $dbh->connect()->query($sql);
                    echo '<table class="table table-dark" style="background-color:#3a5774; text-align:center;">';
                    echo '<tr>';
                      echo '<th>Company</th>';
                      echo '<th>Category</th>';
                      echo '<th>Name</th>';
                      echo '<th>Date</th>';
                      echo '<th>Amount</th>';
                      echo '<th style="background-color: rgb(33, 37, 46);">';
                        echo '<a href="../includes/finances.inc.php?form_type=Expense"><p class="bi-plus-circle" style="color:white;"></p></a>';
                      echo '</th>';
                    echo '</tr>';
                    $total_expenses_amount = 0;
                    $total_not_shown_expenses = 0;
                    $show_limit = 5;
                    $counter = 1;
                    $additional_rows = 0;
                    while ($row = $stmt->fetch()) {
                      if ($counter <= $show_limit){
                        echo '<tr>';
                          echo '<td style="background:rgb(25, 29, 32); color:grey;">' .$row['fe_company']. '</td>';
                          echo '<td style="background:rgb(25, 29, 32); color:grey;">' .$row['cat_name']. '</td>';
                          echo '<td style="background:rgb(25, 29, 32);">' .$row['fe_name']. '</td>';
                          $date_string = strtotime($row['fe_date']);
                          echo '<td style="background:rgb(25, 29, 32); color:grey;">' .date('M, d', $date_string). '</td>';
                          echo '<td style="text-align:right; background:rgb(25, 29, 32);">' .number_format((float)$row['fe_amount'], 2). '</td>';
                          echo '<td style="background:rgb(33, 37, 46);">';
                            echo '<span style="display:flex;">';
                              echo '<a href="../includes/finances.inc.php?selected_id='.$row['fe_id'].'&update_type=Edit&form_type=Expense"><p class="bi-pencil-fill" style="color:white;"></p></a>';
                              echo '<a href="../ajax/finances.ajax.php?selected_id='.$row['fe_id'].'&update_type=Delete&form_type=Expense"><p class="bi-trash-fill" style="color:white;"></p></a>';
                            echo '</span>';
                          echo '</td>';
                        echo '</tr>';
                      // get variables for savings:
                      $total_expenses_amount += (float)$row['fe_amount'];

                      } else {
                          $additional_rows++;
                      }
                      // always add to the total amount for all the rows
                      $total_not_shown_expenses += (float)$row['fe_amount'];
                      $counter++;
                    }
                    echo '<tr>';
                      echo '<td colspan=4 style="text-align:left; background-color:rgb(33, 37, 46);">Total:</td>';
                      echo '<td style="text-align:right; background-color:rgb(33, 37, 46);">$'.number_format($total_expenses_amount, 2).'</td>';
                      echo '<td style="background:rgb(33, 37, 46);"></td>';
                    echo '</tr>';
                    echo '<tr>';
                      echo '<td colspan=4 style="text-align:left;"><i>('.$additional_rows.' more rows...)</i></td>';
                      echo '<td style="background:rgb(33, 37, 46);">($'.number_format($total_not_shown_expenses, 2).')</td>';
                      echo '<td style="background:rgb(33, 37, 46);"></td>';
                    echo '</tr>';
                    echo '</table>';
                  echo '</td>';
                echo '</tr>';

                echo '<tr>';
                  echo '<td style="background: rgb(33, 37, 46); border:2px solid rgb(33, 37, 46);">Passive Incomes</td>';
                  echo '<td style="background: rgb(33, 37, 46); border:2px solid rgb(33, 37, 46);">This Month\'s Bills</td>';
                echo '</tr>';

                echo '<tr>';
                  echo '<td style="border-right:2px solid rgb(33, 37, 46); padding:0px; margin:0px;">';
                    $sql = "SELECT * FROM passive_incomes WHERE is_active = 1;";
                    $dbh = new Dbh();
                    $stmt = $dbh->connect()->query($sql);
                    echo '<table class="table table-dark" style="background-color:#3a5774; text-align:center;">';
                      echo '<tr>';
                        echo '<th>Name</th>';
                        echo '<th>Amount</th>';
                        echo '<th>Frequency</th>';
                        echo '<th style="background-color: rgb(33, 37, 46);">';
                          echo '<a href="../includes/finances.inc.php?form_type=Passive"><p class="bi-plus-circle" style="color:white;"></p></a>';
                        echo '</th>';
                      echo '</tr>';
                      $total_passive_incomes = 0;
                      while ($row = $stmt->fetch()) {
                        echo '<tr>';
                          echo '<td style="background:rgb(25, 29, 32);">' .$row['pi_name']. '</td>';
                          echo '<td style="text-align:right; background:rgb(25, 29, 32);">' .number_format((float)$row['pi_amount'], 2). '</td>';
                          echo '<td style="background:rgb(25, 29, 32); color:grey;">' .$row['pi_freq']. '</td>';
                          echo '<td style="background:rgb(33, 37, 46);">';
                            echo '<span style="display:flex;">';
                              echo '<a href="../includes/finances.inc.php?selected_id='.$row['pi_id'].'&update_type=Edit&form_type=Passive"><p class="bi-pencil-fill" style="color:white;"></p></a>';
                              echo '<a href="../ajax/finances.ajax.php?selected_id='.$row['pi_id'].'&update_type=Delete&form_type=Passive"><p class="bi-trash-fill" style="color:white;"></p></a>';
                            echo '</span>';
                          echo '</td>';
                        echo '</tr>';
                        $total_passive_incomes += (float)$row['pi_amount'];
                      }
                      echo '<tr>';
                        echo '<td colspan=2 style="text-align:left; background-color:rgb(33, 37, 46);">Total:</td>';
                        echo '<td style="text-align:right; background-color:rgb(33, 37, 46);">$'.number_format($total_passive_incomes, 2).'</td>';
                        echo '<td style="background:rgb(33, 37, 46);"></td>';
                      echo '</tr>';
                    echo '</table>';
                  echo '</td>';
                  echo '<td style="border:2px solid rgb(33, 37, 46); padding:0px; margin:0px;">';
                  /*
                    $sql = "SELECT bills.bill_id,
                                bills.bill_name,
                                bills.bill_amount,
                                bills.bill_freq
                            FROM current_bills bills
                            WHERE is_active = 1
                            AND bills.bill_freq = 'M';
                    ";
                    */
                    $sql = "SELECT bl.*,
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
                            WHERE cb.bill_freq = 'M'
                            AND cb.is_active = 1

                            GROUP BY bl.bl_id_bill;
                    ";
                    $dbh = new Dbh();
                    $stmt = $dbh->connect()->query($sql);
                    echo '<table class="table table-dark" style="background-color:#3a5774; text-align:center;">';
                      echo '<tr>';
                        echo '<th>Name</th>';
                        echo '<th>Amount</th>';
                        echo '<th>Frequency</th>';
                        echo '<th style="background-color: rgb(33, 37, 46);">';
                          echo '<a href="../includes/finances.inc.php?form_type=Bill"><p class="bi-plus-circle" style="color:white;"></p></a>';
                        echo '</th>';
                      echo '</tr>';
                      $total_bills_amount = 0;
                      while ($row = $stmt->fetch()) {
                        echo '<tr>';
                          echo '<td style="background:rgb(25, 29, 32);">' .$row['bill_name']. '</td>';
                          echo '<td style="text-align:right; background:rgb(25, 29, 32);">' .number_format((float)$row['bl_amount'], 2). '</td>';
                          echo '<td style="background:rgb(25, 29, 32); color:grey;">' .$row['bill_freq']. '</td>';
                          echo '<td style="background:rgb(33, 37, 46);">';
                            echo '<span style="display:flex;">';
                              echo '<a href="../includes/finances.inc.php?selected_id='.$row['bill_id'].'&update_type=Edit&form_type=Bill"><p class="bi-pencil-fill" style="color:white;"></p></a>';
                              echo '<a href="../ajax/finances.ajax.php?selected_id='.$row['bill_id'].'&update_type=Delete&form_type=Bill"><p class="bi-trash-fill" style="color:white;"></p></a>';
                            echo '</span>';
                          echo '</td>';
                        echo '</tr>';
                        // get variables for savings:
                        $total_bills_amount += (float)$row['bl_amount'];
                      }
                      echo '<tr>';
                        echo '<td colspan=2 style="text-align:left; background-color:rgb(33, 37, 46);">Total:</td>';
                        echo '<td style="text-align:right; background-color:rgb(33, 37, 46);">$'.number_format($total_bills_amount, 2).'</td>';
                        echo '<td style="background:rgb(33, 37, 46);"></td>';
                      echo '</tr>';
                    echo '</table>';
                  echo '</td>';
                echo '</tr>';

                echo '<tr>';
                  echo '<td style="background: rgb(33, 37, 46); border:2px solid rgb(33, 37, 46);">Budget Categories</td>';
                  echo '<td style="background: rgb(33, 37, 46); border:2px solid rgb(33, 37, 46);">Category Spending</td>';
                echo '</tr>';
                echo '<tr>';
                  echo '<td style="border:2px solid rgb(33, 37, 46); padding:0px; margin:0px;">';
                  // budgets names need to match the categories of expenses so that we can sum each expense category into a table //
                    $sql = "SELECT b.bud_id,
                                b.bud_name,
                                b.bud_amount,
                                b.bud_freq
                            FROM budgets b
                            WHERE is_active = 1

                            ORDER BY b.bud_name ASC;
                    ";
                    $dbh = new Dbh();
                    $stmt = $dbh->connect()->query($sql);
                    echo '<table class="table table-dark" style="background-color:#3a5774; text-align:center;">';
                      echo '<tr>';
                        echo '<th>Name</th>';
                        echo '<th>Amount</th>';
                        echo '<th>Frequency</th>';
                        echo '<th style="background-color: rgb(33, 37, 46);">';
                          echo '<a href="../includes/finances.inc.php?form_type=Budget"><p class="bi-plus-circle" style="color:white;"></p></a>';
                        echo '</th>';
                      echo '</tr>';
                      $total_budgets_amount = 0;
                      $cat_budgets = array();
                      while ($row = $stmt->fetch()) {
                        //array_push($cat_budgets, $row['bud_amount']);
                        $cat_budgets[$row['bud_name']] = $row['bud_amount'];
                        echo '<tr>';
                          echo '<td style="background:rgb(25, 29, 32);">' .$row['bud_name']. '</td>';
                          echo '<td style="text-align:right; background:rgb(25, 29, 32);">' .number_format((float)$row['bud_amount'], 2). '</td>';
                          echo '<td style="background:rgb(25, 29, 32); color:grey;">' .$row['bud_freq']. '</td>';
                          echo '<td style="background:rgb(33, 37, 46);">';
                            echo '<span style="display:flex;">';
                              echo '<a href="../includes/finances.inc.php?selected_id='.$row['bud_id'].'&update_type=Edit&form_type=Budget"><p class="bi-pencil-fill" style="color:white;"></p></a>';
                              echo '<a href="../ajax/finances.ajax.php?selected_id='.$row['bud_id'].'&update_type=Delete&form_type=Budget"><p class="bi-trash-fill" style="color:white;"></p></a>';
                            echo '</span>';
                          echo '</td>';
                        echo '</tr>';
                        // get variables for savings:
                        $total_budgets_amount += (float)$row['bud_amount'];
                      }
                      echo '<tr>';
                        echo '<td colspan=2 style="text-align:left; background-color:rgb(33, 37, 46);">Total:</td>';
                        echo '<td style="text-align:right; background-color:rgb(33, 37, 46);">$'.number_format($total_budgets_amount, 2).'</td>';
                        echo '<td style="background:rgb(33, 37, 46);"></td>';
                      echo '</tr>';
                    echo '</table>';
                  echo '</td>';
                  echo '<td style="border:2px solid rgb(33, 37, 46); padding:0px; margin:0px;">';
                    $sql = "SELECT fe.fe_id,
                                fe.id_category,
                                cat.cat_name,
                                SUM(fe.fe_amount) AS 'fe_amount',
                                fe.fe_date
                            FROM finance_expenses fe
                            LEFT JOIN categories cat ON fe.id_category = cat.cat_id
                            WHERE MONTH(fe.fe_date)=MONTH(now())
                            AND YEAR(fe.fe_date)=YEAR(now())
                            AND fe.is_active = 1

                            GROUP BY fe.id_category
                            ORDER BY fe.id_category ASC;
                    ";
                    $dbh = new Dbh();
                    $stmt = $dbh->connect()->query($sql);
                    echo '<table class="table table-dark" style="background-color:#3a5774; text-align:center;">';
                    echo '<tr>';
                      echo '<th>Category</th>';
                      echo '<th>Amount</th>';
                    echo '</tr>';

                    //var_dump($cat_budgets);
                    $counter = 0;
                    while ($row = $stmt->fetch()) {
                      // find the matching category with this name in category array
                      //$key = $row['fe_category'];
                      //$result = isset($array[$key]) ? $array[$key] : null;
                      //echo 'category: '. $row['fe_category'] .'<br>';
                      $find_budget = $cat_budgets[$row['cat_name']];
                      //echo 'find_budget: '.$find_budget.'<br>';
                      $color = 'green';
                      if (array_key_exists($row['cat_name'], $cat_budgets)) {
                        if ($find_budget <= $row['fe_amount']) {
                          $color = 'red';
                        }
                      }

                      echo '<tr>';
                        echo '<td style="background:rgb(25, 29, 32);">' .$row['cat_name']. '</td>';
                        echo '<td style="text-align:right; background:rgb(25, 29, 32); color:'.$color.';">' .number_format((float)$row['fe_amount'], 2). '</td>';
                      echo '</tr>';
                      $counter++;
                    }
                    echo '</table>';
                  echo '</td>';
                echo '</tr>';

                echo '<tr colspan=2 style="padding-top:20px;">';
                  echo '<td colspan=2 style="text-align:center; border-top:2px solid rgb(33, 37, 46);">Savings</td>';
                echo '</tr>';

                echo '<tr colspan=2>';
                  echo '<td colspan=2>';
                    echo '<table class="table table-dark" style="background-color:#3a5774; text-align:center;">';
                      echo '<tr>';
                        echo '<th colspan=2>Gross Monthly</th>';
                        echo '<th colspan=2>Net Monthly</th>';
                        //echo '<th>Gross Yearly</th>';
                        //echo '<th>Net Yearly</th>';
                      echo '</tr>';
                      echo '<tr>';
                        // we need to get some variables
                        $net_savings = $total_incomes_amount - $total_not_shown_expenses - $total_bills_amount;
                        $color = 'green';
                        if ($net_savings <= 0.00) {
                          $color = 'red';
                        }
                        echo '<td colspan=2 style="text-align:right; background:rgb(25, 29, 32);">$' .number_format($total_incomes_amount, 2). '</td>';
                        echo '<td colspan=2 style="text-align:right; background:rgb(25, 29, 32); color:'.$color.';">$' .number_format($net_savings, 2). '</td>';
                        //echo '<td style="text-align:right; background:rgb(25, 29, 32);">$' .number_format($total_incomes_amount*12, 2). '</td>';
                        //echo '<td style="text-align:right; background:rgb(25, 29, 32); color:green;">$' .number_format($net_savings*12, 2). '</td>';
                      echo '</tr>';
                    echo '</table>';
                  echo '</td>';
                echo '</tr>';
                echo '</table>';  // the table for the two main rows for incomes & expenses

            //  echo '</div>';  // the div ending for the main section for displaying overview

            echo '</td>';
            echo '<td style="background:rgb(25, 29, 32); text-align:center;">';

              echo '<h2>Expenses</h2>';
              echo '<h2>'.$this_year.'</h2>';
              echo '<table>'; // mini table to display months
              // sql for getting the bill logs for the correct times in history to show the correct expenses
              $sql = "SELECT bl.*,
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
                      GROUP BY bl.bl_id_bill;
              ";
              $dbh = new Dbh();
              $stmt = $dbh->connect()->query($sql);

              $total_history_bills = 0;
              while ($row = $stmt->fetch()) {
                $total_history_bills += $row['bl_amount'];
              }


              $monthly_totals = array();
              $sql = "SELECT SUM(f.fe_amount) AS 'fe_amount',
                             f.fe_date,
                             f.is_active
                      FROM finance_expenses f
                      WHERE is_active = 1
                      AND YEAR(f.fe_date)=YEAR(now())
                      GROUP BY MONTH(f.fe_date)
                      ORDER BY f.fe_date ASC;
              ";
              $dbh = new Dbh();
              $stmt = $dbh->connect()->query($sql);

              while ($row = $stmt->fetch()) {
                $fe_date = strtotime($row['fe_date']);
                $get_formatted_date = date('M', $fe_date);

                $monthly_totals[$get_formatted_date] = $row['fe_amount'];

                //echo "get_formatted_date: ".$get_formatted_date."<br>";
                //echo "this month: ".$months_of_year[$counter]."<br>";

              }

              foreach ($months_of_year as $month) {
                  $this_total = '(No data)';
                  $color = 'grey';
                  //echo "month: ".$month."<br>";
                  //echo "get_dates: ".$get_dates[$counter]."<br>";
                  foreach($monthly_totals as $each_month => $total) {
                    //echo "each_month: ".$each_month."<br>";
                    if ($month == $each_month) {
                      //echo "picked! <br>";
                      $add_bills_total = $total_history_bills + $total;
                      $this_total = '$'.$add_bills_total;
                      $color = 'white';
                      break;
                    }
                  }
                  echo '<tr>';
                    echo '<td style="color:grey;">'.$month.'</td>';
                    echo '<td style="color:'.$color.';">'.$this_total.'</td>';
                  echo '</tr>';

              }
              echo '</table>'; // mini table to display months

            echo '</td>';
          echo '</tr>';
        echo '</table>';
      ?>

<?php
  $footer = new Footer();
  $footer->show_footer();
?>

</body>
</html>
