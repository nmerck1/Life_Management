<?php
//declare(strict_types = 1);
include '../includes/autoloader.inc.php';
include '../includes/function_library.inc.php';

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../pages/login.php");
    exit;
}

$loggedin = $_SESSION['loggedin'];
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];
$id_role = $_SESSION['id_role'];

// check messages on every page
$messages = library_get_num_notifications($user_id);

// Prepare a select statement
//echo "user_id: ". $user_id."<br>";
//echo "id_role: ". $id_role."<br>";
$sql = "
    SELECT *
    FROM users
    WHERE user_id = '".$user_id."'
    AND is_active = 1
";
//echo $sql;
$dbh = new Dbh();
$stmt = $dbh->connect()->query($sql);
//echo $sql;
// should only populate one row of data
while ($row = $stmt->fetch()) {
  $user_name = $row['user_name'];
  $user_fname = $row['user_fname'];
  $user_lname = $row['user_lname'];
  $pass_word = $row['pass_word'];
  //echo "user_fname: ".$user_fname."<br>";
}
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
  $navbar->show_header_nav($loggedin, $user_fname, $id_role, $messages);





  // this is for looking at previous finance dates in the system
  $date_search = date('Y-m-d');
  if (isset($_POST['date_search'])) {
    $date_search = $_POST['date_search'];
  }
  //echo "date_search: ".$date_search."<br>";

  // start the outer table
  echo '<div class="container" style="max-width:100%;">';

    //echo '<div class="container">';
      echo '<h1 style="text-align:center;">Diet Overview</h1>';
      $day = date('w', strtotime($date_search));
      $show_today = date('m/d', strtotime($date_search));
      //echo "day: ".$day."<br>";
      //echo "week_start: ".$week_start."<br>";
      //echo "week_end: ".$week_end."<br>";
      echo '<h2 style="text-align:center;">'.$show_today.'</h2>';
      // mini form for displaying different dates in history
      echo '<form method="post" action="../pages/diet.php" style="text-align:center;">';
        echo '<input type="date" name="date_search" value="'.$date_search.'"></input>';
        echo '<button type="submit" name="submit_search" class="btn btn-primary btn-sm" value="Display">Display Day</button>';
      echo '</form>';
    //echo '</div>';

    echo '<br>';

    echo '<div class="container">';
      echo '<p style="text-align:center;">Food Log</p>';
      echo '<table class="table table-dark" style="background-color:#3a5774; width:400px; margin:auto;">';
        echo '<tr>';

          $time = strtotime($show_today);
          $next_date = date('D', $time);
          $format_num = date('m-d', $time);
          echo '<th style="text-align:center;">' .$next_date. ' <span style="color:black;">('.$format_num.')</span></th>';// style="width:225px;"

          echo '<th style="background-color:rgb(33, 37, 46); width:10px;">';
            echo '<a href="../includes/diet.inc.php?user_id='.$user_id.'"><p class="bi-plus-circle" style="color:white;"></p></a>';
          echo '</th>';

        echo '</tr>';
        echo '<tr>';

            $days_with_logs = 0;
            $total_calories = 0;
            $total_carbs = 0;
            $total_protein = 0;
            $total_fat = 0;

            echo '<td style="background-color: rgb(25, 29, 32);">';
              $time_check = date('Y-m-d', strtotime($show_today));
              //echo "time: ".$time."<br>";
              //echo "time_check: ".$time_check."<br>";
              // make the sql for this specific day
              $sql = "
                SELECT *
                FROM food_logs fl
                LEFT JOIN measurements m ON fl.id_mea = m.mea_id
                WHERE fl.is_active = 1
                AND fl.fl_log_date >= TIMESTAMP('".$time_check." 00:00:00')
                AND fl.fl_log_date <= TIMESTAMP('".$time_check." 23:59:59')
                AND fl.id_user = '".$user_id."';
              ";
              //echo $sql;
              $dbh = new Dbh();
              $stmt = $dbh->connect()->query($sql);

              $calories = 0;
              $carbs = 0;
              $protein = 0;
              $fat = 0;

              $build_string = '';
              $breakfast_items_string = '';
              $lunch_items_string = '';
              $dinner_items_string = '';
              $snacks_items_string = '';

              $stmt_get_rows = $conn->prepare($sql);
              $stmt_get_rows->execute();
              $stmt_get_rows->store_result();
              $num_rows = mysqli_stmt_num_rows($stmt_get_rows);
              if ($num_rows > 0) {
                echo '<p class="my_paragraph" style="color:grey; text-align:center;"> [Foods] </p>';
                echo '<ul class="my_list">';
                while ($row = $stmt->fetch()) {
                  $mea_abbr = $row['mea_abbr'];
                  if ($mea_abbr == 'Other') { $mea_abbr = ''; }

                  $build_string .= '<li class="my_li">';

                  $build_string .= '<a id="diet_li_a" href="../includes/diet.inc.php?selected_id='.$row['fl_id'].'&update_type=Edit&user_id='.$user_id.'"><p class="bi-pencil-fill" style="color:white;"> </p> </a>';
                  $build_string .= '<a id="diet_li_a" href="../ajax/diet.ajax.php?selected_id='.$row['fl_id'].'&update_type=Delete&user_id='.$user_id.'"><p class="bi-trash-fill" style="color:white;"> </p> </a>';

                  $build_string .= ' x'.$row['fl_quantity'].' '.$row['fl_name'];//.' ('.$row['fl_amount'].$mea_abbr.')';

                  $build_string .= '<a id="diet_li_a" href="../includes/diet.inc.php?selected_id='.$row['fl_id'].'&update_type=Copy&user_id='.$user_id.'"><p class="bi-clipboard-plus" style="color:white;"> </p> </a>';

                  $build_string .= '</li><br><br>';

                  $calories += ($row['fl_calories'] * $row['fl_quantity']);
                  $carbs += ($row['fl_carbs'] * $row['fl_quantity']);
                  $protein += ($row['fl_protein'] * $row['fl_quantity']);
                  $fat += ($row['fl_fat'] * $row['fl_quantity']);
                  // check what meal time this is

                  if ($row['fl_meal_time'] == 'Breakfast'){ $breakfast_items_string .= $build_string; }
                  elseif ($row['fl_meal_time'] == 'Lunch'){ $lunch_items_string .= $build_string; }
                  elseif ($row['fl_meal_time'] == 'Dinner'){ $dinner_items_string .= $build_string; }
                  elseif ($row['fl_meal_time'] == 'Snacks'){ $snacks_items_string .= $build_string; }
                  $build_string = '';
                }
                echo '</ul>';
                // now echo out each meal time in order:
                echo '<ul class="my_list">';
                if ($breakfast_items_string != '') {
                  echo '<li class="my_li" style="list-style-type:none; color:grey; width:100%;">(Breakfast)</li><br>';
                  echo $breakfast_items_string;
                }
                if ($lunch_items_string != '') {
                  echo '<li class="my_li" style="list-style-type:none; color:grey; width:100%;">(Lunch)</li><br>';
                  echo $lunch_items_string;
                }
                if ($dinner_items_string != '') {
                  echo '<li class="my_li" style="list-style-type:none; color:grey; width:100%;">(Dinner)</li><br>';
                  echo $dinner_items_string;
                }
                if ($snacks_items_string != '') {
                  echo '<li class="my_li" style="list-style-type:none; color:grey; width:100%;">(Snacks)</li><br>';
                  echo $snacks_items_string;
                }
                echo '</ul>';
              } else {
                echo '<p style="color:grey;">(No foods logged)</p>';
              }

              echo '<br>';
              echo '<br>';

              if ($calories != 0 && $carbs != 0 && $protein != 0 && $fat != 0) {
                echo '<p class="my_paragraph" style="color:grey; text-align:center;"> [Totals] </p>';
                  echo '<ul class="my_list">';
                    echo '<li class="my_li">Calories: '.number_format($calories, 1).'</li>';
                    echo '<br>';
                    echo '<li class="my_li">Carbs: '.number_format($carbs, 1).'g</li>';
                    echo '<br>';
                    echo '<li class="my_li">Protein: '.number_format($protein, 1).'g</li>';
                    echo '<br>';
                    echo '<li class="my_li">Fat: '.number_format($fat, 1).'g</li>';
                    echo '<br>';
                  echo '</ul>';
                  $total_calories += $calories;
                  $total_carbs += $carbs;
                  $total_protein += $protein;
                  $total_fat += $fat;
              }

            echo '</td>';

            echo '<td style="background:rgb(33, 37, 46);"></td>';
          echo '</tr>';

        echo '</table>';
        echo '<br>';
      echo '</div>';
  echo '</div>';
  echo '<br>';



  /*
    echo '<table class="table table-dark" style="background-color:#3a5774;">';    // main table
      echo '<tr>';

        echo '<td>';
          echo '<h1 style="text-align:center;">Diet Overview</h1>';
          $day = date('w', strtotime($date_search));
          $week_start = date('m/d', strtotime($date_search.'-'.$day.' days'));
        	$week_end = date('m/d', strtotime($date_search.'+'.(6-$day).' days'));
          $show_week = $week_start.' - '.$week_end.'<br>';
          //echo "day: ".$day."<br>";
          //echo "week_start: ".$week_start."<br>";
          //echo "week_end: ".$week_end."<br>";
          echo '<h2 style="text-align:center;">'.$show_week.'</h2>';
          // mini form for displaying different dates in history
          echo '<form method="post" action="../pages/diet.php" style="text-align:center;">';
            echo '<input type="date" name="date_search" value="'.$date_search.'"></input>';
            echo '<button type="submit" name="submit_search" class="btn btn-primary btn-sm" value="Display">Display Week</button>';
          echo '</form>';
        echo '</td>';

      echo '</tr>';
      echo '<tr>';

        echo '<td style="background: rgb(33, 37, 46);">Food Logs</td>';

      echo '</tr>';
      echo '<tr>';

        echo '<td>';
        echo '<table class="table table-dark" style="background-color:#3a5774;">';
          echo '<tr>';

          // loop for days of this week
          for ($i=0; $i<7; $i++){
            $time = strtotime($week_start.'+'.$i.' days');
            $next_date = date('D', $time);
            $format_num = date('m-d', $time);
            echo '<th style="text-align:center;">' .$next_date. ' <span style="color:black;">('.$format_num.')</span></th>';// style="width:225px;"
          }
          echo '<th style="background-color: rgb(33, 37, 46);">';
            echo '<a href="../includes/diet.inc.php?user_id='.$user_id.'"><p class="bi-plus-circle" style="color:white;"></p></a>';
          echo '</th>';

          echo '</tr>';
          echo '<tr>';

              $days_with_logs = 0;
              $total_calories = 0;
              $total_carbs = 0;
              $total_protein = 0;
              $total_fat = 0;

              for ($i=-1; $i<6; $i++){
                //echo '<td style="background-color: rgb(25, 29, 32);">';
                  // actions
                  //echo '<a href="../includes/diet.inc.php?selected_id='.$row['fl_id'].'&update_type=Edit&user_id='.$user_id.'"><p class="bi-pencil-fill" style="color:white;"></p></a>';
                  //echo '<a href="../ajax/diet.ajax.php?selected_id='.$row['fl_id'].'&update_type=Delete&user_id='.$user_id.'"><p class="bi-trash-fill" style="color:white;"></p></a>';
                //echo '</td>';
                echo '<td style="background-color: rgb(25, 29, 32);">';
                  $time = date('Y-m-d', strtotime($week_start.'+'.$i.' days'));
                  $time_check = date('Y-m-d', strtotime($week_start.'+'.($i+1).' days'));
                  //echo "time: ".$time."<br>";
                  //echo "time_check: ".$time_check."<br>";
                  // make the sql for this specific day
                  $sql = "
                    SELECT *
                    FROM food_logs fl
                    LEFT JOIN measurements m ON fl.id_mea = m.mea_id
                    WHERE fl.is_active = 1
                    AND fl.fl_log_date >= TIMESTAMP('".$time_check." 00:00:00')
                    AND fl.fl_log_date <= TIMESTAMP('".$time_check." 23:59:59')
                    AND fl.id_user = '".$user_id."';
                  ";
                  //echo $sql;
                  $dbh = new Dbh();
                  $stmt = $dbh->connect()->query($sql);

                  $calories = 0;
                  $carbs = 0;
                  $protein = 0;
                  $fat = 0;

                  $build_string = '';
                  $breakfast_items_string = '';
                  $lunch_items_string = '';
                  $dinner_items_string = '';
                  $snacks_items_string = '';

                  $stmt_get_rows = $conn->prepare($sql);
                  $stmt_get_rows->execute();
                  $stmt_get_rows->store_result();
                  $num_rows = mysqli_stmt_num_rows($stmt_get_rows);
                  if ($num_rows > 0) {
                    echo '<p class="my_paragraph" style="color:grey; text-align:center;"> [Foods] </p>';
                    echo '<ul class="my_list">';
                    while ($row = $stmt->fetch()) {
                      $mea_abbr = $row['mea_abbr'];
                      if ($mea_abbr == 'Other') { $mea_abbr = ''; }

                      $build_string .= '<li class="my_li">';

                      $build_string .= '<a id="diet_li_a" href="../includes/diet.inc.php?selected_id='.$row['fl_id'].'&update_type=Edit&user_id='.$user_id.'"><p class="bi-pencil-fill" style="color:white;"></p></a>';
                      $build_string .= '<a id="diet_li_a" href="../ajax/diet.ajax.php?selected_id='.$row['fl_id'].'&update_type=Delete&user_id='.$user_id.'"><p class="bi-trash-fill" style="color:white;"></p></a>';

                      $build_string .= ' x'.$row['fl_quantity'].' '.$row['fl_name'];//.' ('.$row['fl_amount'].$mea_abbr.')';

                      $build_string .= '</li><br><br>';

                      $calories += ($row['fl_calories'] * $row['fl_quantity']);
                      $carbs += ($row['fl_carbs'] * $row['fl_quantity']);
                      $protein += ($row['fl_protein'] * $row['fl_quantity']);
                      $fat += ($row['fl_fat'] * $row['fl_quantity']);
                      // check what meal time this is

                      if ($row['fl_meal_time'] == 'Breakfast'){ $breakfast_items_string .= $build_string; }
                      elseif ($row['fl_meal_time'] == 'Lunch'){ $lunch_items_string .= $build_string; }
                      elseif ($row['fl_meal_time'] == 'Dinner'){ $dinner_items_string .= $build_string; }
                      elseif ($row['fl_meal_time'] == 'Snacks'){ $snacks_items_string .= $build_string; }
                      $build_string = '';
                    }
                    // now echo out each meal time in order:
                      echo '<ul class="my_list">';
                      if ($breakfast_items_string != '') {
                        echo '<li class="my_li" style="list-style-type:none; color:grey; width:100%;">(Breakfast)</li><br>';
                        echo $breakfast_items_string;
                      }
                      if ($lunch_items_string != '') {
                        echo '<li class="my_li" style="list-style-type:none; color:grey; width:100%;">(Lunch)</li><br>';
                        echo $lunch_items_string;
                      }
                      if ($dinner_items_string != '') {
                        echo '<li class="my_li" style="list-style-type:none; color:grey; width:100%;">(Dinner)</li><br>';
                        echo $dinner_items_string;
                      }
                      if ($snacks_items_string != '') {
                        echo '<li class="my_li" style="list-style-type:none; color:grey; width:100%;">(Snacks)</li><br>';
                        echo $snacks_items_string;
                      }
                      echo '</ul>';


                    echo '</ul>';
                    $days_with_logs++;
                  } else {
                    echo '<p style="color:grey;">(No foods logged)</p>';
                  }

                  echo '<br>';
                  echo '<br>';

                  if ($calories != 0 && $carbs != 0 && $protein != 0 && $fat != 0) {
                    echo '<p class="my_paragraph" style="color:grey; text-align:center;"> [Totals] </p>';
                      echo '<ul class="my_list">';
                        echo '<li class="my_li">Calories: '.number_format($calories, 1).'</li>';
                        echo '<br>';
                        echo '<li class="my_li">Carbs: '.number_format($carbs, 1).'g</li>';
                        echo '<br>';
                        echo '<li class="my_li">Protein: '.number_format($protein, 1).'g</li>';
                        echo '<br>';
                        echo '<li class="my_li">Fat: '.number_format($fat, 1).'g</li>';
                        echo '<br>';
                      echo '</ul>';
                      $total_calories += $calories;
                      $total_carbs += $carbs;
                      $total_protein += $protein;
                      $total_fat += $fat;
                  }

              echo '</td>';
            }
            $average_calories = 0;
            $average_carbs = 0;
            $average_protein = 0;
            $average_fat = 0;
            if ($days_with_logs > 0) {
              $average_calories = round($total_calories / $days_with_logs, 2);
              $average_carbs = round($total_carbs / $days_with_logs, 2);
              $average_protein = round($total_protein / $days_with_logs, 2);
              $average_fat = round($total_fat / $days_with_logs, 2);
            }

            //echo 'average_calories: '.$average_calories. '<br>';
            //echo 'days_with_logs: '.$days_with_logs;
            echo '<td style="background:rgb(33, 37, 46);"></td>';
          echo '</tr>';

          echo '</table>';
        echo '</td>';

      echo '<tr>';
        echo '<td>[Averages per day]';
          echo '<p>Calories: '.$average_calories.'</p>';
          echo '<p>Carbs: '.$average_carbs.'g</p>';
          echo '<p>Protein: '.$average_protein.'g</p>';
          echo '<p>Fat: '.$average_fat.'g</p>';
        echo '</td>';
      echo '</tr>';

      echo '</tr>';
    echo '</table>';
    */






  $footer = new Footer();
  $footer->show_footer();
?>

</body>
</html>
