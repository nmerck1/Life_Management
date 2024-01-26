<?php


class Plan extends Dbh {

  //private $proj_name;
  //private $proj_desc;

  public function show_this_plan(int $plan_id, bool $editable){
    //sql for getting plan name and desc
    $sql_plan = "SELECT * FROM plans WHERE plan_id=$plan_id;";
    $stmt = $this->connect()->query($sql_plan);
    $row = $stmt->fetch();  // show only be one
    // get row data
    $plan_name = $row['plan_name'];
    $plan_desc = $row['plan_desc'];

    // display header:
    echo '<br>';
    echo '<h1 style="text-align:center;">"'.$plan_name.'"</h1>';
    echo '<p style="text-align:center; color:white;">'.$plan_desc.'</p>';
    echo '<br>';

    // sql for getting plan assets
    $sql_assets = "SELECT pa.plan_asset_id,
                         a.asset_id,
                         a.asset_name,
                         a.asset_type,
                         a.asset_desc,
                         a.asset_owned,
                         a.asset_mthly_finance,
                         a.asset_price,
                         a.url_link,
                         a.is_active,
                         pa.is_active
                  FROM plan_assets pa
                  LEFT JOIN plans p ON pa.id_plan = p.plan_id
                  LEFT JOIN assets a ON pa.id_asset = a.asset_id
                  WHERE p.plan_id = '$plan_id';
                  AND pa.is_active = 1 AND a.is_active = 1
                  ";
    $stmt = $this->connect()->query($sql_assets);

    // display table
    echo '<h1>Plan Assets</h1>';
    echo '<table class="table table-dark" style="background-color:#3a5774;">
            <tr>
              <th>Name</th>
              <th>Type</th>';
              //<th>Description</th>
        echo '<th>URL</th>
              <th>Owned?</th>
              <th>Financing</th>
              <th>Total Price</th>';
              if ($editable) {
                echo '<th style="background-color: rgb(33, 37, 46);">
                        <a href="plan.php?plan_id='.$row['plan_id'].'&action=Add"><p class="bi-plus-circle" style="color:white;"></p></a>
                      </th>';
              }
      echo '</tr>';
      // counters
      $row_counter = 0;
      $total_financing = 0;
      $total_price = 0;
      while ($row = $stmt->fetch()) {
        echo '<tr>';
          echo '<td>' .$row['asset_name']. '</td>';
          echo '<td style="color:grey;">' .$row['asset_type']. '</td>';
          //echo '<td>' .$row['asset_desc']. '</td>';
          echo '<td><a href="'.$row['url_link'].'" style="">'.$row['url_link'].'</a></td>';
          echo '<td>';
            if ($row['asset_owned'] == 1){
              echo '<p class="bi-check2" style="color:#69d369; text-align:center;"></p>';
            } else{
              echo '<p class="bi-x" style="color:red; text-align:center;"></p>';
            }
          echo '</td>';
          echo '<td style="text-align:right;"> $', number_format($row['asset_mthly_finance'], 2), '</td>';
          // add finance to total
          $total_financing += $row['asset_mthly_finance'];
          echo '<td style="text-align:right;"> $', number_format($row['asset_price'], 2), '</td>';
          // add finance to total
          $total_price += $row['asset_price'];
          if ($editable) {
            echo '<td style="background-color: rgb(33, 37, 46);">
                    <span style="display:flex;">';
                    // this needs to have a button for removing via Ajax not an anchor tag
                echo '<p class="bi-trash-fill" style="color:white;"></p>
                    </span>
                  </td>';
          }
          $row_counter++;
      echo '</tr>';
      }
      if ($row_counter > 0){
        echo '<tr">';
            echo '<td style="background-color:rgb(33, 37, 46);" colspan=4>Totals: </td>';
            echo '<td style="background-color:rgb(33, 37, 46); text-align:right;"> $', number_format($total_financing, 2), '</td>';
            echo '<td style="background-color:rgb(33, 37, 46); text-align:right;"> $', number_format($total_price, 2), '</td>';
            echo '<td style="background-color: rgb(33, 37, 46);"></td>';
        echo '</tr>';
      }
      echo '</table>';
      if ($row_counter == 0){
        echo '<p style="color:white;"> (No assets) </p>';
      }
  }


  public function show_plans_table(bool $editable, bool $show_title){
    $sql = "SELECT * FROM plans;";
    $stmt = $this->connect()->query($sql);
    // echo out the table and loop through each row and add td
    if ($show_title) {
      echo '<h1>Plans</h1>';
    }
    echo '<table class="table table-dark" style="background-color:#3a5774;">
            <tr>
              <th>Name</th>
              <th>Description</th>';
              if ($editable) {
                echo '<th style="background-color: rgb(33, 37, 46);">
                        <a href="plan.php?action=Add"><p class="bi-plus-circle" style="color:white;"></p></a>
                      </th>';
              }
      echo '</tr>';
      while ($row = $stmt->fetch()) {
        echo '<tr>';
          echo '<td>' .$row['plan_name']. '</td>';
          echo '<td>' .$row['plan_desc']. '</td>';
          if ($editable) {
            echo '<td style="background-color: rgb(33, 37, 46);">
                    <span style="display:flex;">
                      <a href="plan.php?plan_id='.$row['plan_id'].'&action=Edit"><p class="bi-pencil-fill" style="color:white;"></p></a>
                      <a href="vision.php?plan_id='.$row['plan_id'].'&action=Remove"><p class="bi-trash-fill" style="color:white;"></p></a>
                    </span>
                  </td>';
          }
        echo '</tr>';
      }
    echo '</table>';
  }

}
