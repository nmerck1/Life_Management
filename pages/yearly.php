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
	function scroll_years(next_prev_num){
      // setup the ajax request
  		var xhttp = new XMLHttpRequest();
      // get variables from inputs below:
  		var current_year_num = document.getElementById('current_year_num');
      var user_id = document.getElementById('user_id');
      var date_search = document.getElementById('date_search');
      var table_scroll = 'Yearly'

  		var action = 'Next';
      if (next_prev_num == 0) {
        action = 'Prev';
      }

      var can_scroll = true;
      if (action == 'Prev') {
        if (current_year_num.innerHTML == '2019') { // I first made the life management system back in 2019
          can_scroll = false;
        }
      }

      if ( can_scroll == true ) {
        // create link to send GET variables through
        var query_string = "../ajax/scroll.ajax.php";
        query_string += "?current_num=" + current_year_num.innerHTML;
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
      echo '<p id="user_id" style="display:none;" value="'.$user_id.'">'.$user_id.'</p>';
        // this is for looking at previous finance dates in the system
        $date_search = date('Y-m-d');
        if (isset($_POST['date_search'])) {
          $date_search = $_POST['date_search'];
        }

        $this_year = date('Y', strtotime($date_search));
        $first_month = date('Y-m-d', strtotime('first day of January'.$this_year));
        $last_day_of_year = date('Y-m-d', strtotime('12/31'));
        $this_month = date("M");
        //echo "this_month: ".$this_month."<br><br>";
        //echo "next_year: ".$next_year."<br>";
        //echo "month: ".$month."<br>";
        $months_of_year = array();
        $full_name_months = array();
        for ($i = 0; $i < 12; $i++) {
             // echo date('F Y', $month);
             $next_month = strtotime("+".$i." month", strtotime($first_month));
             $show_month = date('M', $next_month);
             $show_full = date('F', $next_month);
             //echo "month: ".$show_month."<br>";
             array_push($months_of_year, $show_month);
             array_push($full_name_months, $show_full);
        }

        //var_dump($months_of_year);
        // start the outer table
        echo '<div class="container">';
          echo '<h1 style="text-align:center;">Yearly Overview</h1>';
          $show_month_year_title = date('F', strtotime($date_search));
          //echo '<h2 style="text-align:center;">'.$show_month_year_title.'</h2>';
          // mini form for displaying different dates in history
          //echo '<form method="post" action="../pages/yearly.php" style="text-align:center;">';
            //echo '<select>';
            //foreach ($months_of_year as $month) {
            //  echo '<option></option>';
            //}
            //echo '</select>';
            //echo $date_search;
            //$date = date('Y-m-d');	// default to today
            //echo '<input type="date" name="date_search" value="'.$date_search.'"></input>';

            //echo '<button type="submit" name="submit_search" class="btn btn-primary btn-sm" value="Display">Display Date</button>';
          //echo '</form>';

          echo '<br>';
          /*
          echo '<div>';// div for category spending
            echo '<p style="text-align:center; background: rgb(33, 37, 46); border-right:2px solid rgb(33, 37, 46); border-top:2px solid rgb(33, 37, 46);">Category Spending</p>';
            echo '<table class="table table-dark" style="background-color:#3a5774; text-align:center;">';
                echo '<tr>';
                  echo '<th>Category</th>';
                  echo '<th>Amount</th>';
                  //echo '<th>Gross Yearly</th>';
                  //echo '<th>Net Yearly</th>';
                echo '</tr>';
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

                while ($row = $stmt->fetch()) {
                  echo '<tr>';
                    echo '<td style="background:rgb(25, 29, 32);">' .$row['cat_name']. '</td>';
                    echo '<td style="text-align:right; background:rgb(25, 29, 32);">$' .number_format(($row['fe_amount'] * 12), 2). '</td>';
                  echo '</tr>';
                }


            echo '</table>';
          echo '</div>';
          */

          echo '<br>';

          echo '<div class="div_element_block">';// div for yearly savings overview
          //  echo '<h2 style="text-align:center;">'.$this_year.'</h2>';
            echo '<p style="width:100%; margin:0px; text-align:center;">';
              echo '<button name="prev_button" onclick="scroll_years(0);" style="float:left; background:none; border:none; font-size:20px; height:32px;">';
                echo '<i class="actions"><p class="bi-arrow-left-square"></p></i>'; // -box-arrow-left
              echo '</button>';
              echo '<button name="next_button" onclick="scroll_years(1);" style="float:right; background:none; border:none; font-size:20px; height:32px;">';
                echo '<i class="actions"><p class="bi-arrow-right-square"></p></i>';
              echo '</button>';
            echo '</p>';


            echo '<div id="scroll_div">';
              echo '<h3 id="current_year_num" style="text-align:center;" value="'.$this_year.'">'.$this_year.'</h3>'; //style="display:none;"
              echo '<p id="date_search" style="display:none;" value="'.$date_search.'">'.$date_search.'</p>';

              echo '<table class="table table-dark" style="text-align:center;">'; // table where rows are incomes, expenses and savings, and columns are months
                echo '<tr>';
                  echo '<th></th>';
                  //foreach ($months_of_year as $month) {
                    //echo '<th>'.$month.'</th>';
                  //}
                  echo '<th><i class="bi-plus-square"></i></th>'; // Incomes
                  echo '<th><i class="bi-dash-square"></i></th>'; // Expenses
                  echo '<th><i class="bi-currency-dollar"></i></th>'; // Savings // <i class="bi-piggy-bank"></i>
                  echo '<th><i class="bi-currency-dollar"></i> (+Loans)</th>'; // <i class="bi-piggy-bank"></i>
                echo '</tr>';
                //echo '<tr>';

                /////////////////////////////////////////////////////////////////////////////////////////////////////
                // This is the variable that is collecting data from these queries and organizing it into a string //
                $year_data_string = "";
                /////////////////////////////////////////////////////////////////////////////////////////////////////

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
                /*
                foreach ($months_of_year as $month) {
                    $this_total = '~';
                    $color = 'grey';

                    if (array_key_exists($month, $income_monthly_totals)) {
                      $this_total = '$'.$income_monthly_totals[$month];
                      $color = 'white';
                      echo '<td style="color:'.$color.';">'.$this_total.'</td>';
                    } else {
                      echo '<td style="color:grey;">~</td>';
                    }
                }
                */
            //  echo '</tr>';

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


                /*
                // expenses
                foreach ($months_of_year as $month) {
                  $this_total = '~';
                  $color = 'grey';

                  if (array_key_exists($month, $expense_monthly_totals)) {
                    $add_bills_total = $total_history_bills + $expense_monthly_totals[$month];
                    $color = 'white';
                    if (array_key_exists($month, $income_monthly_totals)) {
                      $month_savings = ($income_monthly_totals[$month] - $add_bills_total);
                    } else {
                      $month_savings = 0 - $add_bills_total;
                    }

                    // check if positive
                    $save_color = 'red';
                    if ($month_savings >= 0) {
                      $save_color = 'green';
                    }
                    $savings_total_string .= '<td style="color:'.$save_color.';">$'.number_format($month_savings, 2).'</td>';
                    echo '<td style="color:'.$color.';">'.$add_bills_total.'</td>';
                  } else {
                    $savings_total_string .= '<td style="color:grey;">~</td>';
                    echo '<td style="color:grey;">~</td>';
                  }

                }
              */
              //  get the total savings if all your loans are paid off
              $sql = "
                      SELECT SUM(i.iou_amount_owed) AS 'amount_owed',
                             SUM(i.iou_amount_paid) AS 'amount_paid',
                             i.iou_loaner_id,
                             i.iou_is_active
                      FROM ious i

                      WHERE i.iou_is_active = 1
                      AND i.iou_loaner_id = ".$user_id."
                      AND YEAR(i.iou_owe_date)=YEAR('".$date_search."')
                      AND i.iou_is_paid_off = 0;
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





              //echo '</tr>';

              //$savings_total_string = '';
              $total_yearly_expenses = 0;
              $total_yearly_incomes = 0;
              $total_yearly_savings = 0;
              $total_yearly_loan_savings = 0;
              $is_alternate_row = false;
              $add_alternating_class = '';
              $end_year_data = false;
              foreach ($months_of_year as $month) {
                if ($end_year_data == false) {
                  $year_data_string .= "['".$month."', ";
                }

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
                    if ($end_year_data == false) {
                      $year_data_string .= $this_total .", ";
                    }
                  } else {
                    echo '<td '.$add_alternating_class.' style="color:grey;">~</td>';
                    if ($end_year_data == false) {
                      $year_data_string .= "0, ";
                    }
                  }
                  // expenses
                  if (array_key_exists($month, $expense_monthly_totals)) {
                    $add_bills_total = $total_history_bills + $expense_monthly_totals[$month];
                    $total_yearly_expenses += $add_bills_total;

                    if (array_key_exists($month, $income_monthly_totals)) {
                      $month_savings = ($income_monthly_totals[$month] - $add_bills_total);
                    } else {
                      $month_savings = (0 - $add_bills_total);
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

                    if ($end_year_data == false) {
                      $year_data_string .= $add_bills_total .", ". $month_savings ."], ";
                    }
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
                    if ($end_year_data == false) {
                      $year_data_string .= "0, 0], ";
                    }
                  }
                echo '</tr>';
                if ($this_month == $month) {
                  $end_year_data = true;  // next loop iteration will not add values to next months in graph string creation
                }
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
          echo '</div>';
          echo '</div>';
        echo '</div>';

        echo '<br>';


        // here we are going to retrieve data from database to show each months incomes, expenses and savings for each month on a line chart
        //echo "incomes: ". var_dump($income_monthly_totals)."<br>";
        $income_data_points = array();
        //echo "incomes: ". $savings."<br>";
        //for ($i=0; $i<count($expense_monthly_totals); $i++) {
        $counter = 0;
        foreach ($income_monthly_totals as $month=>$total) {
          if ($total != NULL) {
            array_push($income_data_points, array("y" => $total, "label" => $month));
          }
          $counter++;
        }
      //  var_dump($income_data_points);

        //echo "expenses: ". var_dump($expense_monthly_totals)."<br>";
        // array for data points
        $expense_months = array();
        $expense_totals = array();
        //echo "incomes: ". $savings."<br>";
        //for ($i=0; $i<count($expense_monthly_totals); $i++) {
        $counter = 0;
        foreach ($expense_monthly_totals as $month=>$total) {
          if ($total != NULL) {
            array_push($expense_months, $month);
            array_push($expense_totals, $total);
          }
          $counter++;
        }
        //var_dump($expense_months);
        //var_dump($expense_totals);

        if ($id_role == 1){
          echo $year_data_string;
          // the new library method to generate the graph
          library_year_line_graph($year_data_string);



          // here we get all the current active categories to loop through each one and get each month totals for the year
          $cat_monthly_data_string = "";
          $cat_title_data_string = "['Month', ";  // categories get added to this each time
          $cat_names_array = array();
          $dont_add = false;

          // for each month, get each category and sum the amounts for each month
          foreach ($full_name_months as $month) {

            $sql_cat = "
              SELECT * FROM categories WHERE is_active = 1;
            ";
            //echo $sql_cat.'<br>';
            $dbh = new Dbh();
            $stmt_cat = $dbh->connect()->query($sql_cat);

            $cat_monthly_data_string .= "['" .$month. "', ";

            while ($row = $stmt_cat->fetch()) {
              //echo "cat: ". $row['cat_id'] ."<br>";
              //echo "cat name: ". $row['cat_name'] ."<br>";
              //echo "month: ". $month ."<br>";
              $cat_name = $row['cat_name'];

              if ($dont_add == false) {
                array_push($cat_names_array, $cat_name);
              }

              //$cat_title_data_string .= $cat_name. ", ";

              // for each cat, get each month total
              $sql_month_cat = "
                    SELECT
                           c.cat_name AS 'cat_name',
                           SUM(fe.fe_amount) AS 'fe_amount',
                           fe.fe_date,
                           c.cat_id
                    FROM finance_expenses fe
                    LEFT JOIN users u ON fe.id_user = u.user_id
                    LEFT JOIN categories c ON fe.id_category = c.cat_id

                    WHERE fe.is_active = 1
                    AND u.user_id = ".$user_id."

                    AND MONTHNAME(fe.fe_date) = '".$month."'
                    AND YEAR(fe.fe_date) = YEAR('".$last_day_of_year."')
                    AND c.cat_id = ".$row['cat_id']."

                    GROUP BY c.cat_name, MONTH(fe.fe_date)
                    ORDER BY fe.fe_date ASC;
              ";
              //echo $sql_month_cat.'<br>';
              $dbh = new Dbh();
              $stmt_month_cat = $dbh->connect()->query($sql_month_cat);

              if ($result = mysqli_query($conn, $sql_month_cat)) {
                // Return the number of rows in result set
                $num_rows = mysqli_num_rows( $result );
                //echo "num_rows: ".$num_rows."<br>";
                if ($num_rows == 0) {
                  $cat_monthly_data_string .= "0, ";
                }
              }
              while ($row = $stmt_month_cat->fetch()) {
                $expense_date = strtotime($row['fe_date']);
                $get_formatted_date = date('M', $expense_date);

                if ($row['fe_amount'] == 0 || $row['fe_amount'] == null) {
                  $cat_monthly_data_string .= "0, ";
                } else {
                  $cat_monthly_data_string .= $row['fe_amount'].", ";
                }



                //$expense_monthly_totals[$get_formatted_date] = $row['fe_amount'];
              }
              //
            }
            $dont_add = true;
            $cat_monthly_data_string .= "], ";
          }
          //$cat_monthly_data_string .= "], ";

          for ($i = 0; $i < count($cat_names_array); $i++){
            //echo "cat_names_array: ". $cat_names_array[$i]."<br>";
            $cat_title_data_string .= "'" .str_replace(' ', '_', $cat_names_array[$i]) ."', ";
          }
          $cat_title_data_string .= "], ";

          //echo "cat_title_data_string: ".$cat_title_data_string;
          //echo "cat_monthly_data_string: ".$cat_monthly_data_string;
          library_line_month_cat_graph($cat_title_data_string, $cat_monthly_data_string);


        }

      ?>

<?php
  $footer = new Footer();
  $footer->show_footer();
?>

</body>
</html>
