<?php

class Navbar {

  public function show_header_nav($loggedin, $user_fname, $id_role, $messages){
    //echo '<div class="divMainNavbar">';
      echo '<ul class="mainNavbarUL">';

        echo '<li id="mainNavFirstLI">';
          //echo '<i class="bi-yin-yang" style="margin:10px;"></i> Life Management ';
          //echo '<p>';
            echo '<img id="mainLogo" src="../pics/LMS_Fox_03_Small_Trans.png"></img>';
          //echo '</p>';

          //echo '<a href="../pages/manage.php"><i class="bi-three-dots" style="margin:10px;"></i></a>';
        echo '</li>';

        if ($loggedin) {
          $icon = 'bi-envelope-fill';//bi-envelope-exclamation-fill
          $show = '';
          if ($messages > 0) { $show = $messages; }

          echo '<li id="mainNavSecondLI">';
            echo '<div id="userLinksDiv">';
              echo '<a class="'.$icon.'" href="../pages/notifications.php"> '.$show.' </a>';
              echo '<span style="width:15px;"></span>';
              echo '<a class="bi-person-circle" href="../pages/profile.php"></a>';
            echo '</div>';
          echo '</li>';
        } else {
          echo '<li>';  // I do this so that the style for last child doesn't make the main Life Management title on the right...
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
          echo '<li class="firstSecondaryLI">';
            echo '<a href="../pages/manage.php" class="btn btn-'.$manage.' btn-sm">Manage</a>';
          echo '</li>';
          echo '<li class="secondSecondaryLI">';
            echo '<a href="../pages/view.php" class="btn btn-'.$view.' btn-sm">View</a>';
          echo '</li>';

        echo '</ul>';

      //echo '</div>';
    }
  }

}
