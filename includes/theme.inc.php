<?php
declare(strict_types=1);
include '../includes/autoloader.inc.php';
include '../includes/function_library.inc.php';

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../pages/login.php");
    exit;
}

$show_error = false;

$action = '';	// this is either Update or Insert
if (isset($_GET['action'])){
	$action = $_GET['action'];
  //echo "update_type: ".$update_type."<br>";
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

  $navbar->show_section_nav($loggedin, '', $id_role);

?>


<?php
	//echo '<p id="selected_id" style="display:none;" value="'.$selected_id.'">'.$selected_id.'</p>';
	echo '<p id="user_id" style="display:none;" value="'.$user_id.'">'.$user_id.'</p>';

	echo '<p id="test"></p>';

  echo '<br>';
  echo '<br>';
  echo '<br>';
  echo '<br>';
  echo '<br>';

  echo '<div class="mainContentContainer">';
    echo '<div class="container text-center"  style="height:600px;">';

      echo '<div class="div_element_block">';
        echo '<label>Themes: </label>';
        echo '<br>';
        // get values from selected id in table:
        $sql = "SELECT * FROM themes WHERE theme_is_active = 1; ";
        $dbh = new Dbh();
        $stmt = $dbh->connect()->query($sql);
        // should only populate one row of data
        while ($row = $stmt->fetch()) {
            echo '<a class="btn btn-primary" href="../ajax/theme.ajax.php?user_id='.$user_id.'&theme_file='.$row['theme_file'].'">'.$row['theme_name'].'</a>';
            echo '<br>';
            echo '<br>';
        }
      echo '</div>';

    echo '</div>';
  echo '</div>';

  $footer = new Footer();
  $footer->show_footer();
?>

</body>
</html>
