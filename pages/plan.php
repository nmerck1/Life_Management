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

<div class="container">
  <!--
  <div class="row content">
    <div id="left_sidenav" class="col-sm-2 sidenav">
      <p class="bi-card-list" style="font-size: 1rem; color: white;"><a href="#"> Plans</a></p>
      <p class="bi-list-check" style="font-size: 1rem; color: white;"><a href="#"> Goals</a></p>
      <p class="bi-lightbulb" style="font-size: 1rem; color: white;"><a href="#"> Ideas</a></p>
    </div>
  -->
    <div>
      <?php
        $plan_id;
        if (isset($_GET['plan_id'])){
          $plan_id = $_GET['plan_id'];
        }
        if ($plan_id != null && $plan_id != ''){
          // display everything for this plan:
          $get_plan = new Plan();
          $get_plan->show_this_plan((int)$plan_id, true);

        }
      ?>
    </div>
</div>

<?php
  $footer = new Footer();
  $footer->show_footer();
?>

</body>
</html>
