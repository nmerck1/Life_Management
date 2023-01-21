<?php

date_default_timezone_set('America/New_York');

// "global variables"
$g_servername = "localhost";
//$g_servername = "lifemanagement.me";
$g_username = "root";
$g_password = "";
$g_database = "lifement_life_management";
$g_port = 3306;

$is_server = false;

if ($is_server == true) {
	$g_username = "lifement_test";
	$g_password = "poopy";
	$g_database = "lifement_life_management";
	$g_port = 3306;
}
// Create connection
$conn = mysqli_connect(
	$g_servername,
	$g_username,
	$g_password,
	$g_database,
	$g_port
);



// gets a dropdown based on the categories table (this will be used in multiple places )
function library_get_categories_dropdown($cat_id){
	echo '<label>Category: </label>';
	$sql = "SELECT *
					FROM categories
					WHERE is_active = 1
					ORDER BY cat_name ASC;
	";
	$dbh = new Dbh();
	$stmt = $dbh->connect()->query($sql);
	echo '<select id="category">';
		while ($row = $stmt->fetch()) {
			if ($cat_id == $row['cat_id']) {
				echo '<option value="'.$row['cat_id'].'" selected="selected">'.$row['cat_name'].'</option>';
			} else {
				echo '<option value="'.$row['cat_id'].'">'.$row['cat_name'].'</option>';
			}

		}
	echo '</select>';
}


// gets a dropdown based on the categories table, but excludes any with names defined in the array parameter.
function library_exclude_get_categories_dropdown($exclude_names){
	echo '<label>Category: </label>';
	$sql = "SELECT *
					FROM categories
					WHERE is_active = 1
					ORDER BY cat_name ASC;
	";
	$dbh = new Dbh();
	$stmt = $dbh->connect()->query($sql);
	echo '<select id="category">';
		while ($row = $stmt->fetch()) {
			if ( !in_array($row['cat_name'], $exclude_names) ) {
				echo '<option value="'.$row['cat_id'].'" selected="selected">'.$row['cat_name'].'</option>';
			}

		}
	echo '</select>';
}

// gets a dropdown based on the categories table, but excludes any with names defined in the array parameter.
function library_get_users_exclude_current($current_user_id, $user_id_select){
	$sql = "SELECT *
					FROM users
					WHERE is_active = 1
					AND user_id != $current_user_id
					ORDER BY user_name ASC;
	";
	$dbh = new Dbh();
	$stmt = $dbh->connect()->query($sql);
	echo '<select id="user">';
		while ($row = $stmt->fetch()) {
			if ($row['user_id'] == $user_id_select) {
				echo '<option value="'.$row['user_id'].'" selected="selected">'.$row['user_name'].'</option>';
			} else {
				echo '<option value="'.$row['user_id'].'">'.$row['user_name'].'</option>';
			}

		}
	echo '</select>';
}

function library_get_num_notifications($user_id){
	$sql = "
					SELECT
						n.n_subject,
						n.n_send_date,
						n.n_read_date,
						n.is_active,

						fu.user_fname AS 'from_fname',
						fu.user_lname AS 'from_lname'
					FROM notifications n
					LEFT JOIN users fu ON n.n_from_user = fu.user_id
					WHERE n.is_active = 1
					AND n.n_to_user = ".$user_id."
					AND n.n_read_date < DATE('2020-01-01')

					ORDER BY n.n_send_date DESC;
	";
	//echo $sql;
	$dbh = new Dbh();
	$stmt = $dbh->connect()->query($sql);

	$messages = 0;
	while ($row = $stmt->fetch()) {
		$messages++;
	}
	return $messages;
}


function library_get_freq_dropdown($freq_value){
	// default to monthly
	if ($freq_value == '') { $freq_value = 'M'; }

	echo '<label>Frequency: </label>';
	$all_freqs = array('M', 'Y'); // 'D', 'W',
	$freq_names = array('Monthly', 'Yearly'); //'Daily', 'Weekly',
	echo '<select id="freq" name="freq">';
		for ($i=0; $i<count($all_freqs); $i++) {
			if ($all_freqs[$i] == $freq_value) {
				echo '<option value="'.$freq_value.'" selected="selected">'.$freq_names[$i].'</option>';
			} else {
				echo '<option value="'.$freq_value.'">'.$freq_names[$i].'</option>';
			}

		}
	echo '</select>';
}

// this was changed to the company name as a filter instead of id since I already have a bunch of records and dont want
// to update the column as a id_company int type foreign key... so it will be the name right now,
// I have full control of what companies get added so just make sure they are distinct names, otherwise
// it will get duplicate records. Use SELECT DISTINCT comp_name in query for searching...
function library_get_companies_dropdown($comp_name){
	echo '<label>Company: </label>';
	echo '<input id="company" name="company" oninput="update_input_search(this.value);" value="'.$comp_name.'"></input>';
	echo '<div id="search_options_popup" name="search_options_popup" class="searchPopup">';
	echo '</div>';
}

function library_get_food_categories_dropdown($fc_id) {
	echo '<label>Food Category: </label>';
	$sql = "SELECT *
					FROM food_categories
					WHERE is_active = 1
					ORDER BY fc_name ASC;
	";
	$dbh = new Dbh();
	$stmt = $dbh->connect()->query($sql);
	echo '<select id="food_category" name="food_category" style="width:200px;">';
		while ($row = $stmt->fetch()) {
			if ($fc_id == $row['fc_id']) {
				echo '<option value="'.$row['fc_id'].'" selected="selected">'.$row['fc_name'].' <span style="color:grey;">('.$row['fc_desc'].')</span></option>';
			} else {
				echo '<option value="'.$row['fc_id'].'">'.$row['fc_name'].' <span style="color:grey;">('.$row['fc_desc'].')</span></option>';
			}
		}
	echo '</select>';
}

function library_get_measurements_dropdown($mea_id) {
	//echo '<label>Food Category: </label>';
	$sql = "SELECT *
					FROM measurements
					WHERE is_active = 1;
	";
	$dbh = new Dbh();
	$stmt = $dbh->connect()->query($sql);
	echo '<select id="measurement" name="measurement">';
		while ($row = $stmt->fetch()) {
			if ($mea_id == $row['mea_id']) {
				echo '<option value="'.$row['mea_id'].'" selected="selected">'.$row['mea_abbr'].'</option>';
			} else {
				echo '<option value="'.$row['mea_id'].'">'.$row['mea_abbr'].'</option>';
			}
		}
	echo '</select>';
}

function library_get_meal_time_dropdown($meal_time){
	// default to monthly
	if ($meal_time == '') { $meal_time = 'Breakfast'; }
	echo '<label>Meal Time: </label>';
	$meal_names = array('Breakfast', 'Lunch', 'Dinner', 'Snacks'); //'Daily', 'Weekly',
	echo '<select id="meal_time" name="meal_time">';
		for ($i=0; $i<count($meal_names); $i++) {
			if ($meal_names[$i] == $meal_time) {
				echo '<option value="'.$meal_names[$i].'" selected="selected">'.$meal_names[$i].'</option>';
			} else {
				echo '<option value="'.$meal_names[$i].'">'.$meal_names[$i].'</option>';
			}

		}
	echo '</select>';
}

// this method creates a line graph based on three arrays: incomes, expenses, savings (all for each month of the given year)
// $year_num, $incomes_array, $expenses_array, $savings_array
function library_year_line_graph($year_data_string) {

echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>';

	?>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	 <script type="text/javascript">
		 google.charts.load('current', {'packages':['corechart']});
		 google.charts.setOnLoadCallback(drawChart);

		 function drawChart() {
			var data = google.visualization.arrayToDataTable([
				['Month', 'Incomes', 'Expenses', 'Savings'],
				<?php
					echo $year_data_string;
			  ?>
			]);

			 var options = {
				 title: 'Incomes, Expenses & Savings',
				 curveType: 'function',
				 legend: { position: 'bottom' },
				 backgroundColor: 'black',
			 };

			 var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

			 chart.draw(data, options);
			 }
		</script>
		<?php
		echo '<div id="curve_chart" style="width: 90%; margin:auto; height: 500px"></div>';
		echo '<br>';
}



