<?php

class Navbar {

  public function show_header_nav($loggedin, $user_fname, $id_role, $messages){
    echo '<ul class="navbar-brand" style="width:100%; font-size:17px;">';
      echo '<li><a class="bi-yin-yang" href="home.php"></a></li>';
      echo '<li><a href="../pages/home.php">Home</a></li>';
      echo '<li><a href="../pages/finances.php">Finances</a></li>';
      echo '<li><a href="../pages/diet.php">Diet</a></li>';
      if ($id_role == 1) {
        echo '<li><a style="color:rgb(215 46 46);" href="../pages/admin.php">Admin</a></li>';
      }
      if ($loggedin) {
        echo '<li class="dropdown" style="float:right; border-right:none;">';
           echo '<a href="javascript:void(0)" class="dropbtn"><i class="bi-person-fill">  Hi '.$user_fname.'!</i></a>';
           echo '<div class="dropdown-content">';
            echo '<a href="../pages/profile.php">Profile</a>';
            echo '<a href="../pages/logout.php">Log Out</a>';
           echo '</div>';
         echo '</li>';

        echo '<li style="float:right; font-size:17px;"><a class="bi-envelope-fill" href="../pages/notifications.php"> '.$messages.'</a></li>';
      }
      //echo '<a class="navbar-brand" href="../pages/vision.php">Vision</a>';
      //echo '<a class="navbar-brand" href="../pages/routine.php">Routine</a>';

    echo '</ul>';
  }
  
}
