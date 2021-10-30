<?php
declare(strict_types = 1);
include '../includes/autoloader.inc.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php
    $header = new Header();
    $header->show_header();
  ?>
</head>
<body>

<?php
  //use Style\Navbar;
  $navbar = new Navbar();
  $navbar->show_header_nav();
?>

<div class="container-fluid text-center">
  <div class="row content">
    <!--
      <div class="col-sm-2 sidenav">
        <p class="bi-card-list" style="font-size: 1rem; color: white;"><a href="#"> Plans</a></p>
        <p class="bi-list-check" style="font-size: 1rem; color: white;"><a href="#"> Goals</a></p>
        <p class="bi-lightbulb" style="font-size: 1rem; color: white;"><a href="#"> Ideas</a></p>
      </div>
    -->
    <div class="col-sm-8 text-left">
      <?php

      ?>
    </div>

  </div>
</div>

<footer class="container-fluid text-center">
  <p class="bi-egg" style="color:white;"></p>
</footer>

</body>
</html>
