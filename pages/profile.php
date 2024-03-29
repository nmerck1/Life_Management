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
  $navbar = new Navbar();
  $navbar->show_header_nav($loggedin, $user_fname, $id_role, $messages);

  $navbar->show_section_nav($loggedin, '', $id_role);


  echo '<br>';
  echo '<br>';
  echo '<br>';
  echo '<br>';
  echo '<br>';

  echo '<div class="mainContentContainer">';
      echo '<div class="container" style="height:100%; text-align:center;">';

        echo '<br>';
        echo '<br>';
        echo '<br>';
        echo '<div class="div_element_block">';
          echo '<br>';
          $sql = "
                  SELECT *
                  FROM users u
                  LEFT JOIN user_roles ur ON u.id_role = ur.role_id
                  WHERE u.user_id = '".$user_id."'
                  AND u.is_active = 1;
          ";
          //echo $sql;
          $dbh = new Dbh();
          $stmt = $dbh->connect()->query($sql);

          while ($row = $stmt->fetch()) {
            $user_name = $row['user_name'];
            $user_fname = $row['user_fname'];
            $user_lname = $row['user_lname'];
            $user_dob = $row['user_dob'];
            $role_name = $row['role_name'];
            $user_icon = $row['user_icon'];
          }

          $today = date("Y-m-d");
          $diff = date_diff(date_create($user_dob), date_create($today));

          echo '<img src="../pics/profile/'.$user_icon.'" style="border: 2px solid black; border-radius: 10px;" />';
          echo '<h2>'.$user_fname.' '.$user_lname.'</h2>';
          echo '<p><span style="color:grey;">Username: </span>'.$user_name.'</p>';
          echo '<p><span style="color:grey;">Role: </span>'.$role_name.'</p>';
          echo '<p><span style="color:grey;">Age: </span>'.$diff->format('%y').'</p>';

        echo '</div>';
      echo '</div>';

      echo '<br>';

      echo '<div class="container" style="text-align:center;">';

        echo '<div class="div_element_block">';
          echo '<p>(Actions)</p>';
          echo '<a class="btn btn-primary" href="../includes/profile.inc.php?user_id='.$user_id.'&action=Password">Change Password</a>';
          echo '<br>';
          echo '<br>';

          echo '<a class="btn btn-primary" href="../includes/theme.inc.php?user_id='.$user_id.'&action=Theme">Change Theme</a>';
          echo '<br>';
          echo '<br>';

          echo '<a class="btn btn-primary" href="../includes/icon.inc.php?user_id='.$user_id.'&action=Icon">Change Icon</a>';
          echo '<br>';
          echo '<br>';

          echo '<a class="btn btn-danger" href="../pages/logout.php">Logout</a>';
          echo '<br>';
          echo '<br>';
        echo '</div>';
        echo '<br>';
        echo '<br>';
      echo '</div>';
      echo '<br>';
      echo '<br>';echo '<br>';
      echo '<br>';
  echo '</div>';

  $footer = new Footer();
  $footer->show_footer();
?>

</body>
</html>
