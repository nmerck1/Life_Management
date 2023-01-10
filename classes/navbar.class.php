<?php

class Navbar {

  public function show_header_nav($loggedin, $user_fname, $id_role, $messages){
    echo '<ul class="navbar-brand" style="width:100%; font-size:17px;">';
      echo '<li><a class="bi-yin-yang" href="home.php"></a></li>';

      echo '<li class="navdropdown" style="float:left; border-right:none;">';
        echo '<a href="javascript:void(0)" class="dropnavbtn"><i class="bi-arrows-expand"></i></a>';
        echo '<div class="navbar-content">';
          echo '<a href="../pages/finances.php">Finances</a>';
          //echo '<a href="../pages/diet.php">Diet</a>';
          //echo '<a href="../pages/routine.php">Routine</a>';
          if ($id_role == 1) {
            echo '<a style="color:rgb(215 46 46);" href="../pages/admin.php">Admin</a>';
          }
        echo '</div>';
      echo '</li>';
      /*
      echo '<li id="expand_icon"><a href="javascript:void(0);" onclick="responsive_navbar()">';
        echo '<i class="bi-arrows-expand"></i>';
      echo '</a></li>';


      echo '<div class="topnav" id="my_topnav">';
      echo '<ul style="width:100%; font-size:17px;">';
        //echo '<li><a href="../pages/home.php">Home</a></li>';
        echo '<li><a href="../pages/finances.php">Finances</a></li>';
        echo '<li><a href="../pages/diet.php">Diet</a></li>';
        if ($id_role == 1) {
          echo '<li><a style="color:rgb(215 46 46);" href="../pages/admin.php">Admin</a></li>';
        }
        echo '</ul>';
      echo '</div>';
      */


      if ($loggedin) {
        echo '<li class="dropdown" style="float:right; border-right:none;">';
           echo '<a href="javascript:void(0)" class="dropbtn"><i class="bi-person-fill">  Hi '.$user_fname.'!</i></a>';
           echo '<div class="dropdown-content">';
            echo '<a href="../pages/profile.php">Profile</a>';
            echo '<a href="../pages/logout.php">Log Out</a>';
           echo '</div>';
         echo '</li>';

        $icon = 'bi-envelope-fill';//bi-envelope-exclamation
        $show = '';
        if ($messages > 0) { $show = $messages; }
        echo '<li style="float:right; font-size:17px;"><a class="'.$icon.'" href="../pages/notifications.php"> '.$show.'</a></li>';
      }
      //echo '<a class="navbar-brand" href="../pages/vision.php">Vision</a>';


    echo '</ul>';
  }

}
