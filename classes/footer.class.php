<?php


class Footer {

  public function show_footer(){
    $current_version = '1.30.27.6';

    echo '<footer class="container-fluid text-center">';
      echo '<p class="bi-egg-fill" style="color:white;"></p>';
      echo '<p style="text-align:center; color:rgb(47, 115, 152);">'.$current_version.'</p>';
    echo '</footer>';

  }

}
