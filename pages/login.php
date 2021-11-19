<?php
//declare(strict_types = 1);
include '../includes/autoloader.inc.php';
include '../includes/function_library.inc.php';
// Initialize the session
session_start();
$_SESSION['loggedin'] = false;

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: ../pages/home.php");
    exit;
}

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
  // Check if username is empty
  if(empty(trim($_POST["username"]))){
      $username_err = "Please enter username.";
  } else{
      $username = trim($_POST["username"]);
  }

  // Check if password is empty
  if(empty(trim($_POST["password"]))){
      $password_err = "Please enter your password.";
  } else{
      $password = trim($_POST["password"]);
  }

  // Validate credentials
  if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "
            SELECT *
            FROM users
            WHERE user_name = '".$username."'
            AND is_active = 1
        ";
        //echo $sql;
        $dbh = new Dbh();
        $stmt = $dbh->connect()->query($sql);
        //echo $sql;
        // should only populate one row of data
        while ($row = $stmt->fetch()) {
          $user_id = $row['user_id'];
          $id_role = $row['id_role'];
          $user_name = $row['user_name'];
          $user_fname = $row['user_fname'];
          $user_lname = $row['user_lname'];
          $pass_word = $row['pass_word'];
        }
        // hash the password given
        $hashed_password = md5($password);
        //echo "pass_word: ".$pass_word."<br>";
        //echo "hashed_password: ".$hashed_password."<br>";
        // check if passwords match
        if (isset($pass_word) && $pass_word == $hashed_password) {
          // Password is correct, so start a new session
          session_start();

          // Store data in session variables
          $_SESSION["loggedin"] = true;
          $_SESSION["user_id"] = $user_id;
          $_SESSION['id_role'] = $id_role;
          $_SESSION["username"] = $username;

          echo "user_id: ".$user_id."<br>";
          echo "username: ".$username."<br>";

          // Redirect user to welcome page
          header("location: ../pages/home.php");
        } else {
            // Password is not valid, display a generic error message
            $login_err = "Invalid username or password.";
        }
    }
}




?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php
    $header = new Header();
    $header->show_header();
  ?>


  <script type="text/javascript">
    function show_hide_password() {
      var x = document.getElementById("password");
      if (x.type === "password") {
        x.type = "text";
      } else {
        x.type = "password";
      }
    }
  </script>

</head>
<body>


<?php
  //use Style\Navbar;
  $navbar = new Navbar();
  $navbar->show_header_nav('', '', '');

  if(!empty($login_err)){
      echo '<div class="alert alert-danger">' . $login_err . '</div>';
  }

  echo '<div class="container text-center">';

    echo '<h1>Login</h1>';
      echo '<form method="post" action="../pages/login.php">';

        echo '<label>Username: </label>';
        echo '<input type="text" id="username" name="username"></input>';
        echo '<br>';
        echo '<label>Password: </label>';
        echo '<input type="password" id="password" name="password"></input>';
        echo '<br>';
        echo '<input type="checkbox" onclick="show_hide_password()">Show Password';
        echo '<br>';

        echo '<button style="margin:auto; display:inherit;" name="login_button" value="Login" class="btn btn-success btn-md">Login</button>';
      echo '</form>';
  echo '</div>';

  $footer = new Footer();
  $footer->show_footer();
?>

</body>
</html>