// this method creates a line graph based on three arrays: incomes, expenses, savings (all for each month of the given year)
// $year_num, $incomes_array, $expenses_array, $savings_array
function library_line_month_cat_graph($cat_title_data_string, $data_string) {

echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>';

	?>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	 <script type="text/javascript">
		 google.charts.load('current', {'packages':['corechart']});
		 google.charts.setOnLoadCallback(drawChart);

		 function drawChart() {
			var data = google.visualization.arrayToDataTable([
				//['Month', 'Expenses'],
				<?php
					echo $cat_title_data_string;//['Month', 'Category1', 'Category2', ...],
					echo $data_string;
			  ?>
			]);

			 var options = {
				 title: 'Monthly Categories Spending',
				 lineWidth: 3,
				 curveType: 'function',
				 legend: { position: 'bottom' },
				 backgroundColor: 'black',
			 };

			 var chart = new google.visualization.LineChart(document.getElementById('cat_monthly_curve_chart'));

			 chart.draw(data, options);
			 }
		</script>
		<?php
		echo '<div id="cat_monthly_curve_chart" style="width: 90%; margin:auto; height: 500px"></div>';
		echo '<br>';
}


// table functions

function library_incomes_table($user_id, $action, $current_page_num, $date_search, $show_per_page) {
		// add or subtract the page number depending on the action
		$get_current_page_num = 1;
	  if ($action == "Next") {
			$get_current_page_num = $current_page_num + 1;
		} elseif ($action == "Prev") {
			$get_current_page_num = $current_page_num - 1;
		}
		echo '<p id="Incomes_current_page_num" style="text-align:center; display:none;" value="'.$get_current_page_num.'">'.$get_current_page_num.'</p>'; //style="display:none;"
		echo '<p id="Incomes_page_show" style="text-align:center; color:grey;">(Page '.$get_current_page_num.')</p>';

		$get_sql_limit_min = ($show_per_page * $get_current_page_num) - $show_per_page;
		$get_sql_limit_max = ($show_per_page * $get_current_page_num);

		$sql = "
				SELECT fi.fi_id,
						fi.fi_company,
						fi.fi_name,
						fi.fi_amount,
						fi.fi_date
				FROM finance_incomes fi
				LEFT JOIN users u ON fi.id_user = u.user_id
				WHERE fi.is_active = 1
				AND u.user_id = ".$user_id."
				AND MONTH(fi.fi_date)=MONTH('".$date_search."')
				AND YEAR(fi.fi_date)=YEAR('".$date_search."')


	      ORDER BY fi.fi_date DESC
	      LIMIT ".$get_sql_limit_min.",".$show_per_page .";
		";
		//echo $sql;
		$dbh = new Dbh();
		$stmt = $dbh->connect()->query($sql);
		echo '<table class="table table-dark" style="text-align:center;">';
		echo '<tr>';
			echo '<th>Company</th>';
			echo '<th>Name</th>';
			echo '<th>Date</th>';
			echo '<th style="text-align:right;">Amount</th>';
			echo '<th class="end_row_options">';
				echo '<a href="../includes/finances.inc.php?form_type=Income&user_id='.$user_id.'"><i class="actions"><p class="bi-plus-circle"></p></i></a>';
			echo '</th>';
		echo '</tr>';
		$total_incomes_amount = 0;
		$is_alternate_row = false;
		$add_alternating_class = '';
		while ($row = $stmt->fetch()) {
				echo '<tr>';

				if ($is_alternate_row == false) {
					$add_alternating_class = '';
					$is_alternate_row = true;
				} else {
					$add_alternating_class = 'class="alternating_row"';
					$is_alternate_row = false;
				}
				echo '<td '.$add_alternating_class.' style="color:grey;">' .$row['fi_company']. '</td>';
				echo '<td '.$add_alternating_class.'>' .$row['fi_name']. '</td>';
				$date_string = strtotime($row['fi_date']);
				echo '<td '.$add_alternating_class.' style="color:grey;">' .date('M, d', $date_string). '</td>';
				echo '<td '.$add_alternating_class.' style="text-align:right;">' .number_format((float)$row['fi_amount'], 2). '</td>';
				echo '<td class="end_row_options">';
					echo '<span>'; //style="display:flex;"
						echo '<a href="../includes/finances.inc.php?selected_id='.$row['fi_id'].'&update_type=Edit&form_type=Income&user_id='.$user_id.'"><i class="actions"><p class="bi-pencil-fill"></p></i></a>';
						echo '<a href="../ajax/finances.ajax.php?selected_id='.$row['fi_id'].'&update_type=Delete&form_type=Income&user_id='.$user_id.'" onclick="return confirm(\'Delete: '.$row['fi_name'].' Income?\')"><i class="actions"><p class="bi-trash-fill"></p></i></a>';
					echo '</span>';
				echo '</td>';
			echo '</tr>';
			// get variables for savings:
			$total_incomes_amount += (float)$row['fi_amount'];
		}
		echo '<tr>';
			echo '<td class="end_row_options" colspan=3 style="text-align:left;">Total:</td>';
			echo '<td class="end_row_options" style="text-align:right;">$'.number_format($total_incomes_amount, 2).'</td>';
			echo '<td class="end_row_options"></td>';
		echo '</tr>';
	echo '</table>';
} // end incomes table

