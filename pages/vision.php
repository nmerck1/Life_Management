<?php
declare(strict_types = 1);
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
?>


<div class="container text-center">
  <!--
  <div class="row content">
    <div id="left_sidenav" class="col-sm-2 sidenav">
      <p class="bi-card-list" style="font-size: 1rem; color: white;"><a href="#"> Plans</a></p>
      <p class="bi-list-check" style="font-size: 1rem; color: white;"><a href="#"> Goals</a></p>
      <p class="bi-lightbulb" style="font-size: 1rem; color: white;"><a href="#"> Ideas</a></p>
    </div>
  -->
    <div class="container" style="height:600px;">
      <?php

      echo '<p> (This page is currently being developed) </p>';


      /*
      echo '<h1>Daily Checklist</h2>';
      echo '<ul>';
      $sql = "
            SELECT *
            FROM daily_checklists dc
            LEFT JOIN users u ON dc.id_user = u.user_id
            LEFT JOIN daily_tasks dt ON dc.id_task = dt.task_id
            WHERE dc.is_active = 1
            AND u.user_id = ".$user_id."

            #ORDER BY dt.task_name ASC;
      ";
      $dbh = new Dbh();
      $stmt = $dbh->connect()->query($sql);

      $color = 'white';
      $line_through = 'none';
      while ($row = $stmt->fetch()) {
        echo "task_value: ".$row["task_value"]."<br>";
        echo "min_task_completed: ".$row["min_task_completed"]."<br>";

        if ($row["task_value"] >= $row["min_task_completed"]) {
          $color = 'grey';
          $line_through = 'line-through';
        }

        echo '<li style="color:'.$color.'; text-decoration:'.$line_through.';">'.$row["task_name"].'</li>';

      }


      echo '</ul>';

      echo '<br>';
        $show_plans = new Plan();
        $show_plans->show_plans_table(true, true);  // editable, show title
        */
      ?>
    </div>
</div>

<?php
  $footer = new Footer();
  $footer->show_footer();
?>

</body>
</html>
