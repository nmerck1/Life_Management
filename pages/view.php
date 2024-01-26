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
  $finance_nav->show_header_nav('Monthly', $secondary_tab);

?>

<script type="text/javascript" src="../js/finances_shared.js"></script>

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

    //echo '<h1 style="text-align:center;">Monthly</h1>';

    echo '<div id="scroll_month_div" name="scroll_month_div">';   // scroll div

        // call main monthly tables for this page
        library_monthly_tables("Current", strtotime($date_search), $user_id, $secondary_tab);

    echo '</div>';  // end scroll div


  echo '</div>';  // end main div
?>

<?php
  $footer = new Footer();
  $footer->show_footer();
?>

</body>
</html>
