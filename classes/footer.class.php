<?php


class Footer {

  public function show_footer(){
    $current_version = '1.33.47.13';

    echo '<footer class="container-fluid text-center">';
      echo '<p class="bi-egg-fill" style="color:white;"></p>';
      echo '<p style="text-align:center; font-weight:bold;">'.$current_version.'</p>';
    echo '</footer>';

  }

}
