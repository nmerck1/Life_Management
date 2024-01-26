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
  $navbar = new Navbar();
  $navbar->show_header_nav($loggedin, $user_fname, $id_role, $messages);

  $navbar->show_section_nav($loggedin, 'Admin', $id_role);

  //$secondary_tab = '';
  //$navbar->show_secondary_nav($loggedin, $secondary_tab);

  //$admin_nav = new AdminNavbar();
  //$admin_nav->show_header_nav('Monthly', $secondary_tab);



  echo '<div class="container" style="height:100%;">';

    echo '<div class="row">';
    // first select each user that is active and then loop through each one
    $user_ids = array();
    $sql = "
            SELECT u.user_id,
                  u.user_name,
                  u.user_fname,
                  u.user_lname,
                  u.user_last_logged,
                  u.user_icon,

                  ur.role_name,
                  ur.role_color

            FROM users u
            LEFT JOIN user_roles ur ON u.id_role = ur.role_id
            WHERE u.is_active = 1

            ORDER BY u.user_fname ASC;
    ";
    //echo $sql;
    $dbh = new Dbh();
    $stmt = $dbh->connect()->query($sql);

    $is_alternate_row = false;
    $add_alternating_class = '';
    while ($row = $stmt->fetch()) {
        //echo '<tr>';

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
        //echo '<td '.$add_alternating_class.'>'.$row['user_name'].'</td>';
        //echo '<td '.$add_alternating_class.' style="color:'.$role_color.';">'.$row['role_name'].'</td>';
        //echo '<td '.$add_alternating_class.'>'.$row['user_fname'].' '.$row['user_lname'].'</td>';

        $date_string = strtotime($row['user_last_logged']);
        //echo '<td '.$add_alternating_class.' style="color:grey;">'.date('m-d-Y h:iA', $date_string).'</td>';

        //echo '<div class="row d-flex justify-content-center align-items-center h-100">';
        //echo '<div class="card">';
          //echo '<div class="col-lg-4">';
            //echo '<div class="card-body">';

                  //echo '<div class="col-md-4 gradient-custom text-center text-white" style="border-top-left-radius: .5rem; border-bottom-left-radius: .5rem;">';

                        //echo '</div>';
                      //echo '</div>';

              //echo '</div>';
        //  echo '</div>'; // card group divs
        //echo '</div>';

        echo '<div class="col-md-4">';
          echo '<div class="card" style="margin-bottom: 20px;">';
            echo '<div class="card-body" style="border: 2px solid '.$row['role_color'].'; border-radius: 10px;">';

            echo '<img src="../assets/img/profile-icons/'.$row['user_icon'].'" alt="Avatar" class="img-fluid my-5" style="width: 80px;" />';
            echo '<h5>'.$row['user_fname'].' '.$row['user_lname'].'</h5>';
            echo '<p>'.$row['user_name'].'</p>';
            echo '<i class="far fa-edit mb-5"></i>';
          //echo '</div>';
          //echo '<div class="col-md-8">';
            //echo '<div class="card-body p-4">';
              echo '<h6>Information</h6>';
              echo '<hr class="mt-0 mb-4">';
              //echo '<div class="row pt-1">';
                //echo '<div class="col-6 mb-3">';
                  echo '<h6>Role</h6>';
                  echo '<p class="text-muted">'.$row['role_name'].'</p>';
                //echo '</div>';
                //echo '<div class="col-6 mb-3">';
                  echo '<h6>Last Logged</h6>';
                  echo '<p class="text-muted">'.date('M d, Y h:iA', $date_string).'</p>';
                //echo '</div>';
              //echo '</div>';
              echo '<h6>Actions</h6>';
              echo '<hr class="mt-0 mb-4">';
              //echo '<div class="row pt-1">';
                //echo '<div class="col-6 mb-3">';
                echo '<p><i>(In Progress)</i></p>';
                  echo '<button href="../includes/admin.inc.php?form_type=View&user_id='.$user_id.'" class="btn btn-secondary">View</a>'; //btn btn-info
              //  echo '</div>';
                //echo '<div class="col-6 mb-3">';
                  echo '<button href="../includes/admin.inc.php?form_type=Edit&user_id='.$user_id.'" class="btn btn-secondary" style="margin: 10px;">Edit</a>';// btn btn-primary

            echo '</div>';
          echo '</div>';
        echo '</div>';


      }
    echo '</div>';// div for card group

  echo '</div>'; // main container div




  $footer = new Footer();
  $footer->show_footer();
?>

</body>
</html>
