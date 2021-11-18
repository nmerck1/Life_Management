<?php

date_default_timezone_set('America/New_York');

// "global variables"
$g_servername = "localhost";
$g_username = "root";
$g_password = "";
$g_database = "life_management";
// Create connection
$conn = new mysqli($g_servername, $g_username, $g_password, $g_database);


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

function library_get_companies_dropdown($comp_id){
	echo '<label>Company: </label>';
	$sql = "SELECT *
					FROM companies
					WHERE is_active = 1
					ORDER BY comp_name ASC;
	";
	$dbh = new Dbh();
	$stmt = $dbh->connect()->query($sql);
	echo '<select id="company">';
		while ($row = $stmt->fetch()) {
			if ($comp_id == $row['cat_id']) {
				echo '<option value="'.$row['comp_id'].'" selected="selected">'.$row['comp_name'].'</option>';
			} else {
				echo '<option value="'.$row['comp_id'].'">'.$row['comp_name'].'</option>';
			}

		}
	echo '</select>';
}


?>
