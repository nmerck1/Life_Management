<?php


class Finance extends Dbh {

  public function show_all_finances_table(bool $editable) {
    echo '<table class="table table-dark" style="background-color:#3a5774;">';
    echo '<tr>';
      echo '<td>Incomes</td>';
      echo '<td>Expenses</td>';
    echo '</tr>';
    echo '<tr>';
      echo '<td>'.$this->show_this_finance_table('Incomes', $editable).'</td>';
      echo '<td>'.$this->show_this_finance_table('Expenses', $editable).'</td>';
    echo '</tr>';
    echo '</table>';
  }

  public function show_this_finance_table(string $finance_type, bool $editable) {
    // check which table:
    $stmt;
    $tbl_abbr;
    if ($finance_type == 'Incomes') {
      $sql = "SELECT * FROM finance_incomes WHERE is_active = 1;";
      $stmt = $this->connect()->query($sql);
      $tbl_abbr = 'fi';
    } elseif ($finance_type == 'Expenses') {
      $sql = "SELECT * FROM finance_expenses WHERE is_active = 1;";
      $stmt = $this->connect()->query($sql);
      $tbl_abbr = 'fe';
    }
    //echo '<h1>'.$finance_type.'</h1>';
    echo '<table class="table table-dark" style="background-color:#3a5774;">';
    echo '<tr>';
      echo '<th>Company</th>';
      echo '<th>Name</th>';
      echo '<th>Amount</th>';
      echo '<th>Date</th>';
    echo '</tr>';
    while ($row = $stmt->fetch()) {
      echo '<tr>';
        echo '<td>' .$row[$tbl_abbr.'_company']. '</td>';
        echo '<td>' .$row[$tbl_abbr.'_name']. '</td>';
        echo '<td style="text-align:right;">' .$row[$tbl_abbr.'_amount']. '</td>';
        echo '<td>' .$row[$tbl_abbr.'_date']. '</td>';
        /*
        if ($editable) {
          echo '<td style="background-color: rgb(33, 37, 46);">
                  <span style="display:flex;">
                    <a href="plan.php?plan_id='.$row['plan_id'].'&action=Edit"><p class="bi-pencil-fill" style="color:white;"></p></a>
                    <a href="vision.php?plan_id='.$row['plan_id'].'&action=Remove"><p class="bi-trash-fill" style="color:white;"></p></a>
                  </span>
                </td>';
        }
        */
      echo '</tr>';
    }
    echo '</table>';
  }


  public function show_yearly_finances_table(){

  }

}
