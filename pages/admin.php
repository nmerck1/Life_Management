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

// check if user is not an admin
if ($id_role != 1){
  header("location: ../pages/home.php");
  exit;
}

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



  echo '<div class="container" style="height:100%; text-align:center;">';
    echo '<h1>User Activity & Overview</h1>';
    echo '<br>';
    echo '<table class="table table-dark" style="background-color:#3a5774; text-align:center;">';
    echo '<tr>';
      echo '<th>Username</th>';
      echo '<th>Role</th>';
      echo '<th>First & Last</th>';
      echo '<th>Last Logged In</th>';
      echo '<th>Number Finance Income Records</th>';
      echo '<th>Number Finance Expense Records</th>';
      echo '<th>Number Diet Food Log Records</th>';
    echo '</tr>';

    // first select each user that is active and then loop through each one
    $user_ids = array();
    $sql = "
            SELECT u.user_id,
                  u.user_name,
                  u.user_fname,
                  u.user_lname,
                  u.user_last_logged,

                  ur.role_name,
                  ur.role_color

            FROM users u
            LEFT JOIN user_roles ur ON u.id_role = ur.role_id
            WHERE u.is_active = 1

            ORDER BY u.user_last_logged DESC;
    ";
    //echo $sql;
    $dbh = new Dbh();
    $stmt = $dbh->connect()->query($sql);

    $is_alternate_row = false;
    $add_alternating_class = '';
    while ($row = $stmt->fetch()) {
        echo '<tr>';

        if ($is_alternate_row == false) {
          $add_alternating_class = '';
          $is_alternate_row = true;
        } else {
          $add_alternating_class = 'class="alternating_row"';
          $is_alternate_row = false;
        }
        $this_user_id = $row['user_id'];

        $role_color = $row['role_color'];
        //echo 'this_user_id: '.$this_user_id.'<br>';
        echo '<td '.$add_alternating_class.'>'.$row['user_name'].'</td>';
        echo '<td '.$add_alternating_class.' style="color:'.$role_color.';">'.$row['role_name'].'</td>';
        echo '<td '.$add_alternating_class.'>'.$row['user_fname'].' '.$row['user_lname'].'</td>';

        $date_string = strtotime($row['user_last_logged']);
        echo '<td '.$add_alternating_class.' style="color:grey;">'.date('m-d-Y h:iA', $date_string).'</td>';

        $build_rows = '';
        // now for each row and each user, let's print out their stats
        $sql_stats = "
              SELECT u.user_id,

                    COUNT(DISTINCT fi.fi_id) AS 'num_incomes',
                    COUNT(DISTINCT fe.fe_id) AS 'num_expenses',
                    COUNT(DISTINCT fl.fl_id) AS 'num_food_logs'

              FROM users u
              LEFT JOIN finance_incomes fi ON u.user_id = fi.id_user AND fi.fi_id <> ''
              LEFT JOIN finance_expenses fe ON u.user_id = fe.id_user AND fe.fe_id <> ''
              LEFT JOIN food_logs fl ON u.user_id = fl.id_user AND fl.fl_id <> ''

              WHERE u.user_id = '".$this_user_id."'
              AND fi.is_active = 1
              AND fe.is_active = 1
              AND fl.is_active = 1;
        ";
        //echo $sql_stats.'<br><br>';
        $dbh = new Dbh();
        $stmt_stats = $dbh->connect()->query($sql_stats);

        while ($row = $stmt_stats->fetch()) {
          echo '<td '.$add_alternating_class.'>'.$row['num_incomes'].'</td>';
          echo '<td '.$add_alternating_class.'>'.$row['num_expenses'].'</td>';
          echo '<td '.$add_alternating_class.'>'.$row['num_food_logs'].'</td>';
        }

      echo '</tr>';
    }

    echo '</table>';
  echo '</div>';




  $footer = new Footer();
  $footer->show_footer();
?>

</body>
</html>
