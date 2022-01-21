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
          echo '<h1 style="text-align:center;">Budgets</h1>';


          echo '<div>';// div for category spending
            echo '<p style="text-align:center; background: rgb(33, 37, 46); border-right:2px solid rgb(33, 37, 46); border-top:2px solid rgb(33, 37, 46);">Custom Budgets</p>';
            echo '<table class="table table-dark" style="background-color:#3a5774; text-align:center;">';
                echo '<tr>';
                  echo '<th>Name</th>';
                  echo '<th>Amount</th>';
                  echo '<th style="background-color: rgb(33, 37, 46);">';
                    echo '<a href="../includes/finances.inc.php?form_type=Budget&user_id='.$user_id.'"><p class="bi-plus-circle" style="color:white;"></p></a>';
                  echo '</th>';
                echo '</tr>';
                $sql = "
                        SELECT bud.bud_id,
                               cat.cat_name,
                               bud.bud_amount,
                               bud.id_category,
                               bud.is_active,
                               bud.id_user
                        FROM budgets bud
                        LEFT JOIN categories cat ON bud.id_category = cat.cat_id

                        WHERE bud.is_active = 1
                        AND bud.id_user = ".$user_id."

                        ORDER BY cat.cat_name ASC;
                ";
              //  echo $sql .'<br>';
                $dbh = new Dbh();
                $stmt = $dbh->connect()->query($sql);

                while ($row = $stmt->fetch()) {
                  echo '<tr>';
                    echo '<td style="background:rgb(25, 29, 32);">' .$row['cat_name']. '</td>';
                    echo '<td style="text-align:right; background:rgb(25, 29, 32);">$' .number_format(($row['bud_amount']), 2). '</td>';
                    echo '<td style="background:rgb(33, 37, 46);">';
                        echo '<a href="../includes/finances.inc.php?selected_id='.$row['bud_id'].'&update_type=Edit&form_type=Budget&user_id='.$user_id.'"><p class="bi-pencil-fill" style="color:white;"></p></a>';
                        echo '<a href="../ajax/finances.ajax.php?selected_id='.$row['bud_id'].'&update_type=Delete&form_type=Budget&user_id='.$user_id.'"><p class="bi-trash-fill" style="color:white;"></p></a>';
                    echo '</td>';
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
