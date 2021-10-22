<?php
declare(strict_types = 1);
include '../includes/autoloader.inc.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Life Management</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.6.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../css/style.css">

</head>
<body>

<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="home.php">
        <p class="bi-yin-yang"></p>
      </a>
      <?php
        //use Style\Navbar;
        $navbar = new Navbar();
        $navbar->show_header_nav();
      ?>
    </div>
  </div>
</nav>

<div class="container-fluid text-center">
  <div class="row content">
    <div class="col-sm-2 sidenav">
      <p class="bi-card-list" style="font-size: 1rem; color: white;"><a href="#"> Plans</a></p>
      <p class="bi-list-check" style="font-size: 1rem; color: white;"><a href="#"> Goals</a></p>
      <p class="bi-lightbulb" style="font-size: 1rem; color: white;"><a href="#"> Ideas</a></p>
    </div>
    <div class="col-sm-8 text-left">
      <h1>Plans</h1>
      <table class="table table-dark" style="background-color:#3a5774;">
        <tr>
          <th>Name</th>
          <th>Description</th>
          <th style="background-color: rgb(33, 37, 46);"></td>
        </tr>
        <tr>
          <td>VanLyfe</td>
          <td>This plan is about living in a van in downtown Greenville.</td>
          <td style="background-color: rgb(33, 37, 46);">
            <span style="display:flex;">
              <p class="bi-pencil-fill" style="color:white;"></p>
              <p class="bi-plus-circle" style="color:white;"></p>
              <p class="bi-trash-fill" style="color:white;"></p>
            </span>

          </td>
        </tr>
        <tr>
          <td>LandLord</td>
          <td>This plan is about buying land and a house or two houses, then renting out one of them for passive income.</td>
          <td style="background-color: rgb(33, 37, 46);">
            <p class="bi-pencil-fill" style="color:white;"></p>
          </td>
        </tr>
      </table>



    </div>

  </div>
</div>

<footer class="container-fluid text-center">
  <p class="bi-egg" style="color:white;"></p>
</footer>

</body>
</html>
