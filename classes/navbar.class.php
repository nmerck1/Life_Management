<?php

class Navbar {

  public function show_header_nav($loggedin, $user_fname, $id_role, $messages){
    //echo '<div class="divMainNavbar">';
      echo '<ul class="mainNavbarUL">';

        echo '<li id="mainNavFirstLI">';
          echo '<i class="bi-yin-yang" style="margin:10px;"></i> Life Management ';
          //echo '<a href="../pages/manage.php"><i class="bi-three-dots" style="margin:10px;"></i></a>';
        echo '</li>';

        if ($loggedin) {
          $icon = 'bi-envelope-fill';//bi-envelope-exclamation-fill
          $show = '';
          if ($messages > 0) { $show = $messages; }

          echo '<li>';
              echo '<a class="'.$icon.'" style="margin:10px;" href="../pages/notifications.php"> '.$show.' </a>';
              echo '<span style="width:50px;"></span>';
              echo '<a href="../pages/profile.php">  '.$user_fname.' </a>';
          echo '</li>';
        }

      echo '</ul>';
    //echo '</div>';
  }

  public function show_secondary_nav($loggedin, $active_tab) {

    $current_style = 'primary';
    $active_style = 'dark';

    $manage = $current_style;
    $view = $current_style;

    if ($active_tab == 'Manage') {
      $manage = $active_style;
    } elseif ($active_tab == 'View') {
      $view = $active_style;
    } 

    if ($loggedin) {
      //echo '<div class="divSecondaryNavbar">';
        echo '<ul class="secondaryNavbarUL">';

          echo '<li>';
            echo '<a href="../pages/manage.php" class="btn btn-'.$manage.' btn-sm">Manage</a>';
          echo '</li>';
          echo '<li>';
            echo '<a href="../pages/view.php" class="btn btn-'.$view.' btn-sm">View</a>';
          echo '</li>';

        echo '</ul>';

      //echo '</div>';
    }
  }

}
