<?php

class FinanceNavbar {

  public function show_header_nav(){
    echo '<div class="container" style="text-align:center;">';
    echo '<h1 style="text-align:center;">Finances</h1>';
      echo '<p>';
        echo '<a href="../pages/finances.php" class="btn btn-primary btn-sm">Monthly Overview</a>';
      echo '</p>';
      echo '<p>';
        echo '<a href="../pages/yearly.php" class="btn btn-primary btn-sm">Yearly Overview</a>';
      echo '</p>';
      echo '<p>';
        echo '<a href="../pages/budgets.php" class="btn btn-primary btn-sm">Budgets</a>';
      echo '</p>';
      //echo '<p>';
        //echo '<a href="../pages/ious.php" class="btn btn-primary btn-sm">IOUs</a>';
      //echo '</p>';
    echo '</div>';
    echo '<br>';
  }

}
