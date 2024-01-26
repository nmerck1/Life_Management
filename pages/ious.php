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

  $secondary_tab = 'Manage';
  $navbar->show_secondary_nav($loggedin, $secondary_tab);

  $finance_nav = new FinanceNavbar();
  $finance_nav->show_header_nav('IOUs', $secondary_tab);
?>
  <script type="text/javascript">
  	function scroll_loans(next_prev_num, table_scroll){
        // setup the ajax request
    		var xhttp = new XMLHttpRequest();
        // get variables from inputs below:
    		var current_page_num = document.getElementById('current_loan_page');
        var user_id = document.getElementById('user_id');
        var date_search = document.getElementById('date_search');
        var show_per_page = 5;

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
          query_string += "&show_per_page=" + show_per_page;

          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
             document.getElementById("scroll_div_loans").innerHTML = this.responseText;
            }
          };
          xhttp.open("GET", query_string, true);
          xhttp.send();

          // when the data is returned after ajax, it redirects back to inventory
          //window.location = "../pages/manage.php";
        }
  	}
    function scroll_debts(next_prev_num, table_scroll){
        // setup the ajax request
    		var xhttp = new XMLHttpRequest();
        // get variables from inputs below:
    		var current_page_num = document.getElementById('current_debt_page');
        var user_id = document.getElementById('user_id');
        var date_search = document.getElementById('date_search');
        var show_per_page = 5;

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
          query_string += "&show_per_page=" + show_per_page;

          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
             document.getElementById("scroll_div_debts").innerHTML = this.responseText;
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
        if (isset($_POST['date_search'])) {
          $date_search = $_POST['date_search'];
        }

        echo '<p id="date_search" style="display:none;" value="'.$date_search.'">'.$date_search.'</p>';

        // start the outer table
        echo '<div class="container">';
          //echo '<h1 style="text-align:center;">IOUs</h1>';
          //echo '<i style="color:grey;">';
          //  echo '(This is where you can manage your loans to people.
          //          You can also view the debts that have been paid off to you.
          //          It will also show you what debts have been paid off both by you and your previous debtors.)';
          //echo '</i>';

          echo '<div class="div_element_block">'; // div for owed to you
            echo '<h4 style="text-align:center;">Your Loans</h4>';
            echo '<p style="width:95%; margin:0px; text-align:center;">';
              echo '<button name="prev_button" onclick="scroll_loans(0, \'Loans\');" style="float:left; background:none; border:none; font-size:20px; height:32px;">';
                echo '<i class="actions"><p class="bi-arrow-left-square-fill"></p></i>';
              echo '</button>';
              echo '<button name="next_button" onclick="scroll_loans(1, \'Loans\');" style="float:right; background:none; border:none; font-size:20px; height:32px;">';
                echo '<i class="actions"><p class="bi-arrow-right-square-fill"></p></i>';
              echo '</button>';
            echo '</p>';

            echo '<div id="scroll_div_loans">';
              library_ious_table($user_id, "First", 1, $date_search, 5, false, false);
            echo '</div>';

          echo '</div>';

          echo '<br>';

          echo '<div class="div_element_block">'; // div for you owe
            echo '<h4 style="text-align:center;">Your Debts</h4>';
            echo '<p style="width:95%; margin:0px; text-align:center;">';
              echo '<button name="prev_button" onclick="scroll_debts(0, \'Debts\');" style="float:left; background:none; border:none; font-size:20px; height:32px;">';
                echo '<i class="actions"><p class="bi-arrow-left-square-fill"></p></i>';
              echo '</button>';
              echo '<button name="next_button" onclick="scroll_debts(1, \'Debts\');" style="float:right; background:none; border:none; font-size:20px; height:32px;">';
                echo '<i class="actions"><p class="bi-arrow-right-square-fill"></p></i>';
              echo '</button>';
            echo '</p>';

            echo '<div id="scroll_div_debts">';
              library_ious_table($user_id, "First", 1, $date_search, 5, true, false);
              echo '</div>';

          echo '</div>';

          echo '<br>';
          echo '<br>';

          echo '<div class="div_element_block">'; // div for your paid loans
            echo '<h4 style="text-align:center;">Your PAID Loans</h4>';

            library_ious_table($user_id, "First", 1, $date_search, 5, false, true);

          echo '</div>';

          echo '<br>';

          echo '<div class="div_element_block">'; // div for your paid debts
            echo '<h4 style="text-align:center;">Your PAID Debts</h4>';

            library_ious_table($user_id, "First", 1, $date_search, 5, true, true);

          echo '</div>';

          echo '<br>';

        echo '</div>';  // end main div
      ?>

<?php
  $footer = new Footer();
  $footer->show_footer();
?>

</body>
</html>