function library_expenses_table($user_id, $action, $current_page_num, $date_search, $show_per_page) {
  // add or subtract the page number depending on the action
  $get_current_page_num = 1;
  if ($action == "Next") {
		$get_current_page_num = $current_page_num + 1;
	} elseif ($action == "Prev") {
		$get_current_page_num = $current_page_num - 1;
	}
  echo '<p id="Expenses_current_page_num" style="text-align:center; display:none;" value="'.$get_current_page_num.'">'.$get_current_page_num.'</p>'; //style="display:none;"
  echo '<p id="Expenses_page_show" style="text-align:center; color:grey;">(Page '.$get_current_page_num.')</p>';

  $get_sql_limit_min = ($show_per_page * $get_current_page_num) - $show_per_page;
  $get_sql_limit_max = ($show_per_page * $get_current_page_num);

  $sql = "
      SELECT fe.fe_id,
          fe.fe_company,
          fe.id_category,
          cat.cat_name,
          fe.fe_name,
          fe.fe_amount,
          fe.fe_date
      FROM finance_expenses fe
      LEFT JOIN categories cat ON fe.id_category = cat.cat_id
      LEFT JOIN users u ON fe.id_user = u.user_id
      WHERE fe.is_active = 1
      AND u.user_id = ".$user_id."
      AND MONTH(fe.fe_date)=MONTH('".$date_search."')
      AND YEAR(fe.fe_date)=YEAR('".$date_search."')

      ORDER BY fe.fe_date DESC
      LIMIT ".$get_sql_limit_min.",".$show_per_page .";
  ";
  $dbh = new Dbh();
  $stmt = $dbh->connect()->query($sql);
  echo '<table class="table table-dark" style="background-color:#3a5774; text-align:center;">';
    echo '<tr>';
      echo '<th>Company</th>';
      //echo '<th>Category</th>';
      echo '<th>Name</th>';
      echo '<th>Date</th>';
      echo '<th style="text-align:right;">Amount</th>';
      echo '<th class="end_row_options">';
        echo '<a href="../includes/finances.inc.php?form_type=Expense&user_id='.$user_id.'"><i class="actions"><p class="bi-plus-circle"></p></i></a>';
      echo '</th>';
    echo '</tr>';
    $total_expenses_amount = 0;
    $total_not_shown_expenses = 0;
    //$show_limit = 5;                      // this limit variable is helpful for make next and previous eventually...
    $counter = 1;
    $is_alternate_row = false;
    $add_alternating_class = '';
    while ($row = $stmt->fetch()) {

      //if ($counter <= $show_limit){
          echo '<tr>';

          if ($is_alternate_row == false) {
            $add_alternating_class = '';
            $is_alternate_row = true;
          } else {
            $add_alternating_class = 'class="alternating_row"';
            $is_alternate_row = false;
          }
          echo '<td '.$add_alternating_class.' style="color:grey;">' .$row['fe_company']. '</td>';
          //echo '<td style="background:rgb(25, 29, 32); color:grey;">' .$row['cat_name']. '</td>';
          echo '<td '.$add_alternating_class.'>' .$row['fe_name']. '</td>';
          $date_string = strtotime($row['fe_date']);
          echo '<td '.$add_alternating_class.' style="color:grey;">' .date('M, d', $date_string). '</td>';
          echo '<td '.$add_alternating_class.' style="text-align:right;">' .number_format((float)$row['fe_amount'], 2). '</td>';
          echo '<td class="end_row_options">';
            echo '<span>'; //style="display:flex;"
              echo '<a href="../includes/finances.inc.php?selected_id='.$row['fe_id'].'&update_type=Edit&form_type=Expense&user_id='.$user_id.'"><i class="actions"><p class="bi-pencil-fill"></p></i></a>';
              echo '<a href="../ajax/finances.ajax.php?selected_id='.$row['fe_id'].'&update_type=Delete&form_type=Expense&user_id='.$user_id.'"><i class="actions"><p class="bi-trash-fill"></p></i></a>';
            echo '</span>';
          echo '</td>';
        echo '</tr>';
      // get variables for savings:
      $total_expenses_amount += (float)$row['fe_amount'];
      // always add to the total amount for all the rows
      $total_not_shown_expenses += (float)$row['fe_amount'];
      $counter++;
    }
    echo '<tr>';
      echo '<td colspan=4 class="end_row_options" style="text-align:left;">Total: <p style="float:right;">$'.number_format($total_expenses_amount, 2).'</p></td>';
      //echo '<td style="text-align:right; background-color:rgb(33, 37, 46);">$'.number_format($total_expenses_amount, 2).'</td>';
      echo '<td class="end_row_options"></td>';
    echo '</tr>';
    echo '<tr>';
    echo '</tr>';
  echo '</table>';

} // end expenses table

function library_category_spending_table($user_id, $date_search) {
	$sql = "
					SELECT cat.cat_name,
					SUM(fe.fe_amount) AS 'fe_amount',
					IF (bud.bud_amount IS NULL, NULL, bud.bud_amount)  AS 'bud_amount',
					fe.is_active
					FROM finance_expenses fe

					LEFT JOIN users u ON fe.id_user = u.user_id
					LEFT JOIN categories cat ON fe.id_category = cat.cat_id
					LEFT JOIN budgets bud ON fe.id_category = bud.id_category

					WHERE fe.is_active = 1
					AND u.user_id = ".$user_id."
					AND (bud.id_user = '".$user_id."' OR bud.id_user IS NULL)
					AND YEAR(fe.fe_date)=YEAR('".$date_search."')
					AND MONTH(fe.fe_date)=MONTH('".$date_search."')

					GROUP BY cat.cat_name
					ORDER BY cat.cat_name ASC;
	";
	//echo $sql .'<br>';
	$dbh = new Dbh();
	$stmt = $dbh->connect()->query($sql);

	$build_table = '';
	$total_spent_amount = 0;
	$total_budget_amount = 0;
	$total_over_under_amount = 0;
	$is_alternate_row = false;
	$add_alternating_class = '';
	while ($row = $stmt->fetch()) {
		$build_table .= '<tr>';

		if ($is_alternate_row == false) {
			$add_alternating_class = '';
			$is_alternate_row = true;
		} else {
			$add_alternating_class = 'class="alternating_row"';
			$is_alternate_row = false;
		}

		$build_table .= '<td '.$add_alternating_class.'>' .$row['cat_name']. '</td>';
		$build_table .= '<td '.$add_alternating_class.' style="text-align:right;">$' .number_format($row['fe_amount'], 2). '</td>';
		if ($row['bud_amount'] == NULL) {
				$build_table .= '<td '.$add_alternating_class.' style="text-align:right; color:grey;">~</td>';
				$build_table .= '<td '.$add_alternating_class.' style="text-align:right; color:grey;">~</td>';
		} else {
			$show_budget = "~";
			if ($row['bud_amount'] != NULL) {
					$show_budget = number_format($row['bud_amount'], 2);
			}
			$build_table .= '<td '.$add_alternating_class.' style="text-align:right;">$' .$show_budget. '</td>';
			// get the difference:
			$bud_diff = (float)($row['bud_amount'] - $row['fe_amount']);
			$color = 'red';
			if ($bud_diff >= 0) { $color = 'green'; }
			$total_over_under_amount += $bud_diff;
			$total_budget_amount += $row['bud_amount'];
			$total_spent_amount += $row['fe_amount'];
			$build_table .= '<td '.$add_alternating_class.' style="text-align:right; color:'.$color.';">$' .number_format($bud_diff, 2). '</td>';
		}
		$build_table .= '</tr>';

	}

		// check if there was anything to show:
		if ($build_table == '') {
			echo '<p class="end_row_options" style="color:grey; text-align:center;">(Nothing to show)</p>';
		} else {
			//echo '<i style="color:grey;">';
				//echo '(The "μ" symbol in parenthesis next to the column for "Spent" amounts, represents the average amount
								//spent per day in this category.)';
			//echo '</i>';

			echo '<table class="table table-dark" style="text-align:center;">';
					echo '<tr>';
						echo '<th>Category</th>';
						echo '<th style="text-align:right;">Spent</th>';// (μ)
						echo '<th style="text-align:right;">Budget</th>';
						echo '<th style="text-align:right;">Over/Under</th>';
					echo '</tr>';

					echo $build_table;

					echo '<tr>';
						echo '<td colspan=1 class="end_row_options" style="text-align:left;">Totals:</td>';
						echo '<td class="end_row_options" style="text-align:right;">$'.number_format($total_spent_amount, 2).'</td>';
						echo '<td class="end_row_options" style="text-align:right;">$'.number_format($total_budget_amount, 2).'</td>';
						$color = 'red';
						if ($total_over_under_amount >= 0) { $color = 'green'; }
						echo '<td class="end_row_options" style="text-align:right; color:'.$color.';">$'.number_format($total_over_under_amount, 2).'</td>';
						echo '<td class="end_row_options"></td>';
					echo '</tr>';

			echo '</table>';
		}
} // end category spending table

