<?php


class Footer {

  public function show_footer(){
    $current_version = '1.37.58.17';

    echo '<div>';
      echo '<ul class="footerUL">';

        echo '<li class="footerFirstLI">';
          echo '<i class="bi-c-circle"></i> Life Management System';
        echo '</li>';

        echo '<li class="footerSecondLI">v'.$current_version;
        echo '</li>';

      echo '</ul>';
    echo '</div>';





  }

}
