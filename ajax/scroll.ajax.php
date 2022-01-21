<?php
include '../includes/autoloader.inc.php';
include '../includes/function_library.inc.php';

// variables that we will always get
$current_page_num = $_GET['current_page_num'];
//$form_type = $_GET['form_type'];
$user_id = $_GET['user_id'];
$action = $_GET['action'];
$date_search = $_GET['date_search'];

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
  // here we want to show the results based on where the page number is
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
  //echo $sql."<br>";
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
    $show_limit = 5;                      // this limit variable is helpful for make next and previous eventually...
    $counter = 1;
    $additional_rows = 0;
    $num_rows = 0;
    while ($row = $stmt->fetch()) {
      $num_rows++;
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
          //$additional_rows++;
      }
      // always add to the total amount for all the rows
      $total_not_shown_expenses += (float)$row['fe_amount'];
      $counter++;
    }
    if ($num_rows == 0) {
      echo '<tr>';
        echo '<td colspan=5 style="text-align:center; color:grey; background-color:rgb(33, 37, 46);">(No more records)</td>';
      echo '</tr>';
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


}
show_results_table($user_id, $action, $current_page_num, $date_search);

//header("Location: ../pages/finances.php");
//exit();