function library_yearly_table($user_id, $action, $current_year_num, $date_search, $show_per_page) {

	// add or subtract the page number depending on the action
	if ($action == "Next") {
		$current_year_num = $current_year_num + 1;
	} elseif ($action == "Prev") {
		$current_year_num = $current_year_num - 1;
	}

	$new_date_string = $current_year_num.'-01-01';
  $date_search = date('Y-m-d', strtotime($new_date_string));
	// get variables
	$months_of_year = get_short_name_months_of_year($date_search);
	$this_year = get_this_year($date_search);
	$last_day_of_year = get_last_day_this_year();
	$this_month = get_this_month();

	echo '<h3 id="Yearly_current_year_num" style="text-align:center;" value="'.$this_year.'">'.$this_year.'</h3>'; //style="display:none;"
	echo '<p id="date_search" style="display:none;" value="'.$date_search.'">'.$date_search.'</p>';

	echo '<table class="table table-dark" style="text-align:center;">'; // table where rows are incomes, expenses and savings, and columns are months
		echo '<tr>';
			echo '<th></th>';
			//foreach ($months_of_year as $month) {
				//echo '<th>'.$month.'</th>';
			//}
			echo '<th><i class="bi-plus-square"></i></th>'; // Incomes
			echo '<th><i class="bi-dash-square"></i></th>'; // Expenses
			echo '<th><i class="bi-currency-dollar"></i></th>'; // Savings // <i class="bi-piggy-bank"></i>
			echo '<th><i class="bi-currency-dollar"></i> (+Loans)</th>'; // <i class="bi-piggy-bank"></i>
		echo '</tr>';
		//echo '<tr>';

		/////////////////////////////////////////////////////////////////////////////////////////////////////
		// This is the variable that is collecting data from these queries and organizing it into a string //
		$year_data_string = "";
		/////////////////////////////////////////////////////////////////////////////////////////////////////

		//echo '<td style="background:rgb(25, 29, 32);">Incomes</td>';
		$income_monthly_totals = array();
		$sql = "
						SELECT SUM(f.fi_amount) AS 'fi_amount',
									 f.fi_date,
									 f.is_active
						FROM finance_incomes f
						LEFT JOIN users u ON f.id_user = u.user_id
						WHERE f.is_active = 1
						AND u.user_id = ".$user_id."
						AND YEAR(f.fi_date)=YEAR('".$date_search."')

						GROUP BY MONTH(f.fi_date)
						ORDER BY f.fi_date ASC;
		";
		//echo $sql .'<br>';
		$dbh = new Dbh();
		$stmt = $dbh->connect()->query($sql);

		while ($row = $stmt->fetch()) {
			$fi_date = strtotime($row['fi_date']);
			$get_formatted_date = date('M', $fi_date);

			// check if fi_amount is zero or null
			if ($row['fi_amount'] == 0 || $row['fi_amount'] == null) {
				$income_monthly_totals[$get_formatted_date] = 0;
			} else {
				$income_monthly_totals[$get_formatted_date] = $row['fi_amount'];
			}
		}

		$expense_monthly_totals = array();
		$sql = "
					SELECT bl.*,
									 cb.bill_name,
									 cb.bill_freq
						FROM bill_logs bl
						INNER JOIN current_bills cb ON bl.bl_id_bill = cb.bill_id
						INNER JOIN
								(SELECT bl_id,
												bl_id_bill,
												bl_amount,
												MAX(bl_valid_date) AS MaxDateTime
									FROM bill_logs
									WHERE DATE(bl_valid_date) <= DATE('2022-01-01')
									AND is_active = 1
									GROUP BY bl_id_bill
								) bl2
						ON bl.bl_valid_date = bl2.MaxDateTime
						LEFT JOIN users u ON bl.id_user = u.user_id
						WHERE bl.is_active = 1
						AND u.user_id = ".$user_id."

						GROUP BY bl.bl_id_bill;
		";
		$dbh = new Dbh();
		$stmt = $dbh->connect()->query($sql);

		$total_history_bills = 0;
		while ($row = $stmt->fetch()) {
			$total_history_bills += $row['bl_amount'];
		}

		$sql = "
						SELECT SUM(fe.fe_amount) AS 'fe_amount',
									 fe.fe_date,
									 fe.is_active
						FROM finance_expenses fe
						LEFT JOIN users u ON fe.id_user = u.user_id
						WHERE fe.is_active = 1
						AND u.user_id = ".$user_id."
						AND YEAR(fe.fe_date)=YEAR('".$date_search."')

						GROUP BY MONTH(fe.fe_date)
						ORDER BY fe.fe_date ASC;
		";
		//echo $sql.'<br>';
		$dbh = new Dbh();
		$stmt = $dbh->connect()->query($sql);

		while ($row = $stmt->fetch()) {
			$fe_date = strtotime($row['fe_date']);
			$get_formatted_date = date('M', $fe_date);

			$expense_monthly_totals[$get_formatted_date] = $row['fe_amount'];
		}
	//  get the total savings if all your loans are paid off
	$sql = "
					SELECT SUM(i.iou_amount_owed) AS 'amount_owed',
								 SUM(i.iou_amount_paid) AS 'amount_paid',
								 i.iou_loaner_id,
								 i.iou_is_active
					FROM ious i

					WHERE i.iou_is_active = 1
					AND i.iou_loaner_id = ".$user_id."
					AND YEAR(i.iou_owe_date)=YEAR('".$date_search."')
					AND i.iou_is_paid_off = 0;
	";
	//echo $sql.'<br>';
	$dbh = new Dbh();
	$stmt = $dbh->connect()->query($sql);

	$total_loan_amount_owed = 0;
	while ($row = $stmt->fetch()) {
		$total_loan_amount_owed = $row['amount_owed'];
	}
	// get calculation of what is remaining:
		$total_yearly_expenses = 0;
		$total_yearly_incomes = 0;
		$total_yearly_savings = 0;
		$total_yearly_loan_savings = 0;
		$is_alternate_row = false;
		$add_alternating_class = '';
		$end_year_data = false;
		foreach ($months_of_year as $month) {
			if ($end_year_data == false) {
				$year_data_string .= "['".$month."', ";
			}

			$this_total = '~';
			$color = 'grey';

			$month_savings = 0;
			$month_savings_with_loans = 0;

			if ($is_alternate_row == false) {
				$add_alternating_class = '';
				$is_alternate_row = true;
			} else {
				$add_alternating_class = 'class="alternating_row"';
				$is_alternate_row = false;
			}

			echo '<tr>';
				// month name
				echo '<td '.$add_alternating_class.' style="color:grey;">'.$month.'</td>';
				// incomes
				if (array_key_exists($month, $income_monthly_totals)) {
					$total_yearly_incomes += $income_monthly_totals[$month];

					$this_total = $income_monthly_totals[$month];
					echo '<td '.$add_alternating_class.'>$'.number_format($this_total, 2).'</td>';
					if ($end_year_data == false) {
						$year_data_string .= $this_total .", ";
					}
				} else {
					echo '<td '.$add_alternating_class.' style="color:grey;">~</td>';
					if ($end_year_data == false) {
						$year_data_string .= "0, ";
					}
				}
				// expenses
				if (array_key_exists($month, $expense_monthly_totals)) {
					$add_bills_total = $total_history_bills + $expense_monthly_totals[$month];
					$total_yearly_expenses += $add_bills_total;

					if (array_key_exists($month, $income_monthly_totals)) {
						$month_savings = ($income_monthly_totals[$month] - $add_bills_total);
					} else {
						$month_savings = (0 - $add_bills_total);
					}
					$month_savings_with_loans = ($month_savings + $total_loan_amount_owed); // regardless we add loans to savings
					$total_yearly_savings += $month_savings;
					$total_yearly_loan_savings += $month_savings_with_loans;
					// check if positive
					$save_color1 = 'red';
					if ($month_savings >= 0) {
						$save_color1 = 'green';
					}
					//$savings_total_string .= '<td style="color:'.$save_color.';">$'.number_format($month_savings, 2).'</td>';
					echo '<td '.$add_alternating_class.'>$'.number_format($add_bills_total, 2).'</td>';
					echo '<td '.$add_alternating_class.' style="color:'.$save_color1.';">$'.number_format($month_savings, 2).'</td>';

					if ($end_year_data == false) {
						$year_data_string .= $add_bills_total .", ". $month_savings ."], ";
					}
					$save_color2 = 'red';
					if ($month_savings_with_loans >= 0) {
						$save_color2 = 'green';
					}
					echo '<td '.$add_alternating_class.' style="color:'.$save_color2.';">$'.number_format($month_savings_with_loans, 2).'</td>';

				} else {
					//$savings_total_string .= '<td style="color:grey;">~</td>';
					echo '<td '.$add_alternating_class.' style="color:grey;">~</td>';
					echo '<td '.$add_alternating_class.' style="color:grey;">~</td>';
					echo '<td '.$add_alternating_class.' style="color:grey;">~</td>';
					if ($end_year_data == false) {
						$year_data_string .= "0, 0], ";
					}
				}
			echo '</tr>';
			if ($this_month == $month) {
				$end_year_data = true;  // next loop iteration will not add values to next months in graph string creation
			}
		}


		echo '<tr>';
			echo '<td class="end_row_options" style="color:grey;">(Totals)</td>';
			echo '<td class="end_row_options">$'.number_format($total_yearly_incomes, 2).'</td>';
			echo '<td class="end_row_options">$'.number_format($total_yearly_expenses, 2).'</td>';
			$save_color = 'red';
			if ($total_yearly_savings >= 0) {
				$save_color = 'green';
			}
			echo '<td class="end_row_options" style="color:'.$save_color.';">$'.number_format($total_yearly_savings, 2).'</td>';
			$save_color = 'red';
			if ($total_yearly_loan_savings >= 0) {
				$save_color = 'green';
			}
			echo '<td class="end_row_options" style="color:'.$save_color.';">$'.number_format($total_yearly_loan_savings, 2).'</td>';
		echo '</tr>';


	echo '</table>';
} // end yearly table

