<!DOCTYPE html>
<html lang="en">
<head>
  <?php
    ////declare(strict_types = 1);
    include '../includes/autoloader.inc.php';
    //include '/home3/lifement/public_html/classes/header.class.php';
    include '../includes/function_library.inc.php';
    // Initialize the session
    session_start();

    //////////////////////////////////////////////////////////////////////////////
    $_SESSION['loggedin'] = false;

    //echo 'Session: '.$_SESSION['loggedin'].'<br>';
    // Check if the user is already logged in, if yes then redirect him to welcome page
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
        header("location: ../pages/manage.php");
        exit;
    } else {
      //echo "<h1>Hello Friends. Website is still currently being worked on. Patience is a virtue! ~N</h2>";
    }
    //error_reporting(E_ALL);

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
              $user_theme = $row['user_theme'];
            }
            // hash the password given
            //$hashed_password = md5($password);
            //echo "pass_word: ".$pass_word."<br>";
            //echo "hashed_password: ".$hashed_password."<br>";
            // check if passwords match
            if (isset($pass_word) && $password == $pass_word) {  //  && $pass_word == $hashed_password
              // Password is correct, so start a new session
              //session_start();

              // Store data in session variables
              $_SESSION["loggedin"] = true;
              $_SESSION["user_id"] = $user_id;
              $_SESSION['id_role'] = $id_role;
              $_SESSION["username"] = $username;

              //echo "user_id: ".$user_id."<br>";
              //echo "username: ".$username."<br>";
              $date_now = date('Y-m-d H:i:s');  // Removed CURRENT_TIMESTAMP and put this instead
              // update last logged in to current timestamp
              $sql = "
                      UPDATE users
                      SET user_last_logged = TIMESTAMP('".$date_now."')
                      WHERE user_id = ".$user_id.";
              ";
              //echo $sql;
              if ($conn->query($sql) === TRUE) {
                echo 'SUCCESS: UPDATED RECORD';
              } else {
                echo 'ERROR: DID NOT UPDATE';
              }

              // Redirect user to welcome page
              header("location: ../pages/manage.php");
            } else {
                // Password is not valid, display a generic error message
                $login_err = "Invalid username or password.";
            }
        }
    }
    //////////////////////////////////////////////////////////////////////////////


    $header = new Header();
    $header->show_header('');
  ?>

</head>

<body class="bg-theme bg-theme1">
<?php
  if(!empty($login_err)){
      echo '<div class="alert alert-danger" style="text-align: center;">' . $login_err . '</div>';
  }
?>

<!-- start loader -->
<!--
   <div id="pageloader-overlay" class="visible incoming"><div class="loader-wrapper-outer"><div class="loader-wrapper-inner" ><div class="loader"></div></div></div></div>
-->
   <!-- end loader -->

<!-- Start wrapper-->
 <div id="wrapper">

 <div class="loader-wrapper"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></div>
	<div class="card card-authentication1 mx-auto my-5">
		<div class="card-body">
		 <div class="card-content p-2">
       <div class="image-container">
           <img id="mainLogo" src="../assets/img/logos/LMS_Fox_03_Small_Trans.png">
       </div>
		  <div class="card-title text-uppercase text-center py-3">Sign In</div>
		    <form method="post" action="../pages/login.php">
    			  <div class="form-group">
      			  <label for="username" class="sr-only">Username</label>
      			   <div class="position-relative has-icon-right">
      				  <input type="text" id="username" name="username" class="form-control input-shadow" placeholder="Enter Username">
      			   </div>
      			  </div>
      			  <div class="form-group">
      			  <label for="password" class="sr-only">Password</label>
      			   <div class="position-relative has-icon-right">
      				  <input type="password" id="password" name="password" class="form-control input-shadow" placeholder="Enter Password">
      				  <div class="form-control-position">
                  <button type="button" onclick="show_hide_password()" style="border: none; background: transparent;">
                      <i id="eye_icon" class="bi bi-eye-slash-fill"></i>
                  </button>
      				  </div>
      			   </div>
      			  </div>

      			<div class="form-row">
      			 <div class="form-group col-6">
      			   <div class="icheck-material-white">
                      <input type="checkbox" id="user-checkbox" checked="" />
                      <label for="user-checkbox">Remember me</label>
      			  </div>
      			 </div>
             <!--
      			 <div class="form-group col-6 text-right">
      			  <a href="reset-password.html">Reset Password</a>
      			 </div>
           -->
      			</div>
      			 <input type="submit" name="login_button" value="Login" class="btn btn-light btn-block"></input>
    			</div>
			 </form>
		   </div>
		  </div>
      <!--
		  <div class="card-footer text-center py-3">
		    <p class="text-warning mb-0">Don't have an account? <a href="register.html"> Sign Up here</a></p>
		  </div>
    -->
	     </div>

     <!--Start Back To Top Button-->
    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
    <!--End Back To Top Button-->

	</div><!--wrapper-->

  <!-- Bootstrap core JavaScript-->
  <script src="../assets/js/new-design/jquery.min.js"></script>
  <script src="../assets/js/new-design/popper.min.js"></script>
  <script src="../assets/js/new-design/bootstrap.min.js"></script>

  <!-- sidebar-menu js -->
  <script src="../assets/js/new-design/sidebar-menu.js"></script>

  <!-- Custom scripts -->
  <script src="../assets/js/new-design/app-script.js"></script>

  <script type="text/javascript">
  function show_hide_password() {
    var x = document.getElementById("password");
    var eye_icon = document.getElementById("eye_icon");
    if (x.type === "password") {
      x.type = "text";
      eye_icon.className = "bi bi-eye-fill"; // Use className instead of class
    } else {
      x.type = "password";
      eye_icon.className = "bi bi-eye-slash-fill"; // Use className instead of class
    }
  }

  </script>

</body>
</html>
