<?php

class Header {

  private $is_index = 2;

  public function show_header($user_theme){
    echo '<link rel="icon" href="../assets/img/logos/fox.png" style="background-color: transparent;">';
    echo '<title>Life Management</title>';

    echo '<meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
    echo '<meta http-equiv="X-UA-Compatible" content="IE=9" />';

    // external links
    echo '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">';
    echo '<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>';
    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>';
    echo '<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>';
    echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.0/font/bootstrap-icons.css">';
    echo '<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>';

    // new design include styles and js folders
    echo '<link href="../assets/css/new-design/pace.min.css" rel="stylesheet"/>';
    echo '<script src="../assets/js/new-design/pace.min.js"></script>';
    echo '<link href="../assets/css/new-design/bootstrap.min.css" rel="stylesheet"/>';
    echo '<link href="../assets/css/new-design/animate.css" rel="stylesheet" type="text/css"/>';
    echo '<link href="../assets/css/new-design/icons.css" rel="stylesheet" type="text/css"/>';
    echo '<link href="../assets/css/new-design/app-style.css" rel="stylesheet"/>';

    if ($this->is_index == 1) {
      echo '<link rel="stylesheet" type="text/css" href="../assets/css/themes/site.css">';
      if ($user_theme == '') { $user_theme = 'standard_blue.css'; }
      //echo '<link rel="stylesheet" type="text/css" href="../assets/css/themes/'.$user_theme.'">';
    } else {
      echo '<link rel="stylesheet" type="text/css" href="../assets/css/themes/site.css">';
      if ($user_theme == '') { $user_theme = 'standard_blue.css'; }
      //echo '<link rel="stylesheet" type="text/css" href="../assets/css/themes/'.$user_theme.'">';
    }
  }
}