// for debts and loans
function library_ious_table($user_id, $action, $current_page_num, $date_search, $show_per_page, $current_user_owes = false, $paid_off = false) {
	// add or subtract the page number depending on the action
	$get_current_page_num = 1;
	if ($action == "Next") {
		$get_current_page_num = $current_page_num + 1;
	} elseif ($action == "Prev") {
		$get_current_page_num = $current_page_num - 1;
	}

  $get_sql_limit_min = ($show_per_page * $get_current_page_num) - $show_per_page;
  $get_sql_limit_max = ($show_per_page * $get_current_page_num);

  $editable = false;  // true for if it is not paid off and if created_by is this user
  $insertable = false;  // true if not paid off

  $loaner = '=';
  $debtor = '!=';
  $color = '';
  $form_type = 'Loan';
  $current_page_name = "current_loan_page";
  // check to see if this is owed by current user or not:
  if ($current_user_owes == true) {
    $loaner = '!=';
    $debtor = '=';
    $color = 'color:red;';
    $form_type = 'Debt';
    $current_page_name = "current_debt_page";
  }

  echo '<p id="'.$current_page_name.'" style="text-align:center; display:none;" value="'.$get_current_page_num.'">'.$get_current_page_num.'</p>'; //style="display:none;"
	echo '<p id="'.$form_type.'s_page_show" style="text-align:center; color:grey;">(Page '.$get_current_page_num.')</p>';

  $filter_paid_off = 0;
  // check if we want to see paid off or not:
  if ($paid_off == true) {
    $filter_paid_off = 1;
  }
  // start the sql here for owed_to_you_build_table
  $sql = "
    SELECT i.iou_id,
        i.iou_reason,
        i.iou_loaner_id,
        i.iou_debtor_id,
        i.iou_amount_owed,
        i.iou_amount_paid,
        i.iou_owe_date,
        i.iou_created_by,
        i.iou_updated_date,
        i.iou_is_paid_off,
        i.iou_paid_off_date,
        i.iou_is_active,

        loaners.user_name AS 'loaner_user_name',
        loaners.user_fname AS 'loaner_user_fname',
        loaners.user_lname AS 'loaner_user_lname',

        debtors.user_name AS 'debtor_user_name',
        debtors.user_fname AS 'debtor_user_fname',
        debtors.user_lname AS 'debtor_user_lname'

    FROM ious i
    LEFT JOIN users loaners ON i.iou_loaner_id = loaners.user_id
    LEFT JOIN users debtors ON i.iou_debtor_id = debtors.user_id

    WHERE i.iou_is_active = 1
    AND i.iou_is_paid_off = ".$filter_paid_off."
    AND i.iou_loaner_id ".$loaner." '".$user_id."'
    AND i.iou_debtor_id ".$debtor." '".$user_id."'

    ORDER BY i.iou_owe_date DESC
    LIMIT ".$get_sql_limit_min.",".$show_per_page .";
  ";
  // loaners.user_name AS 'loaner_user_name',      # this is the current user's info who is the loaner
  // debtors.user_name AS 'debtor_user_name',      # this is the debtor's info of the current user
  //echo $sql;
  $dbh = new Dbh();
  $stmt = $dbh->connect()->query($sql);
  echo '<table class="table table-dark" style="text-align:center;">';
  echo '<tr>';
    echo '<th>Reason</th>';
    if ($current_user_owes == true) {
      echo '<th>Loaner</th>';
    } else {
      echo '<th>Debtor</th>';
    }
    if ($paid_off == false) {
      echo '<th style="text-align:right;">Owed</th>'; //echo '<th>Amount Owed</th>';
    }
    echo '<th style="text-align:right;">Paid</th>'; // echo '<th>Amount Paid</th>';
    if ($paid_off == false) {
      echo '<th style="text-align:right;">Remaining</th>'; // echo '<th>Amount Left</th>';
    }
    echo '<th>Owe Date</th>';
    if ($paid_off == true) {
      echo '<th>Paid Off Date</th>';  // this is only visible in the paid off tables
    }
    echo '<th class="end_row_options">';
    if ($paid_off == false) {
      echo '<a href="../includes/ious.inc.php?form_type='.$form_type.'&user_id='.$user_id.'"><i class="actions"><p class="bi-plus-circle"></p></i></a>';
    }
    echo '</th>';
  echo '</tr>';
    $total_owed_amount = 0;
    $total_paid_amount = 0;
    $total_left_amount = 0;
    $is_alternate_row = false;
    $add_alternating_class = '';
    while ($row = $stmt->fetch()) {
      echo '<tr>';

      if ($is_alternate_row == false) {
        $add_alternating_class = '';
        $is_alternate_row = true;
      } else {
        $add_alternating_class = 'class="alternating_row"';
        $is_alternate_row = false;
      }

        echo '<td '.$add_alternating_class.' style="color:grey;">' .$row['iou_reason']. '</td>';
        if ($current_user_owes == true) {
          echo '<td '.$add_alternating_class.'>' .$row['loaner_user_name']. '</td>';
        } else {
          echo '<td '.$add_alternating_class.'>' .$row['debtor_user_name']. '</td>';
        }
        if ($paid_off == false) {
          echo '<td '.$add_alternating_class.' style="'.$color.' text-align:right; ">' .number_format((float)$row['iou_amount_owed'], 2). '</td>';
        }
        echo '<td '.$add_alternating_class.' style="color:green; text-align:right;">' .number_format((float)$row['iou_amount_paid'], 2). '</td>';
        if ($paid_off == false) {
          $amount_left = ($row['iou_amount_owed'] - $row['iou_amount_paid']);
          echo '<td '.$add_alternating_class.' style="'.$color.' text-align:right;">' .number_format($amount_left, 2). '</td>';
        }
        $date_string1 = strtotime($row['iou_owe_date']);
        echo '<td '.$add_alternating_class.' style="color:grey;">' .date('M d, Y', $date_string1). '</td>';
        if ($row['iou_is_paid_off'] == 1) {
          $date_string2 = strtotime($row['iou_paid_off_date']); // only visible when paid off is equal to true or 1
          echo '<td '.$add_alternating_class.' style="color:grey;">' .date('M d, Y', $date_string2). '</td>';
        }

        // below options/actions are only visible when the created by is the current user
        echo '<td class="end_row_options">';
          if ($row['iou_created_by'] == $user_id && $row['iou_is_paid_off'] == 0) {
            echo '<span>'; //style="display:flex;"
              echo '<a href="../includes/ious.inc.php?selected_id='.$row['iou_id'].'&update_type=Update&form_type='.$form_type.'&user_id='.$user_id.'"><i class="actions"><p class="bi-pencil-fill"></p></i></a>';
              echo '<a href="../ajax/ious.ajax.php?selected_id='.$row['iou_id'].'&update_type=Delete&form_type='.$form_type.'&user_id='.$user_id.'"><i class="actions"><p class="bi-trash-fill"></p></i></a>';
            echo '</span>';
          }
        echo '</td>';
      echo '</tr>';
      // get variables for owed and paid:
      $total_owed_amount += (float)$row['iou_amount_owed'];
      $total_paid_amount += (float)$row['iou_amount_paid'];
      if ($paid_off == false) {
        $total_left_amount += $amount_left;
      }
    }
    echo '<tr>';
      echo '<td colspan=2 class="end_row_options" style="text-align:left;">Totals:</td>';
      if ($paid_off == false) {
        echo '<td class="end_row_options" style="text-align:right;">$'.number_format($total_owed_amount, 2).'</td>';
      }
      echo '<td class="end_row_options" style="text-align:right;">$'.number_format($total_paid_amount, 2).'</td>';
      if ($paid_off == false) {
        echo '<td class="end_row_options" style="text-align:right;">$'.number_format($total_left_amount, 2).'</td>';
      }
      echo '<td class="end_row_options" colspan=3></td>';
    echo '</tr>';
  echo '</table>';
} // end ious table

