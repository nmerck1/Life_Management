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
  $finance_nav->show_header_nav('Monthly');
?>

<script type="text/javascript">
	function scroll_table(next_prev_num, table_scroll){
      //alert("table: " + table_scroll);
      // setup the ajax request
  		var xhttp = new XMLHttpRequest();
      // get variables from inputs below:
  		var current_page_num = document.getElementById(table_scroll + '_current_page_num');
      if (table_scroll == 'DetailedCat') {// one exception
        var current_cat_id = document.getElementById(table_scroll + '_current_cat_id');
      }
      if (table_scroll == 'DetailedComp') {// one exception
        var current_comp_id = document.getElementById(table_scroll + '_current_comp_id');
      }
      var user_id = document.getElementById('user_id');
      var date_search = document.getElementById('date_search');
      var show_per_page = 5;
      var scroll_div_name = table_scroll + "_scroll_div";

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
        query_string += "&user_id=" + user_id.innerHTML;
        query_string += "&action=" + action;
        query_string += "&date_search=" + date_search.innerHTML;
        query_string += "&table_scroll=" + table_scroll;
        query_string += "&show_per_page=" + show_per_page;
        if (table_scroll == 'DetailedCat') {// one exception
          query_string += "&cat_id=" + current_cat_id.innerHTML;
        }
        if (table_scroll == 'DetailedComp') {// one exception
          query_string += "&comp_id=" + current_comp_id.innerHTML;
        }

        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
           document.getElementById(scroll_div_name).innerHTML = this.responseText;
          }
        };
        xhttp.open("GET", query_string, true);
        xhttp.send();

        // when the data is returned after ajax, it redirects back to inventory
        //window.location = "../pages/finances.php";
      }
	}

  // this is where we scroll through each month's incomes, expenses, category spending all at once with arrow buttons //
  function scroll_month(next_prev_value, current_date_search, table_scroll) {
    // setup the ajax request
    var xhttp = new XMLHttpRequest();
    // get variables from inputs below:
    //var date_search = document.getElementById('date_search');
    var dateVar = new Date(date_search.innerHTML);
    //alert("date search inner HTML: " + date_search.innerHTML);

    var current_page_num = 0;
    var user_id = document.getElementById('user_id');
    var show_per_page = 0;


    var action = 'Next';
    if (next_prev_value == 0) {
      action = 'Prev';
    }
    // remove extra zeros from date since we are counting hours, minutes and seconds...
    //var date_num = dateVar.getTime();
    //var date_num_string = date_num.toString();
    //var to_remove = date_num_string.length - 3;
    //date_num_string.substring(to_remove, date_num_string.length);

    //var d1 = new Date(current_date_search);
    //var d2 = new Date();
    //var greater = d1.getTime() > d2.getTime();

    //alert("dateVar search set time: " + d1 + " current month and year: " + d2);

    var can_scroll = true;
    //if (action == 'Next') {
    //  if (greater) {  // can't scroll into the future...
    //    can_scroll = false;
    //    alert("can't scroll into future");
    //  }
    //}

    if ( can_scroll == true ) {
      // create link to send GET variables through
      var query_string = "../ajax/scroll.ajax.php";
      query_string += "?action=" + action;
      query_string += "&date_search=" + current_date_search;
      query_string += "&current_num=" + current_page_num;
      query_string += "&user_id=" + user_id.innerHTML;
      query_string += "&table_scroll=" + table_scroll;
      query_string += "&show_per_page=" + show_per_page;

      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
         document.getElementById("scroll_month_div").innerHTML = this.responseText;
        }
      };
      xhttp.open("GET", query_string, true);
      xhttp.send();

      // when the data is returned after ajax, it redirects back to inventory
      //window.location = "../pages/finances.php";
    }
  }

  // this method is for selecting a category button in order to show more detail about that category spending this month & year //
  function select_cat(cat_id, table_scroll) {
      // setup the ajax request
      var xhttp = new XMLHttpRequest();
      // get variables from inputs below:
      var current_page_num = document.getElementById(table_scroll + '_current_page_num');
      var user_id = document.getElementById('user_id');
      var date_search = document.getElementById('date_search');
      var show_per_page = 5;
      var scroll_div_name = table_scroll + "_scroll_div";

      // reset page number too
      current_page_num.innerHTML = 0;
      // update the current set cat id to be regular class for style
      var current_cat_id = document.getElementById(table_scroll + '_current_cat_id');
      var get_current_cat_button = document.getElementById('cat_button_' + current_cat_id.innerHTML);
      var get_new_cat_button = document.getElementById('cat_button_' + cat_id);
      //alert("current cat id: " + current_cat_id.innerHTML);
      //alert("new cat id: " + cat_id);
      // remove from this previous cat button
      get_current_cat_button.className = "btn btn-primary btn-sm";
      // add to new
      get_new_cat_button.className = "btn btn-dark btn-sm";

      // create link to send GET variables through
      var query_string = "../ajax/scroll.ajax.php";
      query_string += "?current_num=" + cat_id;
      query_string += "&user_id=" + user_id.innerHTML;
      query_string += "&action=" + "SelectCategoryTable";
      query_string += "&date_search=" + date_search.innerHTML;
      query_string += "&table_scroll=" + table_scroll;
      query_string += "&show_per_page=" + show_per_page;

      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
         document.getElementById(scroll_div_name).innerHTML = this.responseText;
        }
      };
      xhttp.open("GET", query_string, true);
      xhttp.send();

      // when the data is returned after ajax, it redirects back to inventory
      //window.location = "../pages/finances.php";
  }


  // this method is for selecting a company button in order to show more detail about that company spending this month & year //
  function select_comp(comp_id, table_scroll) {
      // setup the ajax request
      var xhttp = new XMLHttpRequest();
      // get variables from inputs below:
      var current_page_num = document.getElementById(table_scroll + '_current_page_num');
      var user_id = document.getElementById('user_id');
      var date_search = document.getElementById('date_search');
      var show_per_page = 5;
      var scroll_div_name = table_scroll + "_scroll_div";

      // reset page number too
      current_page_num.innerHTML = 0;
      // update the current set cat id to be regular class for style
      var current_comp_id = document.getElementById(table_scroll + '_current_comp_id');
      var get_current_comp_button = document.getElementById('comp_button_' + current_comp_id.innerHTML);
      var get_new_comp_button = document.getElementById('comp_button_' + comp_id);
      //alert("current cat id: " + current_cat_id.innerHTML);
      //alert("new cat id: " + cat_id);
      // remove from this previous cat button
      get_current_comp_button.className = "btn btn-primary btn-sm";
      // add to new
      get_new_comp_button.className = "btn btn-dark btn-sm";

      // create link to send GET variables through
      var query_string = "../ajax/scroll.ajax.php";
      query_string += "?current_num=" + comp_id;
      query_string += "&user_id=" + user_id.innerHTML;
      query_string += "&action=" + "SelectCompanyTable";
      query_string += "&date_search=" + date_search.innerHTML;
      query_string += "&table_scroll=" + table_scroll;
      query_string += "&show_per_page=" + show_per_page;

      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
         document.getElementById(scroll_div_name).innerHTML = this.responseText;
        }
      };
      xhttp.open("GET", query_string, true);
      xhttp.send();

      // when the data is returned after ajax, it redirects back to inventory
      //window.location = "../pages/finances.php";
  }

  </script>


      <?php


        // this is for looking at previous finance dates in the system
        $date_search = date('Y-m-d');
        if (isset($_POST['date_search'])) {
          $date_search = $_POST['date_search'];
        }
        //echo "date search: ".$date_search."<br>";

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
             $show_month = date('M Y', $next_month);
             //echo "next month: ".$show_month."<br>";
             //echo "month: ".$show_month."<br>";
             array_push($months_of_year, $show_month);
        }
        //var_dump($months_of_year);
        // start the outer table
        echo '<div class="container">';   // main div

          echo '<h1 style="text-align:center;">Monthly</h1>';

          echo '<div id="scroll_month_div" name="scroll_month_div">';   // scroll div

              // call main monthly tables for this page
              library_monthly_tables("Current", strtotime($date_search), $user_id);

          echo '</div>';  // end scroll div
          //echo '<div class="div_element_block">';
            //echo '<h4 style="text-align:center;">Metrics</h4>';
            /*
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
