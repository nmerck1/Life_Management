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
	function scroll_expenses(next_prev_num){
      // setup the ajax request
  		var xhttp = new XMLHttpRequest();
      // get variables from inputs below:
  		var current_page_num = document.getElementById('current_page_num');
      var user_id = document.getElementById('user_id');
      var date_search = document.getElementById('date_search');
      var table_scroll = 'Expenses'

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
           document.getElementById("scroll_div").innerHTML = this.responseText;
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
      /* <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
          google.charts.load("current", {packages:["corechart"]});
          //google.charts.setOnLoadCallback(drawChart);

        </script>*/
        echo '<p id="user_id" style="display:none;" value="'.$user_id.'">'.$user_id.'</p>';

        // this is for looking at previous finance dates in the system
        $date_search = date('Y-m-d');
        if (isset($_POST['date_search'])) {
          $date_search = $_POST['date_search'];
        }

        echo '<p id="date_search" style="display:none;" value="'.$date_search.'">'.$date_search.'</p>';

        $this_year = date('Y', strtotime($date_search));
        $first_month = date('Y-m-d', strtotime('first day of January'.$this_year));
        //$next_year = date('Y-m-d', strtotime('+1 year'));

        //echo "first_month: ".$first_month."<br><br>";
        //echo "next_year: ".$next_year."<br>";
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
        echo '<div class="container">';

          echo '<h1 style="text-align:center;">Monthly Overview</h1>';
          $show_month_year_title = date('F', strtotime($date_search));
          echo '<h2 style="text-align:center;"><i class="bi-calendar"> </i>'.$show_month_year_title.'</h2>';
          // mini form for displaying different dates in history
          echo '<form method="post" action="../pages/finances.php" style="text-align:center;">';
            //echo '<select>';
            //foreach ($months_of_year as $month) {
            //  echo '<option></option>';
            //}
            //echo '</select>';
            //echo $date_search;
            //$date = date('Y-m-d');	// default to today
            echo '<input type="date" name="date_search" value="'.$date_search.'"></input>';

            echo '<button type="submit" name="submit_search" class="btn btn-primary btn-sm" value="Display">Display Date</button>';
          echo '</form>';

          echo '<br>';

          echo '<div class="div_element_block">'; // div for incomes
            echo '<h4 style="text-align:center;"><i class="bi-plus-square"> </i>Incomes</h4>';
            // check which table:
            $sql = "
            SELECT fi.fi_id,
                fi.fi_company,
                fi.fi_name,
                fi.fi_amount,
                fi.fi_date
            FROM finance_incomes fi
            LEFT JOIN users u ON fi.id_user = u.user_id
            WHERE fi.is_active = 1
            AND u.user_id = ".$user_id."
            AND MONTH(fi.fi_date)=MONTH('".$date_search."')
            AND YEAR(fi.fi_date)=YEAR('".$date_search."')
            ";
            //echo $sql;
            $dbh = new Dbh();
            $stmt = $dbh->connect()->query($sql);
            echo '<table class="table table-dark" style="text-align:center;">';
            echo '<tr>';
              echo '<th>Company</th>';
              echo '<th>Name</th>';
              echo '<th>Date</th>';
              echo '<th style="text-align:right;">Amount</th>';
              echo '<th class="end_row_options">';
                echo '<a href="../includes/finances.inc.php?form_type=Income&user_id='.$user_id.'"><i class="actions"><p class="bi-plus-circle"></p></i></a>';
              echo '</th>';
            echo '</tr>';
              $total_incomes_amount = 0;
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
                  echo '<td '.$add_alternating_class.' style="color:grey;">' .$row['fi_company']. '</td>';
                  echo '<td '.$add_alternating_class.'>' .$row['fi_name']. '</td>';
                  $date_string = strtotime($row['fi_date']);
                  echo '<td '.$add_alternating_class.' style="color:grey;">' .date('M, d', $date_string). '</td>';
                  echo '<td '.$add_alternating_class.' style="text-align:right;">' .number_format((float)$row['fi_amount'], 2). '</td>';
                  echo '<td class="end_row_options">';
                    echo '<span>'; //style="display:flex;"
                      echo '<a href="../includes/finances.inc.php?selected_id='.$row['fi_id'].'&update_type=Edit&form_type=Income&user_id='.$user_id.'"><i class="actions"><p class="bi-pencil-fill"></p></i></a>';
                      echo '<a href="../ajax/finances.ajax.php?selected_id='.$row['fi_id'].'&update_type=Delete&form_type=Income&user_id='.$user_id.'" onclick="return confirm(\'Delete: '.$row['fi_name'].' Income?\')"><i class="actions"><p class="bi-trash-fill"></p></i></a>';
                    echo '</span>';
                  echo '</td>';
                echo '</tr>';
                // get variables for savings:
                $total_incomes_amount += (float)$row['fi_amount'];
              }
              echo '<tr>';
                echo '<td class="end_row_options" colspan=3 style="text-align:left;">Total:</td>';
                echo '<td class="end_row_options" style="text-align:right;">$'.number_format($total_incomes_amount, 2).'</td>';
                echo '<td class="end_row_options"></td>';
              echo '</tr>';
            echo '</table>';
          echo '</div>';

          echo '<br>';

          echo '<div class="div_element_block">';// div for expenses
            echo '<h4 style="text-align:center;"><i class="bi-dash-square"> </i>Expenses</h4>';
            echo '<p style="width:95%; margin:0px; text-align:center;">';
              echo '<button name="prev_button" onclick="scroll_expenses(0);" style="float:left; background:none; border:none; font-size:20px; height:32px;">';
                echo '<i class="actions"><p class="bi-arrow-left-square"></p></i>';
              echo '</button>';
              echo '<button name="next_button" onclick="scroll_expenses(1);" style="float:right; background:none; border:none; font-size:20px; height:32px;">';
                echo '<i class="actions"><p class="bi-arrow-right-square"></p></i>';
              echo '</button>';
            echo '</p>';


          echo '<div id="scroll_div">';
            echo '<p id="current_page_num" style="text-align:center; color:grey; display:none;" value="1">1</p>'; //style="display:none;"
            echo '<p id="page_show" style="text-align:center; color:grey;">(Page 1)</p>';
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
                    LIMIT 5;
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
                        echo '<a href="../ajax/finances.ajax.php?selected_id='.$row['fe_id'].'&update_type=Delete&form_type=Expense&user_id='.$user_id.'" onclick="return confirm(\'Delete: '.$row['fe_name'].' Expense?\')"><i class="actions"><p class="bi-trash-fill"></p></i></a>';
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
            echo '</div>';
          echo '</div>';

          echo '<br>';

          echo '<div class="div_element_block">'; // div for bills
            echo '<h4 style="text-align:center;"><i class="bi-receipt-cutoff"> </i>Bills</h4>';
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

                    GROUP BY bl.bl_id_bill;
            ";
            $dbh = new Dbh();
            $stmt = $dbh->connect()->query($sql);
            echo '<table class="table table-dark" style="text-align:center;">';
              echo '<tr>';
                echo '<th>Name</th>';
                echo '<th style="text-align:right;">Amount</th>';
                echo '<th>Frequency</th>';
                echo '<th class="end_row_options">';
                  echo '<a href="../includes/finances.inc.php?form_type=Bill&user_id='.$user_id.'"><i class="actions"><p class="bi-plus-circle"></p></i></a>';
                echo '</th>';
              echo '</tr>';
              $total_bills_amount = 0;
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
                  echo '<td '.$add_alternating_class.'>' .$row['bill_name']. '</td>';
                  echo '<td '.$add_alternating_class.' style="text-align:right;">' .number_format((float)$row['bl_amount'], 2). '</td>';
                  echo '<td '.$add_alternating_class.' style="color:grey;">' .$row['bill_freq']. '</td>';
                  echo '<td class="end_row_options">';
                    echo '<span>'; //style="display:flex;"
                      echo '<a href="../includes/finances.inc.php?selected_id='.$row['bill_id'].'&update_type=Edit&form_type=Bill&user_id='.$user_id.'"><i class="actions"><p class="bi-pencil-fill"></p></i></a>';
                      echo '<a href="../ajax/finances.ajax.php?selected_id='.$row['bill_id'].'&update_type=Delete&form_type=Bill&user_id='.$user_id.'" onclick="return confirm(\'Delete: '.$row['bill_name'].' Bill?\')"><i class="actions"><p class="bi-trash-fill"></p></i></a>';
                    echo '</span>';
                  echo '</td>';
                echo '</tr>';
                // get variables for savings:
                $total_bills_amount += (float)$row['bl_amount'];
                //echo "total_bills_amount: ".$total_bills_amount."<br>";
              }
              echo '<tr>';
                echo '<td class="end_row_options" style="text-align:left;">Total:</td>';
                echo '<td class="end_row_options" style="text-align:right;">$'.number_format($total_bills_amount, 2).'</td>';
                echo '<td colspan=2 class="end_row_options"></td>';
              echo '</tr>';
            echo '</table>';
          echo '</div>';

          echo '<br>';

          echo '<div class="div_element_block">';// div for category spending
            echo '<h4 style="text-align:center;">Category Spending</h4>';

            $sql = "
                    SELECT SUM(fe.fe_amount) AS 'fe_amount',
                           fe.fe_date,
                           fe.is_active,
                           cat.cat_name,
                           bud.bud_amount
                    FROM finance_expenses fe
                    LEFT JOIN users u ON fe.id_user = u.user_id
                    LEFT JOIN categories cat ON fe.id_category = cat.cat_id
                    LEFT JOIN budgets bud ON fe.id_category = bud.id_category
                    WHERE fe.is_active = 1
                    AND bud.is_active = 1
                    AND u.user_id = ".$user_id."
                    AND bud.id_user = ".$user_id."
                    AND YEAR(fe.fe_date)=YEAR('".$date_search."')
                    AND MONTH(fe.fe_date)=MONTH('".$date_search."')
                    GROUP BY cat.cat_name
                    ORDER BY cat.cat_name ASC;
            ";
            //echo $sql .'<br>';
            $dbh = new Dbh();
            $stmt = $dbh->connect()->query($sql);

            $build_table = '';
            $total_spent_amount = 0;
            $total_budget_amount = 0;
            $total_over_under_amount = 0;
            $is_alternate_row = false;
            $add_alternating_class = '';
            while ($row = $stmt->fetch()) {
              $build_table .= '<tr>';

              if ($is_alternate_row == false) {
                $add_alternating_class = '';
                $is_alternate_row = true;
              } else {
                $add_alternating_class = 'class="alternating_row"';
                $is_alternate_row = false;
              }

                $build_table .= '<td '.$add_alternating_class.'>' .$row['cat_name']. '</td>';
                $build_table .= '<td '.$add_alternating_class.' style="text-align:right;">$' .number_format($row['fe_amount'], 2). '</td>';
                if ($row['bud_amount'] == NULL) {
                    $build_table .= '<td '.$add_alternating_class.' style="text-align:right; color:grey;">(No budget set)</td>';
                    $build_table .= '<td '.$add_alternating_class.' style="text-align:right; color:green;">$0.00</td>';
                } else {
                  $build_table .= '<td '.$add_alternating_class.' style="text-align:right;">$' .number_format($row['bud_amount'], 2). '</td>';
                  // get the difference:
                  $bud_diff = (float)($row['bud_amount'] - $row['fe_amount']);
                  $color = 'red';
                  if ($bud_diff >= 0) { $color = 'green'; }
                  $total_over_under_amount += $bud_diff;
                  $total_budget_amount += $row['bud_amount'];
                  $total_spent_amount += $row['fe_amount'];
                  $build_table .= '<td '.$add_alternating_class.' style="text-align:right; color:'.$color.';">$' .number_format($bud_diff, 2). '</td>';
                }
              $build_table .= '</tr>';

            }




                //echo "number of loops: ".$loops.'<br>';
                // check if there was anything to show:
                if ($build_table == '') {
                  echo '<p class="end_row_options" style="color:grey; text-align:center;">(Data will show here when you set your budget in a category)</p>';
                } else {
                  //echo '<i style="color:grey;">';
                    //echo '(The "μ" symbol in parenthesis next to the column for "Spent" amounts, represents the average amount
                            //spent per day in this category.)';
                  //echo '</i>';

                  echo '<table class="table table-dark" style="text-align:center;">';
                      echo '<tr>';
                        echo '<th>Category</th>';
                        echo '<th style="text-align:right;">Spent</th>';// (μ)
                        echo '<th style="text-align:right;">Budget</th>';
                        echo '<th style="text-align:right;">Over/Under</th>';
                      echo '</tr>';

                      echo $build_table;

                      echo '<tr>';
                        echo '<td colspan=1 class="end_row_options" style="text-align:left;">Totals:</td>';
                        echo '<td class="end_row_options" style="text-align:right;">$'.number_format($total_spent_amount, 2).'</td>';
                        echo '<td class="end_row_options" style="text-align:right;">$'.number_format($total_budget_amount, 2).'</td>';
                        $color = 'red';
                        if ($total_over_under_amount >= 0) { $color = 'green'; }
                        echo '<td class="end_row_options" style="text-align:right; color:'.$color.';">$'.number_format($total_over_under_amount, 2).'</td>';
                        echo '<td class="end_row_options"></td>';
                      echo '</tr>';

                  echo '</table>';
                }


                //echo '<p id="cat_names">'.$cat_names.'</p>';
                //echo '<p id="cat_amounts">'.$cat_amounts.'</p>';




          echo '</div>';

          echo '<br>';

          //echo '<div class="div_element_block">';
            //echo '<h4 style="text-align:center;">Metrics</h4>';

              $sql = "
                      SELECT SUM(fe.fe_amount) AS 'fe_amount',
                             fe.fe_date,
                             fe.is_active,
                             cat.cat_name
                      FROM finance_expenses fe
                      LEFT JOIN users u ON fe.id_user = u.user_id
                      LEFT JOIN categories cat ON fe.id_category = cat.cat_id

                      WHERE fe.is_active = 1

                      AND u.user_id = ".$user_id."

                      AND YEAR(fe.fe_date)=YEAR('".$date_search."')
                      AND MONTH(fe.fe_date)=MONTH('".$date_search."')

                      GROUP BY cat.cat_name
                      ORDER BY cat.cat_name ASC;
              ";
              //echo $sql .'<br>';
              $dbh = new Dbh();
              $stmt = $dbh->connect()->query($sql);

            // build the pie chart using database data
            ?>
            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
            <script type="text/javascript" src="https://www.google.com/jsapi"></script>
            <script type="text/javascript">
              // Load the Visualization API library and the piechart library.
              google.load('visualization', '1.0', {'packages':['corechart']});
              google.setOnLoadCallback(drawChart);
                //google.charts.setOnLoadCallback(drawChart);
                //alert("calling script");
                  function drawChart() {
                    //alert("calling drawchart");
                      // Create and populate the data table.
                      var data = google.visualization.arrayToDataTable([
                          ['Category', 'Spent'],
                          <?php
                          while( $row = $stmt->fetch() ){
                              extract($row);
                              //$add_comma = ',';
                            //if (!empty($row['fe_id'])) { $add_comma = ''; }
                              echo "['{$row['cat_name']}', {$row['fe_amount']}],";//.$add_comma;
                          }
                          ?>
                      ]);

                      // Create and draw the visualization.
                      var options = {
                        pieHole: 0.4,
                        backgroundColor: 'transparent',
                        pieSliceText: 'none',
                        legend: {
                          position: 'none',
                          textStyle: {
                            color: 'white',
                            fontSize: 13}}
                      };

                      var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
                      chart.draw(data, options);
                  }
              </script>
              <?php

            //echo '<div id="donutchart" style="width:100%; height:100%; background-color:transparent;"></div>'; //
            //echo '<br>';

          //echo '</div>';

          echo '<br>';
          /*
          echo '<div>'; // div for showing month's incomes, expenses and savings totals
            echo '<p style="text-align:center; background: rgb(33, 37, 46); border-right:2px solid rgb(33, 37, 46); border-top:2px solid rgb(33, 37, 46);">Month Totals</p>';

            echo '<table class="table table-dark" style="background-color:#3a5774; text-align:center;">';
                echo '<tr>';
                  echo '<th>Incomes</th>';
                  echo '<th>Expenses</th>';
                  echo '<th>Savings</th>';
                echo '</tr>';

                echo '<tr>';
                  echo '<td style="text-align:right; background-color:rgb(33, 37, 46);">$'.number_format($total_spent_amount, 2).'</td>';
                  echo '<td style="text-align:right; background-color:rgb(33, 37, 46);">$'.number_format($total_budget_amount, 2).'</td>';
                  $color = 'red';
                  if ($bud_diff > 0) { $color = 'green'; }
                  echo '<td style="text-align:right; background-color:rgb(33, 37, 46); color:'.$color.';">$'.number_format($total_over_under_amount, 2).'</td>';
                  echo '<td style="background:rgb(33, 37, 46);"></td>';
                echo '</tr>';

            echo '</table>';
          echo '</div>';
          */
        echo '</div>';  // end main div
      ?>

<?php
  $footer = new Footer();
  $footer->show_footer();
?>

</body>
</html>
