<?php


class Footer {

  public function show_footer(){
    $current_version = '0.15.12.1';
    echo '<footer class="container-fluid text-center">';
      echo '<p class="bi-egg" style="color:white;"></p>';
      echo '<p style="text-align:center; padding-left:10px; color:rgb(47, 115, 152);">'.$current_version.'</p>';
    echo '</footer>';

  }

}
