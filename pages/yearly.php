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
        // this is for looking at previous finance dates in the system
        $date_search = date('Y-m-d');
        if (isset($_POST['date_search'])) {
          $date_search = $_POST['date_search'];
        }

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
          echo '<h1 style="text-align:center;">Yearly Overview</h1>';
          $show_month_year_title = date('F', strtotime($date_search));
          //echo '<h2 style="text-align:center;">'.$show_month_year_title.'</h2>';
          // mini form for displaying different dates in history
          echo '<form method="post" action="../pages/yearly.php" style="text-align:center;">';
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

          echo '<div>';// div for yearly savings overview

            //echo '<h1 style="text-align:center;">Year</h1>';
            echo '<h2 style="text-align:center;">'.$this_year.'</h2>';

            echo '<table class="table table-dark" style="text-align:center;">'; // table where rows are incomes, expenses and savings, and columns are months
              echo '<tr>';
                echo '<th></th>';
                //foreach ($months_of_year as $month) {
                  //echo '<th>'.$month.'</th>';
                //}
                echo '<th>Incomes</th>';
                echo '<th>Expenses</th>';
                echo '<th>Savings</th>';
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

              //echo '</tr>';

              //$savings_total_string = '';
              $total_yearly_expenses = 0;
              $total_yearly_incomes = 0;
              $total_yearly_savings = 0;

              foreach ($months_of_year as $month) {
                $this_total = '~';
                $color = 'grey';

                echo '<tr>';
                  // month name
                  echo '<td style="color:grey; background:rgb(25, 29, 32);">'.$month.'</td>';
                  // incomes
                  if (array_key_exists($month, $income_monthly_totals)) {
                    $total_yearly_incomes += $income_monthly_totals[$month];

                    $this_total = '$'.$income_monthly_totals[$month];
                    $color = 'white';
                    echo '<td style="color:'.$color.'; background:rgb(25, 29, 32);">'.$this_total.'</td>';
                  } else {
                    echo '<td style="color:grey; background:rgb(25, 29, 32);">~</td>';
                  }
                  // expenses
                  if (array_key_exists($month, $expense_monthly_totals)) {
                    $add_bills_total = $total_history_bills + $expense_monthly_totals[$month];
                    $total_yearly_expenses += $add_bills_total;

                    $color = 'white';
                    if (array_key_exists($month, $income_monthly_totals)) {
                      $month_savings = ($income_monthly_totals[$month] - $add_bills_total);
                    } else {
                      $month_savings = 0 - $add_bills_total;
                    }
                    $total_yearly_savings += $month_savings;
                    // check if positive
                    $save_color = 'red';
                    if ($month_savings >= 0) {
                      $save_color = 'green';
                    }
                    //$savings_total_string .= '<td style="color:'.$save_color.';">$'.number_format($month_savings, 2).'</td>';
                    echo '<td style="color:white; background:rgb(25, 29, 32);">$'.number_format($add_bills_total, 2).'</td>';
                    echo '<td style="color:'.$save_color.'; background:rgb(25, 29, 32);">$'.number_format($month_savings, 2).'</td>';
                  } else {
                    //$savings_total_string .= '<td style="color:grey;">~</td>';
                    echo '<td style="color:grey; background:rgb(25, 29, 32);">~</td>';
                    echo '<td style="color:grey; background:rgb(25, 29, 32);">~</td>';
                  }
                echo '</tr>';
              }


              echo '<tr>';
                echo '<td style="color:grey;">(Yearly Totals)</td>';
                echo '<td>$'.number_format($total_yearly_incomes, 2).'</td>';
                echo '<td>$'.number_format($total_yearly_expenses, 2).'</td>';
                $save_color = 'red';
                if ($total_yearly_savings >= 0) {
                  $save_color = 'green';
                }
                echo '<td style="color:'.$save_color.';">$'.number_format($total_yearly_savings, 2).'</td>';
              echo '</tr>';


            echo '</table>';
          echo '</div>';

        echo '</div>';
      ?>

<?php
  $footer = new Footer();
  $footer->show_footer();
?>

</body>
</html>
