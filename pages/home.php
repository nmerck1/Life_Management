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

  //echo '<h1 style="text-align:center;">Life Overview</h1>';
  echo '<div class="container" style="height:600px;">';

  echo '<h2 style="font-size:34px;">Managing your <i class="actions">Life</i> made easy.</h2>';
    //echo '<p style="text-align:center;">(Page is still in development)</p>';
  /*
    echo '<table class="table table-dark" style="background-color:#3a5774;">';    // main table
      echo '<tr>';

        echo '<td>';
          echo '<p style="text-align:center;">Finances</p>';
        //  echo '<p style="text-align:center;">This weeks savings</p>';

          echo '<p style="text-align:center;">This years savings</p>';

          echo '<p style="text-align:center;">Actions</p>';

          echo '<form method="get" action="../includes/finances.inc.php?user_id='.$user_id.'&form_type=Income" style="text-align:center;">';
            echo '<button type="submit" name="submit" class="btn btn-primary btn-sm">Add New Income</button>';
          echo '</form>';

          echo '<br>';

          echo '<form method="get" action="../includes/finances.inc.php?user_id='.$user_id.'&form_type=Expense" style="text-align:center;">';
            echo '<button type="button" name="submit" class="btn btn-primary btn-sm">Add New Expense</button>';
          echo '</form>';

        echo '</td>';

        echo '<td>';
          echo '<p style="text-align:center;">Diet</p>';
          echo '<p style="text-align:center;">Macro Progress to Goal this week</p>';

        //  echo '<p style="text-align:center;">This year percentage of macro goals met</p>';
          //echo '<p style="text-align:center;">Pounds need to be gained/lost to meet weight goal</p>';

          echo '<p style="text-align:center;">Average macros per day</p>';
          echo '<p style="text-align:center;">Average macros per week</p>';

          echo '<p style="text-align:center;">Actions</p>';
            echo '<button style="margin:auto; display:inherit;" name="save_button" onclick="send_to_food();" value="Save" class="btn btn-primary btn-md">Add New Food Log</button>';

        echo '</td>';

      echo '</tr>';
    echo '</table>';
    */
  echo '</div>';

  $footer = new Footer();
  $footer->show_footer();
?>

</body>
</html>
