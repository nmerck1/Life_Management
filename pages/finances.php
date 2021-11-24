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
        echo '<table class="table table-dark">'; // for the entirety of the main three sections: incomes (left), overview (middle), expenses (right)
          echo '<tr>';


            echo '<td>';
              //echo '<div class="container">';

                echo '<h1 style="text-align:center;">Finances Overview</h1>';
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

                echo '<table class="table table-dark" style="background-color:#3a5774;">';
                echo '<tr>';
                  echo '<td style="background: rgb(33, 37, 46); border-right:2px solid rgb(33, 37, 46); border-top:2px solid rgb(33, 37, 46);">Incomes</td>';
                  echo '<td style="background: rgb(33, 37, 46); border-left:2px solid rgb(33, 37, 46); border-top:2px solid rgb(33, 37, 46);">Expenses</td>';
                echo '</tr>';
                echo '<tr>';
                  echo '<td style="border:2px solid rgb(33, 37, 46); padding:0px; margin:0px;">';
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
                          echo '<span style="display:flex;">';
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
                  echo '</td>';
                  echo '<td style="border:2px solid rgb(33, 37, 46); padding:0px; margin:0px;">';
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
                      echo '<th>Category</th>';
                      echo '<th>Name</th>';
                      echo '<th>Date</th>';
                      echo '<th>Amount</th>';
                      echo '<th style="background-color: rgb(33, 37, 46);">';
                        echo '<a href="../includes/finances.inc.php?form_type=Expense&user_id='.$user_id.'"><p class="bi-plus-circle" style="color:white;"></p></a>';
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
                      echo '<td colspan=4 style="text-align:left; background-color:rgb(33, 37, 46);">Total:</td>';
                      echo '<td style="text-align:right; background-color:rgb(33, 37, 46);">$'.number_format($total_expenses_amount, 2).'</td>';
                      echo '<td style="background:rgb(33, 37, 46);"></td>';
                    echo '</tr>';
                    echo '<tr>';
                      if ($additional_rows > 0) {
                        echo '<td colspan=4 style="text-align:left;"><i>('.$additional_rows.' more rows...)</i></td>';
                        echo '<td style="background:rgb(33, 37, 46);">($'.number_format($total_not_shown_expenses, 2).')</td>';
                        echo '<td style="background:rgb(33, 37, 46);"></td>';
                      }
                    echo '</tr>';
                    echo '</table>';
                  echo '</td>';
                echo '</tr>';

                echo '<tr>';
                  echo '<td style="color:grey; background: rgb(33, 37, 46); border:2px solid rgb(33, 37, 46);">Passive Incomes</td>';
                  echo '<td style="background: rgb(33, 37, 46); border:2px solid rgb(33, 37, 46);">This Month\'s Bills</td>';
                echo '</tr>';

                echo '<tr>';
                  echo '<td style="border-right:2px solid rgb(33, 37, 46); padding:0px; margin:0px;">';
                    $sql = "
                      SELECT *
                      FROM passive_incomes pi
                      LEFT JOIN users u ON pi.id_user = u.user_id
                      WHERE pi.is_active = 1
                      AND u.user_id = ".$user_id.";
                    ";
                    $dbh = new Dbh();
                    $stmt = $dbh->connect()->query($sql);
                    echo '<table class="greyedtable">';
                      echo '<tr>';
                        echo '<th>Name</th>';
                        echo '<th>Amount</th>';
                        echo '<th>Frequency</th>';
                        echo '<th style="background-color: rgb(33, 37, 46);">';
                          //echo '<a href="../includes/finances.inc.php?form_type=Passive&user_id='.$user_id.'"><p class="bi-plus-circle" style="color:white;"></p></a>';
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
                              echo '<a href="../includes/finances.inc.php?selected_id='.$row['pi_id'].'&update_type=Edit&form_type=Passive&user_id='.$user_id.'"><p class="bi-pencil-fill" style="color:white;"></p></a>';
                              echo '<a href="../ajax/finances.ajax.php?selected_id='.$row['pi_id'].'&update_type=Delete&form_type=Passive&user_id='.$user_id.'"><p class="bi-trash-fill" style="color:white;"></p></a>';
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
                            echo '<span style="display:flex;">';
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
                  echo '</td>';
                echo '</tr>';

                if ($id_role == 1) {
                    $sql = "
                        SELECT * FROM user_roles WHERE role_id = ".$id_role.";
                    ";
                    $dbh = new Dbh();
                    $stmt = $dbh->connect()->query($sql);

                    $role_color = '2px solid rgb(33, 37, 46)';  // default color for border
                    while ($row = $stmt->fetch()) {
                      $role_color = '2px solid '.$row['role_color'];
                    }
                  echo '<tr>';
                    echo '<td style="border:2px solid rgb(33, 37, 46); border:2px solid rgb(33, 37, 46);">Budget Categories</td>';
                    echo '<td style="border:2px solid rgb(33, 37, 46); border:2px solid rgb(33, 37, 46);">Category Spending</td>';
                  echo '</tr>';
                  echo '<tr>';
                    echo '<td style="border:'.$role_color.'; padding:0px; margin:0px;">';
                    // budgets names need to match the categories of expenses so that we can sum each expense category into a table //
                      $sql = "
                            SELECT b.bud_id,
                                  b.bud_name,
                                  b.bud_amount,
                                  b.bud_freq
                              FROM budgets b
                              LEFT JOIN users u ON b.id_user = u.user_id
                              WHERE b.is_active = 1
                              AND u.user_id = ".$user_id."

                              ORDER BY b.bud_name ASC;
                      ";
                      $dbh = new Dbh();
                      $stmt = $dbh->connect()->query($sql);
                      echo '<table class="table table-dark" style="background-color:#3a5774; border:'.$role_color.'; text-align:center;">';
                        echo '<tr>';
                          echo '<th>Name</th>';
                          echo '<th>Amount</th>';
                          echo '<th>Frequency</th>';
                          echo '<th style="background-color: rgb(33, 37, 46);">';
                            echo '<a href="../includes/finances.inc.php?form_type=Budget&user_id='.$user_id.'"><p class="bi-plus-circle" style="color:white;"></p></a>';
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
                                echo '<a href="../includes/finances.inc.php?selected_id='.$row['bud_id'].'&update_type=Edit&form_type=Budget&user_id='.$user_id.'"><p class="bi-pencil-fill" style="color:white;"></p></a>';
                                echo '<a href="../ajax/finances.ajax.php?selected_id='.$row['bud_id'].'&update_type=Delete&form_type=Budget&user_id='.$user_id.'"><p class="bi-trash-fill" style="color:white;"></p></a>';
                              echo '</span>';
                            echo '</td>';
                          echo '</tr>';
                          // get variables for savings:
                          $total_budgets_amount += (float)$row['bud_amount'];
                        }
                        //var_dump($cat_budgets);
                        echo '<tr>';
                          echo '<td colspan=2 style="text-align:left; background-color:rgb(33, 37, 46);">Total:</td>';
                          echo '<td style="text-align:right; background-color:rgb(33, 37, 46);">$'.number_format($total_budgets_amount, 2).'</td>';
                          echo '<td style="background:rgb(33, 37, 46);"></td>';
                        echo '</tr>';
                      echo '</table>';
                    echo '</td>';
                    echo '<td style="border:2px solid rgb(33, 37, 46); padding:0px; margin:0px;">';
                      $sql = "
                              SELECT fe.fe_id,
                                  fe.id_category,
                                  cat.cat_name,
                                  SUM(fe.fe_amount) AS 'fe_amount',
                                  fe.fe_date
                              FROM finance_expenses fe
                              LEFT JOIN categories cat ON fe.id_category = cat.cat_id
                              LEFT JOIN users u ON fe.id_user = u.user_id
                              WHERE fe.is_active = 1
                              AND u.user_id = ".$user_id."
                              AND MONTH(fe.fe_date)=MONTH('".$date_search."')
                              AND YEAR(fe.fe_date)=YEAR('".$date_search."')

                              GROUP BY fe.id_category
                              ORDER BY cat.cat_name ASC;
                      ";
                      $dbh = new Dbh();
                      $stmt = $dbh->connect()->query($sql);
                      //$count = $stmt->fetchColumn();
                      echo '<table class="table table-dark" style="background-color:#3a5774; border:'.$role_color.'; text-align:center;">';
                      echo '<tr>';
                        echo '<th>Category</th>';
                        echo '<th>Amount</th>';
                      //  echo '<th>Over Scale</th>';
                      echo '</tr>';

                      //var_dump($cat_budgets);
                      $counter = 0;
                      $total_budget_over = 0;
                      //$added = false;
                      while ($row = $stmt->fetch()) {
                        //echo "counter: ".$counter++."<br>";
                        // find the matching category with this name in category array
                        //$key = $row['fe_category'];
                        //$result = isset($array[$key]) ? $array[$key] : null;
                        //echo 'category: '. $row['fe_category'] .'<br>';
                        if (count($cat_budgets) > 0) {
                          $find_budget = $cat_budgets[$row['cat_name']];
                          //echo 'find_budget: '.$find_budget.'<br>';
                          // default color
                          $color = 'green';
                          if (array_key_exists($row['cat_name'], $cat_budgets)) {
                            // create more exact scale colors
                            $get_amount = $row['fe_amount'];                            // 52.41

                            $get_budget =        $find_budget;
                            $get_double_budget = $find_budget * 2;                      // 104.82
                            $get_half_budget =   $find_budget + ($find_budget / 2);   // 26.21

                            //echo "get_budget: ".$get_budget.'<br>';
                            //echo "get_half_budget: ".$get_half_budget.'<br>';
                            //echo "get_double_budget: ".$get_double_budget.'<br><br>';

                            if($get_amount >= $get_double_budget) {
                              $color = 'red';
                            } elseif($get_amount >= $get_half_budget) {
                              $color = 'orange';
                            } elseif($get_amount > $get_budget) {
                              $color = 'yellow';
                            } else {
                              $color = 'green';
                            }

                            $bud_diff = ($get_amount - $get_budget);
                            if ($bud_diff < 0) {
                              $bud_diff = 0;
                            }
                            $total_budget_over += $bud_diff;
                          }

                          echo '<tr>';
                            echo '<td style="background:rgb(25, 29, 32);">' .$row['cat_name']. '</td>';
                            echo '<td style="text-align:right; background:rgb(25, 29, 32); color:'.$color.';">' .number_format((float)$row['fe_amount'], 2). '</td>';
                            //if ($added == false){
                              //echo "count: ".$count++."<br>";
                            //  $added = true;
                              //  echo '<td rowspan='.$counter.' style="text-align:center; background:rgb(25, 29, 32); color:'.$color.';">hello</td>';
                            //  }
                          echo '</tr>';
                          $counter++;
                        }

                      }
                      // check if budget is over at all:
                      $color = 'green';
                      if ($total_budget_over > 0){
                        $color = 'red';
                      }
                      echo '<tr>';
                        echo '<td style="text-align:left; background-color:rgb(33, 37, 46);">Total Amount Over:</td>';
                        echo '<td style="text-align:right; background-color:rgb(33, 37, 46); color:'.$color.';">$'.number_format($total_budget_over, 2).'</td>';
                      echo '</tr>';

                      echo '</table>';
                    echo '</td>';
                  echo '</tr>';
                }


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
                        if ($net_savings < 0.00) {
                          $color = 'red';
                        }
                        echo '<td colspan=2 style="text-align:right; background:rgb(25, 29, 32);">$' .number_format($total_incomes_amount, 2). '</td>';
                        echo '<td colspan=2 style="text-align:right; background:rgb(25, 29, 32); color:'.$color.';">$' .number_format($net_savings, 2). '</td>';
                        //echo '<td style="text-align:right; background:rgb(25, 29, 32);">$' .number_format($total_incomes_amount*12, 2). '</td>';
                        //echo '<td style="text-align:right; background:rgb(25, 29, 32); color:green;">$' .number_format($net_savings*12, 2). '</td>';
                      echo '</tr>';
                    echo '</table>';

                    echo '<tr>';
                      echo '<td colspan=2 style="text-align:center;"><h1>Yearly Overview</h1></td>';
                    echo '</tr>';
                    echo '<tr>';
                      echo '<td colspan=2 style="text-align:center;"><h2>'.$this_year.'</h2></td>';
                    echo '</tr>';

                    echo '<tr>';
                      echo '<td colspan=2>';
                        $savings_total_string = '';
                        echo '<table style="width:100%;">'; // table where rows are incomes, expenses and savings, and columns are months
                          echo '<tr>';
                            echo '<th style="background:rgb(47, 115, 152);"></th>';
                            foreach ($months_of_year as $month) {
                              echo '<th>'.$month.'</th>';
                            }
                          echo '</tr>';
                          echo '<tr>';
                            echo '<td style="background:rgb(25, 29, 32);">Incomes</td>';
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
                            //echo $sql;
                            $dbh = new Dbh();
                            $stmt = $dbh->connect()->query($sql);

                            while ($row = $stmt->fetch()) {
                              $fi_date = strtotime($row['fi_date']);
                              $get_formatted_date = date('M', $fi_date);

                              $income_monthly_totals[$get_formatted_date] = $row['fi_amount'];
                            }

                            foreach ($months_of_year as $month) {
                                $this_total = '~';
                                $color = 'grey';
                                //echo "month: ".$month."<br>";
                                //echo "get_dates: ".$get_dates[$counter]."<br>";
                                foreach($income_monthly_totals as $each_month => $total) {
                                  //echo "each_month: ".$each_month."<br>";
                                  if ($month == $each_month) {
                                    //echo "picked! <br>";
                                    $this_total = '$'.$total;
                                    $color = 'white';
                                    break;
                                  }
                                }
                                echo '<td style="color:'.$color.';">'.$this_total.'</td>';

                            }
                          echo '</tr>';

                          echo '<tr>';
                            echo '<td style="background:rgb(25, 29, 32);">Expenses</td>';
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
                            //echo $sql;
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
                            $dbh = new Dbh();
                            $stmt = $dbh->connect()->query($sql);

                            while ($row = $stmt->fetch()) {
                              $fe_date = strtotime($row['fe_date']);
                              $get_formatted_date = date('M', $fe_date);

                              $expense_monthly_totals[$get_formatted_date] = $row['fe_amount'];
                            }

                            foreach ($months_of_year as $month) {
                                $this_total = '~';
                                $color = 'grey';

                                //$income_counter = 0;
                                foreach($expense_monthly_totals as $each_month => $total) {
                                  //echo "each_month: ".$each_month."<br>";
                                  if ($month == $each_month) {
                                    //echo "picked! <br>";
                                    $add_bills_total = $total_history_bills + $total;
                                    $this_total = '$'.$add_bills_total;
                                    $color = 'white';
                                    // calculate savings for savings row in this table
                                    //echo '$income_monthly_totals[$each_month]: '.$income_monthly_totals[$each_month]."<br>";
                                  //  var_dump($income_monthly_totals);
                                    //var_dump($expense_monthly_totals);
                                    if (count($expense_monthly_totals) != 0 && count($income_monthly_totals) != 0){
                                      if ($income_monthly_totals[$each_month] != null && $income_monthly_totals[$each_month] != '') {
                                        $month_savings = ($income_monthly_totals[$each_month] - $add_bills_total);
                                        // check if positive
                                        $save_color = 'red';
                                        if ($month_savings >= 0) {
                                          $save_color = 'green';
                                        }
                                        $savings_total_string .= '<td style="color:'.$save_color.';">$'.number_format($month_savings, 2).'</td>';
                                      }
                                    } else {
                                      $save_color = 'green';
                                      $savings_total_string .= '<td style="color:'.$save_color.';">$0.00</td>';
                                    }

                                    //break;
                                  } else {
                                    $savings_total_string .= '<td style="color:grey;">~</td>';
                                  }
                                  //$income_counter++;
                                }
                                echo '<td style="color:'.$color.';">'.$this_total.'</td>';

                            }
                          echo '</tr>';

                          echo '<tr>';
                            echo '<td style="background:rgb(25, 29, 32);">Savings</td>';
                            echo $savings_total_string;
                          echo '</tr>';


                        echo '</table>';
                      echo '</td>';





                      /*
                      echo '<td style="background:rgb(25, 29, 32); text-align:center;">';
                        // loop through each month of the year for this year and display the amount for each month

                        echo '<h2>Incomes</h2>';
                        echo '<h2>'.$this_year.'</h2>';
                        echo '<table class="table table-dark">'; // mini table to display months

                        $monthly_totals = array();

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
                        //echo $sql;
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

                      echo '<td style="background:rgb(25, 29, 32); text-align:center;">';

                        echo '<h2>Expenses</h2>';
                        echo '<h2>'.$this_year.'</h2>';
                        echo '<table class="table table-dark">'; // mini table to display months
                        // sql for getting the bill logs for the correct times in history to show the correct expenses
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


                        $monthly_totals = array();
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
                      */
                    echo '</tr>';


                  echo '</td>';
                echo '</tr>';
                echo '</table>';  // the table for the two main rows for incomes & expenses

            //  echo '</div>';  // the div ending for the main section for displaying overview

            //echo '</td>';
          //echo '</tr>';

          //echo '<tr>';


          echo '</tr>';


        echo '</table>';
        echo '</div>';
      ?>

<?php
  $footer = new Footer();
  $footer->show_footer();
?>

</body>
</html>