function library_notifications_table($user_id, $action, $current_page_num, $date_search, $show_per_page, $conn) {
	// add or subtract the page number depending on the action
	$get_current_page_num = 1;
  if ($action == "Next") {
		$get_current_page_num = $current_page_num + 1;
	} elseif ($action == "Prev") {
		$get_current_page_num = $current_page_num - 1;
	}
	echo '<p id="Notifications_current_page_num" style="text-align:center; display:none;" value="'.$get_current_page_num.'">'.$get_current_page_num.'</p>'; //style="display:none;"
	echo '<p id="Notifications_page_show" style="text-align:center; color:grey;">(Page '.$get_current_page_num.')</p>';

	$get_sql_limit_min = ($show_per_page * $get_current_page_num) - $show_per_page;
	$get_sql_limit_max = ($show_per_page * $get_current_page_num);

			$sql = "
							SELECT
								n.n_id,
								n.n_subject,
								n.n_message,
								n.n_type,
								n.n_send_date,
								n.n_read_date,
								n.is_active,
								n.n_to_user,

								ur.role_name AS 'from_role_name',
								ur.role_color AS 'from_role_color',

								fu.id_role AS 'from_role',
								fu.user_name AS 'from_username',
								fu.user_icon AS 'from_icon',
								fu.user_fname AS 'from_fname',
								fu.user_lname AS 'from_lname'

							FROM notifications n
							LEFT JOIN users fu ON n.n_from_user = fu.user_id
							LEFT JOIN user_roles ur ON fu.id_role = ur.role_id

							WHERE n.is_active = 1
							AND n.n_to_user = ".$user_id." OR n.n_to_user = 0

							ORDER BY n.n_send_date DESC
							LIMIT ".$get_sql_limit_min.",".$show_per_page .";
			";
			//echo $sql;
			$dbh = new Dbh();
			$stmt = $dbh->connect()->query($sql);
			// get num rows to check
			$num_stmt = $conn->prepare($sql);
			$num_stmt->execute();
			/* store the result in an internal buffer */
			$num_stmt->store_result();
			if ($num_stmt->num_rows > 0) {
				echo '<table class="table table-dark" style="width:100%;">'; // mini table to display months
					echo '<tr>';
						echo '<th>Type</th>';
						echo '<th>From</th>';
						echo '<th>Subject</th>';
						echo '<th>Sent</th>';
						echo '<th class="end_row_options">';
							//echo '<a href="../ajax/messages.ajax.php?form_type=Income&user_id='.$user_id.'"><p class="bi-plus-circle" style="color:white;"></p></a>';
						echo '</th>';
					echo '</tr>';
			} else {
				echo '<p style="text-align:center;">(There are no notifications)</p>';
			}

			$is_alternate_row = false;
			$add_alternating_class = '';
			while ($row = $stmt->fetch()) {
				echo '<tr>';

				if ($is_alternate_row == false) {
					$add_alternating_class = '';
					$is_alternate_row = true;
				} else {
					$add_alternating_class = 'class="alternating_row"';
					$is_alternate_row = false;
				}
					//echo '<td style="display:none;"><p id="msg_id" name="msg_id" value="'.$row['msg_id'].'">'.$row['msg_id'].'</p></td>';
					//echo '<td>'.$row['from_fname'].' '.$row['from_lname'].'</td>';
					$font_weight = 'font-weight:normal;';
					if ($row['n_read_date'] < date('2020-01-01 00:00:00')) {
						$font_weight = 'font-weight:bold;';
					}

					echo '<td '.$add_alternating_class.' style="'.$font_weight.'">'.$row['n_type'].'</td>';
					echo '<td '.$add_alternating_class.' style="'.$font_weight.'">';
						echo '<i style="color:'.$row['from_role_color'].'; style="'.$font_weight.'"">'.$row['from_username'].' ('.$row['from_role_name'].')</i>';
					echo '</td>';
					echo '<td '.$add_alternating_class.' style="'.$font_weight.'">'.$row['n_subject'].'</td>';
					$date_string = strtotime($row['n_send_date']);
					echo '<td '.$add_alternating_class.' style="color:grey;">' .date('M, d', $date_string). '</td>';
					echo '<td class="end_row_options">';
						//echo '<a href="../includes/messages.ajax.php?user_id='.$user_id.'"><p class="bi-eye-fill" style="color:white;"></p></a>';
						echo '<button class="end_row_options" style="text-align:center; margin:auto; color:white; background-color:black; border:none;" name="view" onclick="view_msg('.$row['n_id'].');" value="View"><i class="actions"><p class="bi-eye-fill"></p></i></button>';
					echo '</td>';
				echo '</tr>';
			}

	echo '</table>';
	echo '<br>';
} // end notifications table

// additional functions //

function get_short_name_months_of_year($date_search) {
	$this_year = get_this_year($date_search);
	$months_of_year = array();
	for ($i = 0; $i < 12; $i++) {
			 $next_month = strtotime("+".$i." month", strtotime(get_first_day_this_year($this_year)));
			 $show_month = date('M', $next_month);
			 array_push($months_of_year, $show_month);
	}
	return $months_of_year;
}

function get_full_name_months_of_year($date_search) {
	$this_year = get_this_year($date_search);
	$full_name_months = array();
	for ($i = 0; $i < 12; $i++) {
			 $next_month = strtotime("+".$i." month", strtotime(get_first_day_this_year($this_year)));
			 $show_full = date('F', $next_month);
			 array_push($full_name_months, $show_full);
	}
	return $full_name_months;
}

function get_this_year($date_search) {
	return date('Y', strtotime($date_search));
}

function get_first_day_this_year($this_year) {
	return date('Y-m-d', strtotime('first day of January'.$this_year));
}

function get_last_day_this_year() {
	return date('Y-m-d', strtotime('12/31'));
}

function get_this_month() {
	return date("M");
}

// function for populating finance page essentially //

