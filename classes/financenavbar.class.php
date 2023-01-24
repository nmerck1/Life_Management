<?php

class FinanceNavbar {

  public function show_header_nav($active_tab, $secondary_tab){

    $current_style = 'primary';
    $active_style = 'dark';

    $monthly = $current_style;
    $yearly = $current_style;
    $budgets = $current_style;
    $ious = $current_style;

    if ($active_tab == 'Monthly') {
      $monthly = $active_style;
    } elseif ($active_tab == 'Yearly') {
      $yearly = $active_style;
    } elseif ($active_tab == 'Budgets') {
      $budgets = $active_style;
    } elseif ($active_tab == 'IOUs') {
      $ious = $active_style;
    }

    echo '<div class="container" style="text-align:center;">';
      //echo '<h1 style="text-align:center;">Finances</h1>';
      if ($secondary_tab == 'Manage') {
        echo '<p>';
          echo '<a href="../pages/manage.php" class="btn btn-'.$monthly.' btn-sm">Monthly</a>';
        echo '</p>';
        echo '<p>';
          echo '<a href="../pages/budgets.php" class="btn btn-'.$budgets.' btn-sm">Budgets</a>';
        echo '</p>';
        echo '<p>';
          echo '<a href="../pages/ious.php" class="btn btn-'.$ious.' btn-sm">IOUs</a>';
        echo '</p>';
      } elseif ($secondary_tab == 'View') {
          echo '<p>';
            echo '<a href="../pages/view.php" class="btn btn-'.$monthly.' btn-sm">Monthly</a>';
          echo '</p>';
          echo '<p>';
            echo '<a href="../pages/yearly.php" class="btn btn-'.$yearly.' btn-sm">Yearly</a>';
          echo '</p>';
      }

    echo '</div>';
    echo '<br>';
  }

}
