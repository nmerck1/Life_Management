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
	/*
	echo '<label>Company: </label>';
	$sql = "SELECT *
					FROM companies
					WHERE is_active = 1
					ORDER BY comp_name ASC;
	";
	$dbh = new Dbh();
	$stmt = $dbh->connect()->query($sql);
	*/
	//echo '<select id="company" name="company" onchange="show_new_input(this);">';
	echo '<label>Company: </label>';
	echo '<input id="company" name="company" oninput="update_input_search(this.value);" value="'.$comp_name.'"></input>';
	echo '<div id="search_options_popup" name="search_options_popup" class="searchPopup">';
			// content gets dynamically made into here //
			//echo '<table style="width:100%;">';
				//echo '<tr style="width:100%;"><td style="width:100%;"><button style="width:100%;"> test </button></td></tr>';
			//echo '</table>';
	echo '</div>';
	/*
		while ($row = $stmt->fetch()) {
			$color = '';
			if ($comp_name == $row['comp_name']) {
				if ($row['comp_name'] == 'Other') {
					$color = 'style="color:red;"';
				}
				echo '<option value="'.$row['comp_name'].'" selected="selected" '.$color.'>'.$row['comp_name'].'</option>';
			} else {
				echo '<option value="'.$row['comp_name'].'" '.$color.'>'.$row['comp_name'].'</option>';
			}

		}
		*/
	//echo '</select>';
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

?>