function library_monthly_tables($action, $date_search_time, $user_id) {
	// hidden inputs //
	// get date how I want it normally
	$get_date_normal = date('Y-m-d', $date_search_time);
	// default show current month and year dates
	$show_month = date('F', $date_search_time);
	$show_year = date('y', $date_search_time);
	$next_month = strtotime("+1 month", $date_search_time);
	$prev_month = strtotime("-1 month", $date_search_time);
	$current_date_set = $date_search_time;

	if ($action == "Next") {
		$current_date_set = $next_month;
		$show_month = date('F', $next_month);
		$show_year = date('y', $next_month);
		$get_date_normal = date('Y-m-d', $next_month);
	} elseif ($action == "Prev") {
		$current_date_set = $prev_month;
		$show_month = date('F', $prev_month);
		$show_year = date('y', $prev_month);
		$get_date_normal = date('Y-m-d', $prev_month);
	}
	// set date search current:
	echo '<p id="date_search" style="display:none;" value="'.$get_date_normal.'">'.$get_date_normal.'</p>';
  echo '<p id="user_id" style="display:none;" value="'.$user_id.'">'.$user_id.'</p>';

	// main content //
	echo '<span>';
		echo '<h2 style="text-align:center;">';
			echo '<button class="prev_button" onclick="scroll_month(0, '.$current_date_set.', \'Monthly\');" style="float:left; background:none; border:none; font-size:20px; height:32px;">';
				echo '<i class="monthly_action"><p class="bi-arrow-left-square"></p></i>';
			echo '</button>';

			echo '<i class="bi-calendar"> </i>'.$show_month.' <span style="color: grey;">(\''. $show_year.')</span>';

			echo '<button class="next_button" onclick="scroll_month(1, '.$current_date_set.', \'Monthly\');" style="float:right; background:none; border:none; font-size:20px; height:32px;">';
				echo '<i class="monthly_action"><p class="bi-arrow-right-square"></p></i>';
			echo '</button>';
		echo '</h2>';
	echo '</span>';

	// mini form for displaying different dates in history
	echo '<form method="post" action="../pages/finances.php" style="text-align:center;">';
		//echo '<select>';
		//foreach ($months_of_year as $month) {
		//  echo '<option></option>';
		//}
		//echo '</select>';
		//echo $date_search;
		//$date = date('Y-m-d');	// default to today
		//echo '<input type="date" name="date_search" value="'.$date_search.'"></input>';

		//echo '<button type="submit" name="submit_search" class="btn btn-primary btn-sm" value="Display">Display Date</button>';
	echo '</form>';

	echo '<br>';

	echo '<div class="div_element_block">'; // div for incomes
		echo '<h4 style="text-align:center;"><i class="bi-plus-square"> </i>Incomes</h4>';
		echo '<p style="width:95%; margin:0px; text-align:center;">';
			echo '<button name="prev_button" onclick="scroll_table(0, \'Incomes\');" style="float:left; background:none; border:none; font-size:20px; height:32px;">';
				echo '<i class="actions"><p class="bi-arrow-left-square"></p></i>';
			echo '</button>';
			echo '<button name="next_button" onclick="scroll_table(1, \'Incomes\');" style="float:right; background:none; border:none; font-size:20px; height:32px;">';
				echo '<i class="actions"><p class="bi-arrow-right-square"></p></i>';
			echo '</button>';
		echo '</p>';

		echo '<div id="Incomes_scroll_div">';
				library_incomes_table($user_id, "First", 1, $get_date_normal, 5);
		echo '</div>';
	echo '</div>';

	echo '<br>';

	echo '<div class="div_element_block">';// div for expenses
		echo '<h4 style="text-align:center;"><i class="bi-dash-square"> </i>Expenses</h4>';
		echo '<p style="width:95%; margin:0px; text-align:center;">';
			echo '<button name="prev_button" onclick="scroll_table(0, \'Expenses\');" style="float:left; background:none; border:none; font-size:20px; height:32px;">';
				echo '<i class="actions"><p class="bi-arrow-left-square"></p></i>';
			echo '</button>';
			echo '<button name="next_button" onclick="scroll_table(1, \'Expenses\');" style="float:right; background:none; border:none; font-size:20px; height:32px;">';
				echo '<i class="actions"><p class="bi-arrow-right-square"></p></i>';
			echo '</button>';
		echo '</p>';

		echo '<div id="Expenses_scroll_div">';
				library_expenses_table($user_id, "First", 1, $get_date_normal, 5);
		echo '</div>';
	echo '</div>';

	echo '<br>';

	echo '<div class="div_element_block">'; // div for bills
		//echo '<h4 style="text-align:center;"><i class="bi-receipt-cutoff"> </i>Bills</h4>';
		echo '<h4 style="text-align:center;"><i class="bi-cash-stack"> </i>Bills</h4>';
		$sql = "
						SELECT bl.*,
									 cb.bill_id,
									 cb.bill_name,
									 cb.bill_freq
						FROM bill_logs bl

						INNER JOIN current_bills cb ON bl.bl_id_bill = cb.bill_id

						INNER JOIN
								(SELECT bl_id,
												bl_id_bill,
												bl_amount,
												MAX(bl_valid_date) AS MaxDateTime
									FROM bill_logs
									WHERE is_active = 1
									GROUP BY bl_id_bill
								) bl2
						ON bl.bl_valid_date = bl2.MaxDateTime

						LEFT JOIN users u ON bl.id_user = u.user_id

						WHERE cb.bill_freq = 'M'
						AND cb.is_active = 1
						AND u.user_id = ".$user_id."

						GROUP BY bl.bl_id_bill;
		";
		$dbh = new Dbh();
		$stmt = $dbh->connect()->query($sql);
		echo '<table class="table table-dark" style="text-align:center;">';
			echo '<tr>';
				echo '<th>Name</th>';
				echo '<th style="text-align:right;">Amount</th>';
				echo '<th>Frequency</th>';
				echo '<th class="end_row_options">';
					echo '<a href="../includes/finances.inc.php?form_type=Bill&user_id='.$user_id.'"><i class="actions"><p class="bi-plus-circle"></p></i></a>';
				echo '</th>';
			echo '</tr>';
			$total_bills_amount = 0;
			$is_alternate_row = false;
			$add_alternating_class = '';
			while ($row = $stmt->fetch()) {
					echo '<tr>';

					if ($is_alternate_row == false) {
						$add_alternating_class = '';
						$is_alternate_row = true;
					} else {
						$add_alternating_class = 'class="alternating_row"';
						$is_alternate_row = false;
					}
					echo '<td '.$add_alternating_class.'>' .$row['bill_name']. '</td>';
					echo '<td '.$add_alternating_class.' style="text-align:right;">' .number_format((float)$row['bl_amount'], 2). '</td>';
					echo '<td '.$add_alternating_class.' style="color:grey;">' .$row['bill_freq']. '</td>';
					echo '<td class="end_row_options">';
						echo '<span>'; //style="display:flex;"
							echo '<a href="../includes/finances.inc.php?selected_id='.$row['bill_id'].'&update_type=Edit&form_type=Bill&user_id='.$user_id.'"><i class="actions"><p class="bi-pencil-fill"></p></i></a>';
							echo '<a href="../ajax/finances.ajax.php?selected_id='.$row['bill_id'].'&update_type=Delete&form_type=Bill&user_id='.$user_id.'" onclick="return confirm(\'Delete: '.$row['bill_name'].' Bill?\')"><i class="actions"><p class="bi-trash-fill"></p></i></a>';
						echo '</span>';
					echo '</td>';
				echo '</tr>';
				// get variables for savings:
				$total_bills_amount += (float)$row['bl_amount'];
				//echo "total_bills_amount: ".$total_bills_amount."<br>";
			}
			echo '<tr>';
				echo '<td class="end_row_options" style="text-align:left;">Total:</td>';
				echo '<td class="end_row_options" style="text-align:right;">$'.number_format($total_bills_amount, 2).'</td>';
				echo '<td colspan=2 class="end_row_options"></td>';
			echo '</tr>';
		echo '</table>';
	echo '</div>';

	echo '<br>';

	echo '<div class="div_element_block">';// div for category spending
		echo '<h4 style="text-align:center;"><i class="bi-cup-hot"> </i>Category Spending</h4>';
		library_category_spending_table($user_id, $get_date_normal);
	echo '</div>';

	echo '<br>';

	// detail category spending
	echo '<div class="div_element_block">'; // div for incomes
		echo '<h4 style="text-align:center;"><i class="bi-card-list"> </i>Detailed Category View</h4>';

		echo '<div id="cat_select_buttons" style="text-align:center;">';
			$first_cat_id = library_get_current_spending_category_buttons($user_id, $get_date_normal);
		echo '</div>';

		echo '<p style="width:95%; margin:0px; text-align:center;">';
			echo '<button name="prev_button" onclick="scroll_table(0, \'DetailedCat\');" style="float:left; background:none; border:none; font-size:20px; height:32px;">';
				echo '<i class="actions"><p class="bi-arrow-left-square"></p></i>';
			echo '</button>';
			echo '<button name="next_button" onclick="scroll_table(1, \'DetailedCat\');" style="float:right; background:none; border:none; font-size:20px; height:32px;">';
				echo '<i class="actions"><p class="bi-arrow-right-square"></p></i>';
			echo '</button>';
		echo '</p>';

		echo '<div id="DetailedCat_scroll_div">';
				// need to get the cat id from the first cat button shown to click on since we are basing the visibility of this table on the categories that are spending categories
				library_detailed_category_spending_table($user_id, $get_date_normal, $first_cat_id, "First", 1, 5);
		echo '</div>';
	echo '</div>';


	echo '<br>';

} // end monthly tables

