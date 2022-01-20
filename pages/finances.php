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
          echo '<h1 style="text-align:center;">Monthly Overview</h1>';
          $show_month_year_title = date('F', strtotime($date_search));
          echo '<h2 style="text-align:center;">'.$show_month_year_title.'</h2>';
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

          echo '<div>'; // div for incomes
            echo '<p style="text-align:center; background: rgb(33, 37, 46); border-right:2px solid rgb(33, 37, 46); border-top:2px solid rgb(33, 37, 46);">Incomes</p>';
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
            echo '<table class="table table-dark" style="background-color:#3a5774; text-align:center;">';
            echo '<tr>';
              echo '<th>Company</th>';
              echo '<th>Name</th>';
              echo '<th>Date</th>';
              echo '<th>Amount</th>';
              echo '<th style="background-color: rgb(33, 37, 46);">';
                echo '<a href="../includes/finances.inc.php?form_type=Income&user_id='.$user_id.'"><p class="bi-plus-circle" style="color:white;"></p></a>';
              echo '</th>';
            echo '</tr>';
              $total_incomes_amount = 0;
              while ($row = $stmt->fetch()) {
                echo '<tr>';
                  echo '<td style="background:rgb(25, 29, 32); color:grey;">' .$row['fi_company']. '</td>';
                  echo '<td style="background:rgb(25, 29, 32);">' .$row['fi_name']. '</td>';
                  $date_string = strtotime($row['fi_date']);
                  echo '<td style="background:rgb(25, 29, 32); color:grey;">' .date('M, d', $date_string). '</td>';
                  echo '<td style="text-align:right; background:rgb(25, 29, 32);">' .number_format((float)$row['fi_amount'], 2). '</td>';
                  echo '<td style="background:rgb(33, 37, 46);">';
                    echo '<span>'; //style="display:flex;"
                      echo '<a href="../includes/finances.inc.php?selected_id='.$row['fi_id'].'&update_type=Edit&form_type=Income&user_id='.$user_id.'"><p class="bi-pencil-fill" style="color:white;"></p></a>';
                      echo '<a href="../ajax/finances.ajax.php?selected_id='.$row['fi_id'].'&update_type=Delete&form_type=Income&user_id='.$user_id.'"><p class="bi-trash-fill" style="color:white;"></p></a>';
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
          echo '</div>';

          echo '<div>';// div for expenses
          echo '<p style="text-align:center; background: rgb(33, 37, 46); border-right:2px solid rgb(33, 37, 46); border-top:2px solid rgb(33, 37, 46);">Expenses</p>';
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

                    ORDER BY fe.fe_date DESC;
                    #LIMIT 5;
            ";
            $dbh = new Dbh();
            $stmt = $dbh->connect()->query($sql);
            echo '<table class="table table-dark" style="background-color:#3a5774; text-align:center;">';
              echo '<tr>';
                echo '<th>Company</th>';
                //echo '<th>Category</th>';
                echo '<th>Name</th>';
                echo '<th>Date</th>';
                echo '<th>Amount</th>';
                echo '<th style="background-color: rgb(33, 37, 46);">';
                  echo '<a href="../includes/finances.inc.php?form_type=Expense&user_id='.$user_id.'"><p class="bi-plus-circle" style="color:white;"></p></a>';
                echo '</th>';
              echo '</tr>';
              $total_expenses_amount = 0;
              $total_not_shown_expenses = 0;
              $show_limit = 10;                      // this limit variable is helpful for make next and previous eventually...
              $counter = 1;
              $additional_rows = 0;
              while ($row = $stmt->fetch()) {
                if ($counter <= $show_limit){
                  echo '<tr>';
                    echo '<td style="background:rgb(25, 29, 32); color:grey;">' .$row['fe_company']. '</td>';
                    //echo '<td style="background:rgb(25, 29, 32); color:grey;">' .$row['cat_name']. '</td>';
                    echo '<td style="background:rgb(25, 29, 32);">' .$row['fe_name']. '</td>';
                    $date_string = strtotime($row['fe_date']);
                    echo '<td style="background:rgb(25, 29, 32); color:grey;">' .date('M, d', $date_string). '</td>';
                    echo '<td style="text-align:right; background:rgb(25, 29, 32);">' .number_format((float)$row['fe_amount'], 2). '</td>';
                    echo '<td style="background:rgb(33, 37, 46);">';
                      echo '<span>'; //style="display:flex;"
                        echo '<a href="../includes/finances.inc.php?selected_id='.$row['fe_id'].'&update_type=Edit&form_type=Expense&user_id='.$user_id.'"><p class="bi-pencil-fill" style="color:white;"></p></a>';
                        echo '<a href="../ajax/finances.ajax.php?selected_id='.$row['fe_id'].'&update_type=Delete&form_type=Expense&user_id='.$user_id.'"><p class="bi-trash-fill" style="color:white;"></p></a>';
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
                echo '<td colspan=4 style="text-align:left; background-color:rgb(33, 37, 46);">Total: <p style="float:right;">$'.number_format($total_expenses_amount, 2).'</p></td>';
                //echo '<td style="text-align:right; background-color:rgb(33, 37, 46);">$'.number_format($total_expenses_amount, 2).'</td>';
                echo '<td style="background:rgb(33, 37, 46);"></td>';
              echo '</tr>';
              echo '<tr>';
                if ($additional_rows > 0) {
                  echo '<td colspan=4 style="text-align:left;"><i>('.$additional_rows.' more rows...)</i> <p style="float:right;">($'.number_format($total_not_shown_expenses, 2).')</p></td>';
                  //echo '<td style="background:rgb(33, 37, 46);">($'.number_format($total_not_shown_expenses, 2).')</td>';
                  echo '<td style="background:rgb(33, 37, 46);"></td>';
                }
              echo '</tr>';
            echo '</table>';
          echo '</div>';

          echo '<div>'; // div for bills
            echo '<p style="text-align:center; background: rgb(33, 37, 46); border-right:2px solid rgb(33, 37, 46); border-top:2px solid rgb(33, 37, 46);">Bills</p>';
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
            echo '<table class="table table-dark" style="background-color:#3a5774; text-align:center;">';
              echo '<tr>';
                echo '<th>Name</th>';
                echo '<th>Amount</th>';
                echo '<th>Frequency</th>';
                echo '<th style="background-color: rgb(33, 37, 46);">';
                  echo '<a href="../includes/finances.inc.php?form_type=Bill&user_id='.$user_id.'"><p class="bi-plus-circle" style="color:white;"></p></a>';
                echo '</th>';
              echo '</tr>';
              $total_bills_amount = 0;
              while ($row = $stmt->fetch()) {
                echo '<tr>';
                  echo '<td style="background:rgb(25, 29, 32);">' .$row['bill_name']. '</td>';
                  echo '<td style="text-align:right; background:rgb(25, 29, 32);">' .number_format((float)$row['bl_amount'], 2). '</td>';
                  echo '<td style="background:rgb(25, 29, 32); color:grey;">' .$row['bill_freq']. '</td>';
                  echo '<td style="background:rgb(33, 37, 46);">';
                    echo '<span>'; //style="display:flex;"
                      echo '<a href="../includes/finances.inc.php?selected_id='.$row['bill_id'].'&update_type=Edit&form_type=Bill&user_id='.$user_id.'"><p class="bi-pencil-fill" style="color:white;"></p></a>';
                      echo '<a href="../ajax/finances.ajax.php?selected_id='.$row['bill_id'].'&update_type=Delete&form_type=Bill&user_id='.$user_id.'"><p class="bi-trash-fill" style="color:white;"></p></a>';
                    echo '</span>';
                  echo '</td>';
                echo '</tr>';
                // get variables for savings:
                $total_bills_amount += (float)$row['bl_amount'];
                //echo "total_bills_amount: ".$total_bills_amount."<br>";
              }
              echo '<tr>';
                echo '<td colspan=2 style="text-align:left; background-color:rgb(33, 37, 46);">Total:</td>';
                echo '<td style="text-align:right; background-color:rgb(33, 37, 46);">$'.number_format($total_bills_amount, 2).'</td>';
                echo '<td style="background:rgb(33, 37, 46);"></td>';
              echo '</tr>';
            echo '</table>';
          echo '</div>';

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
                    echo '<td style="text-align:right; background:rgb(25, 29, 32);">$' .number_format($row['fe_amount'], 2). '</td>';
                  echo '</tr>';
                }


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
