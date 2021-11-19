<?php

class Navbar {

  public function show_header_nav($loggedin, $user_fname, $id_role){
    echo '<nav class="navbar navbar-inverse">';
		  echo '<div class="container-fluid">';
		    echo '<div class="navbar-header">';

		     	echo '<a class="navbar-brand" href="home.php">';
			        echo '<p class="bi-yin-yang"></p>';
		      echo '</a>';

          echo '<a class="navbar-brand" href="../pages/home.php">Home</a>';
          echo '<a class="navbar-brand" href="../pages/vision.php">Vision</a>';
          echo '<a class="navbar-brand" href="../pages/finances.php">Finances</a>';
          if ($id_role == 1) {
            echo '<a class="navbar-brand" style="color:red;" href="../pages/admin.php">Admin</a>';
          }

          //echo '<a class="navbar-brand" href="../pages/routine.php">Routine</a>';
          //echo '<a class="navbar-brand" href="../pages/diet.php">Diet</a>';

          //echo '<a href="../pages/login.php">Login</a>';
          if ($loggedin) {
            echo '<a class="navbar-brand" href="../pages/logout.php">(Logout)</a>';
            
            echo '<p>Hi '.$user_fname.'!</p>';

            echo '<a class="navbar-brand" style="position:absolute; right:150px;" href="../pages/profile.php">';
              echo '<p class="bi-person-fill"></p>';
            echo '</a>';
          }
	  		echo '</div>';
	  	echo '</div>';
		echo '</nav>';

  }

}