function library_detailed_category_spending_table($user_id, $date_search, $cat_id, $action, $current_page_num, $show_per_page) {
		// add or subtract the page number depending on the action
	  $get_current_page_num = 1;
	  if ($action == "Next") {
			$get_current_page_num = $current_page_num + 1;
		} elseif ($action == "Prev") {
			$get_current_page_num = $current_page_num - 1;
		}
	  echo '<p id="DetailedCat_current_page_num" style="text-align:center; display:none;" value="'.$get_current_page_num.'">'.$get_current_page_num.'</p>'; //style="display:none;"
	  echo '<p id="DetailedCat_page_show" style="text-align:center; color:grey;">(Page '.$get_current_page_num.')</p>';
		echo '<p id="DetailedCat_current_cat_id" style="text-align:center; display:none;" value="'.$cat_id.'">'.$cat_id.'</p>';

	  $get_sql_limit_min = ($show_per_page * $get_current_page_num) - $show_per_page;

		$sql = "
				SELECT cat.*,
								fe.*
				FROM finance_expenses fe

				LEFT JOIN users u ON fe.id_user = u.user_id
				LEFT JOIN categories cat ON fe.id_category = cat.cat_id

				WHERE fe.is_active = 1
				AND u.user_id = '".$user_id."'
				AND cat.is_active = 1

				AND YEAR(fe.fe_date)=YEAR('".$date_search."')
				AND MONTH(fe.fe_date)=MONTH('".$date_search."')

				AND cat.cat_id = '".$cat_id."'

				ORDER BY fe.fe_date DESC
				LIMIT ".$get_sql_limit_min.",".$show_per_page .";
		";
	  $dbh = new Dbh();
	  $stmt = $dbh->connect()->query($sql);

		$build_header_for_table = '';
		$build_main_content_for_table = '';

	 	$build_header_for_table .= '<table class="table table-dark" style="background-color:#3a5774; text-align:center;">';
	    $build_header_for_table .= '<tr>';
				$build_header_for_table .= '<th>Category</th>';
	      $build_header_for_table .= '<th>Company</th>';
	      $build_header_for_table .= '<th>Name</th>';
	      $build_header_for_table .= '<th>Date</th>';
	      $build_header_for_table .= '<th style="text-align:right;">Amount</th>';
	      //$build_header_for_table .= '<th class="end_row_options">';
	      //$build_header_for_table .= '<a href="../includes/finances.inc.php?form_type=Expense&user_id='.$user_id.'"><i class="actions"><p class="bi-plus-circle"></p></i></a>';
	      //$build_header_for_table .= '</th>';
	    $build_header_for_table .= '</tr>';

	    $total_expenses_amount = 0;
	    $total_not_shown_expenses = 0;
	    //$show_limit = 5;                      // this limit variable is helpful for make next and previous eventually...
	    $counter = 1;
	    $is_alternate_row = false;
	    $add_alternating_class = '';
	    while ($row = $stmt->fetch()) {
          $build_main_content_for_table .= '<tr>';
          if ($is_alternate_row == false) {
            $add_alternating_class = '';
            $is_alternate_row = true;
          } else {
            $add_alternating_class = 'class="alternating_row"';
            $is_alternate_row = false;
          }
					$build_main_content_for_table .= '<td '.$add_alternating_class.' style="color:grey;">' .$row['cat_name']. '</td>';
          $build_main_content_for_table .= '<td '.$add_alternating_class.' style="color:grey;">' .$row['fe_company']. '</td>';
          $build_main_content_for_table .= '<td '.$add_alternating_class.'>' .$row['fe_name']. '</td>';
          $date_string = strtotime($row['fe_date']);
          $build_main_content_for_table .= '<td '.$add_alternating_class.' style="color:grey;">' .date('M, d', $date_string). '</td>';
          $build_main_content_for_table .= '<td '.$add_alternating_class.' style="text-align:right;">' .number_format((float)$row['fe_amount'], 2). '</td>';
          //echo '<td class="end_row_options">';
            //echo '<span>'; //style="display:flex;"
              //echo '<a href="../includes/finances.inc.php?selected_id='.$row['fe_id'].'&update_type=Edit&form_type=Expense&user_id='.$user_id.'"><i class="actions"><p class="bi-pencil-fill"></p></i></a>';
              //echo '<a href="../ajax/finances.ajax.php?selected_id='.$row['fe_id'].'&update_type=Delete&form_type=Expense&user_id='.$user_id.'"><i class="actions"><p class="bi-trash-fill"></p></i></a>';
            //echo '</span>';
          //echo '</td>';
        $build_main_content_for_table .= '</tr>';
	      // get variables for savings:
	      $total_expenses_amount += (float)$row['fe_amount'];
	      // always add to the total amount for all the rows
	      $total_not_shown_expenses += (float)$row['fe_amount'];
	      $counter++;
	    }

			if ($build_main_content_for_table == '') {
				echo '<p class="end_row_options" style="color:grey; text-align:center;">(Nothing to show)</p>';
			} else {
				echo $build_header_for_table;
				echo $build_main_content_for_table;
				// the rest:
			    echo '<tr>';
			      echo '<td colspan=5 class="end_row_options" style="text-align:left;">Total: <p style="float:right;">$'.number_format($total_expenses_amount, 2).'</p></td>';
			      //echo '<td style="text-align:right; background-color:rgb(33, 37, 46);">$'.number_format($total_expenses_amount, 2).'</td>';
			      echo '<td class="end_row_options"></td>';
			    echo '</tr>';
			    echo '<tr>';
			    echo '</tr>';
			  echo '</table>';
			}
}// end detailed category spending table

// this method returns all of the category names that have expenses associated with them.
function library_get_current_spending_category_buttons($user_id, $date_search) {
	$sql = "
		SELECT cat.cat_id,
			cat.cat_name
		FROM finance_expenses fe

		LEFT JOIN users u ON fe.id_user = u.user_id
		LEFT JOIN categories cat ON fe.id_category = cat.cat_id

		WHERE fe.is_active = 1
		AND u.user_id = '".$user_id."'
		AND cat.is_active = 1

		AND YEAR(fe.fe_date)=YEAR('".$date_search."')
		AND MONTH(fe.fe_date)=MONTH('".$date_search."')

		GROUP BY cat.cat_name
		ORDER BY cat.cat_name ASC;
	";
	$dbh = new Dbh();
	$stmt = $dbh->connect()->query($sql);

  $first_cat_id_in_list = 0;
	$default_style = 'primary';	// default
	$current_style = $default_style;

	while ($row = $stmt->fetch()) {
			if ($first_cat_id_in_list == 0) {
				$first_cat_id_in_list = $row['cat_id'];
				$current_style = 'dark';
			} else {
				$current_style = $default_style;
			}

			echo '<button id="cat_button_'.$row['cat_id'].'" class="btn btn-'.$current_style.' btn-sm" onclick="select_cat('.$row['cat_id'].', \'DetailedCat\');" style="margin:5px;">';
			echo $row['cat_name'];
			echo '</button>';
	}

	return $first_cat_id_in_list;
} // end get current spending category names
?>
