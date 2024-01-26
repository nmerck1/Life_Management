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

  $navbar->show_section_nav($loggedin, 'Finances', $id_role);

  $secondary_tab = 'View';
  $navbar->show_secondary_nav($loggedin, $secondary_tab);

  $finance_nav = new FinanceNavbar();
  $finance_nav->show_header_nav('Yearly', $secondary_tab);
?>

<script type="text/javascript">
	function scroll_table(next_prev_num, table_scroll){
      // setup the ajax request
  		var xhttp = new XMLHttpRequest();
      // get variables from inputs below:
  		var current_year_num = document.getElementById(table_scroll + '_current_year_num');
      var user_id = document.getElementById('user_id');
      var date_search = document.getElementById('date_search');
      var show_per_page = 5;
      var scroll_div_name = table_scroll + "_scroll_div";
      var current_year = new Date().getFullYear();

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
      if (action == 'Next') {
        if (current_year_num.innerHTML == current_year) { // I first made the life management system back in 2019
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
        query_string += "&show_per_page=" + show_per_page;

        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
           document.getElementById(scroll_div_name).innerHTML = this.responseText;
          }
        };
        xhttp.open("GET", query_string, true);
        xhttp.send();

        // when the data is returned after ajax, it redirects back to inventory
        //window.location = "../pages/manage.php";
      }
	}
</script>

      <?php
      echo '<p id="user_id" style="display:none;" value="'.$user_id.'">'.$user_id.'</p>';
        // this is for looking at previous finance dates in the system
        $date_search = date('Y-m-d');
        //if (isset($_POST['date_search'])) {
        //  $date_search = $_POST['date_search'];
        //}

        $this_year = get_this_year($date_search);

        echo '<div class="mainContentContainer">';
          // start the outer table
          echo '<div class="container">';

            //echo '<h1 style="text-align:center;">Yearly</h1>';
            $show_month_year_title = date('F', strtotime($date_search));

            echo '<br>';
            echo '<br>';

            echo '<div class="div_element_block">';// div for yearly savings overview
            //  echo '<h2 style="text-align:center;">'.$this_year.'</h2>';
              echo '<p style="width:100%; margin:0px; text-align:center;">';
                echo '<button name="prev_button" onclick="scroll_table(0, \'Yearly\');" style="float:left; background:none; border:none; font-size:20px; height:32px;">';
                  echo '<i class="actions"><p class="bi-arrow-left-square-fill"></p></i>'; // -box-arrow-left
                echo '</button>';
                echo '<button name="next_button" onclick="scroll_table(1, \'Yearly\');" style="float:right; background:none; border:none; font-size:20px; height:32px;">';
                  echo '<i class="actions"><p class="bi-arrow-right-square-fill"></p></i>';
                echo '</button>';
              echo '</p>';


              echo '<p id="date_search" style="display:none;" value="'.$date_search.'">'.$date_search.'</p>';

              echo '<div id="Yearly_scroll_div">';
                  library_yearly_table($user_id, "First", $this_year, $date_search, 5);
              echo '</div>';
            echo '</div>';

          echo '</div>';
        echo '</div>';
        echo '<br>';

/*     //     DO NOT REMOVE COMMENTED OUT CODE    //


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
*/

/*
        if ($id_role == 0){   // this graph works but i updated this script to work with the the new functions in library so now its broken...  //
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
        // Graphs for average daily prices
        // get data from user
        $sql = "
              SELECT
                cat.cat_name,
                SUM(fe.fe_amount) AS 'total category amount',
                fe.fe_date,
                fe.id_user,
                DAY(LAST_DAY(fe.fe_date)) AS 'days in this month',
                SUM(fe.fe_amount)/DAY(LAST_DAY(fe.fe_date)) AS 'average cost per day'

              FROM finance_expenses fe
              LEFT JOIN categories cat ON cat.cat_id = fe.id_category

              WHERE fe.id_user = '".$user_id."'
              AND MONTH(fe.fe_date) = MONTH('".$date_search."')
              AND YEAR(fe.fe_date) = YEAR('".$date_search."')

              GROUP BY cat.cat_name;
        ";
        echo $sql;
        $dbh = new Dbh();
        $stmt = $dbh->connect()->query($sql);

        $graph_data = array();
        while ($row = $stmt->fetch()) {
          $cat_name = $row['cat_name'];
          $average_amount = $row['average_amount'];
          $graph_data[] = array($cat_name, $average_amount);
        }
        var_dump($graph_data);
        //$graph_data_json = json_encode($graph_data);
        // output graph using data
        echo '<div id="category_averages_chart">';
          library_generate_category_averages($graph_data);
        echo '</div>';
        */
      ?>

<?php
  $footer = new Footer();
  $footer->show_footer();
?>

</body>
</html>
