<?php

date_default_timezone_set('America/New_York');

// "global variables"
$g_servername = "localhost";
//$g_servername = "lifemanagement.me";
$g_username = "root";
$g_password = "";
$g_database = "life_management";
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
	$sql = "SELECT *
					FROM companies
					WHERE is_active = 1
					ORDER BY comp_name ASC;
	";
	$dbh = new Dbh();
	$stmt = $dbh->connect()->query($sql);
	echo '<select id="company" name="company">';
		while ($row = $stmt->fetch()) {
			$color = 'white';
			if ($comp_name == $row['comp_name']) {
				if ($row['comp_name'] == 'Other') {
					$color = 'red';
				}
				echo '<option value="'.$row['comp_name'].'" selected="selected" style="color:'.$color.';">'.$row['comp_name'].'</option>';
			} else {
				echo '<option value="'.$row['comp_name'].'" style="color:'.$color.';">'.$row['comp_name'].'</option>';
			}

		}
	echo '</select>';
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



?>
