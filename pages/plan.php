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
    <div id="left_sidenav" class="col-sm-2 sidenav">
      <p class="bi-card-list" style="font-size: 1rem; color: white;"><a href="#"> Plans</a></p>
      <p class="bi-list-check" style="font-size: 1rem; color: white;"><a href="#"> Goals</a></p>
      <p class="bi-lightbulb" style="font-size: 1rem; color: white;"><a href="#"> Ideas</a></p>
    </div>
    <div class="col-sm-8 text-left">
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
    <div id="right_sidenav" class="col-sm-2 sidenav">
      <div class="well">
        <p>(Monthly Finances)</p>
      </div>
      <div class="well">
        <p>Incomes: $1,500</p>
        <p>Expenses: $500</p>
      </div>
      <div class="well">
        <p>Savings: $1,000</p>
      </div>
    </div>

  </div>
</div>

<footer class="container-fluid text-center">
  <p class="bi-egg" style="color:white;"></p>
</footer>

</body>
</html>
