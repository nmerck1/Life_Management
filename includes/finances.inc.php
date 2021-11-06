<?php
declare(strict_types=1);
include '../includes/autoloader.inc.php';
include '../includes/function_library.inc.php';

$show_error = false;

$selected_id = '';
if (isset($_GET['selected_id'])){
	$selected_id = $_GET['selected_id'];
}

$form_type = '';		// this is either Expense, Income, Bill, Passive, Budget, etc.
if (isset($_GET['form_type'])){
	$form_type = $_GET['form_type'];
} else {
	$show_error = true;
}

$update_type = '';	// this is either Update or Insert
if (isset($_GET['update_type'])){
	$update_type = $_GET['update_type'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Life Management</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.6.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../css/style.css">

</head>
<body>

<?php
	//use Style\Navbar;
	$navbar = new Navbar();
	$navbar->show_header_nav();
?>

<script type="text/javascript">
	function send_expense_to_ajax(){
		// setup the ajax request
		var xhttp = new XMLHttpRequest();
		// get variables from inputs below:
		var selected_id = document.getElementById('selected_id');
		var update_type = document.getElementById('update_type');
		var form_type = document.getElementById('form_type');

		var company = document.getElementById('company');
		var name = document.getElementById('name');
		var category = document.getElementById('category');
		var category_value = category.options[category.selectedIndex].value;
		var amount = document.getElementById("amount");
		var date = document.getElementById('date');
		var notes = document.getElementById('notes');

		// create link to send GET variables through
		var query_string = "../ajax/finances.ajax.php";
		query_string += "?selected_id=" + selected_id.innerHTML;
		query_string += "&update_type=" + update_type.innerHTML;
		query_string += "&form_type=" + form_type.innerHTML;

		query_string += "&company=" + company.value;
		query_string += "&name=" + name.value;
		query_string += "&category=" + category_value;
		query_string += "&amount=" + amount.value;
		query_string += "&date=" + date.value;
		query_string += "&notes=" + notes.value;

		//alert(query_string);

		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
			 document.getElementById("test").innerHTML = this.responseText;
			}
		};
		xhttp.open("GET", query_string, true);
		xhttp.send();

		// when the data is returned after ajax, it redirects back to inventory
		window.location = "../pages/finances.php";
	}
</script>

<?php
	echo '<p id="selected_id" style="display:none;" value="'.$selected_id.'">'.$selected_id.'</p>';
	echo '<p id="form_type" style="display:none;" value="'.$form_type.'">'.$form_type.'</p>';

	echo '<p id="test"></p>';


?>
<div class="container text-center">
    <div>
				<?php
					if (!$show_error) {
						// check form to set
						if ($form_type == 'Expense') {
							// default variables
							$update_type = "";
							$company = "";
							$name = "";
							$cat_id = "";
							$amount = 0.00;
							$date = date('Y-m-d');	// default to today
							$notes = "";
							// check if there is an id, then we are either editing or deleting an existing record
							if ($selected_id != NULL) {
								// this is a currently existing record
								echo '<h1>Edit Expense</h1>';
								$update_type = 'Update';
								// load variables from selected_id
								// get values from selected id in table:
									$sql = "SELECT * FROM finance_expenses WHERE fe_id = '".$selected_id."' ";
									$dbh = new Dbh();
			            $stmt = $dbh->connect()->query($sql);
									//echo $sql;
									// should only populate one row of data
									while ($row = $stmt->fetch()) {
										//echo $row['fe_company'];
										$company = $row['fe_company'];
										$name = $row['fe_name'];
										$cat_id = $row['id_category'];
										// format date
										//echo $row['fe_date'];
										$get_date = date_create($row['fe_date']);
										$formatted_date = date_format($get_date, 'Y-m-d');
										//echo $formatted_date;
										$date = $formatted_date;
										$amount = $row['fe_amount'];
										$notes = $row['fe_notes'];
									}
							} else {
								// this is a new record we are creating
								echo '<h1>Add New Expense</h1>';
								$update_type = 'Insert';
							}
							echo '<p id="update_type" value="'.$update_type.'">'.$update_type.'</p>';
							// print the form type here
							echo '<div class="container">';
								//echo '<p id="selected_id" value="'.$selected_id.'">selected_id: '.$selected_id.'</p>';
								//echo '<p id="update_type" value="'.$update_type.'">update_type: '.$update_type.'</p>';

								echo '<label>Company: </label>';
								echo '<input type="text" id="company" value="'.$company.'" placeholder="Ingles, QT, Wal-Mart, etc."></input>';
								echo '<br>';
								echo '<label>Name: </label>';
								echo '<input type="text" id="name" value="'.$name.'"></input>';
								echo '<br>';

								library_get_categories_dropdown($cat_id);

								//echo '<input type="text" id="category" value="'.$category.'" placeholder="Food, Entertainment, Gas, etc."></input>';
								echo '<br>';
								echo '<label>Amount: </label>';
								echo '<input type="number" id="amount" value="'.$amount.'" placeholder="x.xx" style="text-align:right;"></input>';
								echo '<br>';
								echo '<label>Date: </label>';
								echo '<input type="date" id="date" value="'.$date.'"></input>';
								echo '<br>';
								echo '<label>Notes: </label>';
								echo '<input type="text" id="notes" value="'.$notes.'"></input>';
								echo '<br>';

								echo '<button style="margin:auto; display:inherit;" name="save_button" onclick="send_expense_to_ajax();" value="Save" class="btn btn-success btn-md">Save</button>';
							echo '</div>';
						} elseif ($form_type == 'Income') {
							// check if there is an id, then we are either editing or deleting an existing record
							if ($selected_id != NULL) {
								// this is a currently existing record
								echo '<h1>Edit Income</h1>';
								$update_type = 'Update';
							} else {
								// this is a new record we are creating
								echo '<h1>Add New Income</h1>';
								$update_type = 'Insert';
							}
							// print the form type here
							echo '<div class="container">';
							 	echo '<p id="selected_id" value="'.$selected_id.'">selected_id: '.$selected_id.'</p>';

								echo '<label>Company: </label>';
								echo '<input type="text" id="company" placeholder="Ingles, QT, Wal-Mart, etc."></input>';
								echo '<br>';
								echo '<label>Name: </label>';
								echo '<input type="text" id="name"></input>';
								echo '<br>';
								echo '<label>Amount: </label>';
								echo '<input type="number" id="amount" placeholder="x.xx"></input>';
								echo '<br>';
								echo '<label>Date: </label>';
								echo '<input type="date" id="date"></input>';
								echo '<br>';
								echo '<label>Notes: </label>';
								echo '<input type="text" id="notes"></input>';
								echo '<br>';

								echo '<button style="margin:auto; display:inherit;" name="save_button" onclick="send_income_to_ajax();" value="Save" class="btn btn-success btn-md">Save</button>';
							echo '</div>';
						}	elseif ($form_type == 'Passive') {
							// check if there is an id, then we are either editing or deleting an existing record
							if ($selected_id != NULL) {
								// this is a currently existing record
								echo '<h1>Edit Passive Income</h1>';
								$update_type = 'Update';
							} else {
								// this is a new record we are creating
								echo '<h1>Add New Passive Income</h1>';
								$update_type = 'Insert';
							}
							// print the form type here
							echo '<div class="container">';
								echo '<p id="selected_id" value="'.$selected_id.'">selected_id: '.$selected_id.'</p>';

								echo '<button style="margin:auto; display:inherit;" name="save_button" onclick="send_passive_to_ajax();" value="Save" class="btn btn-success btn-md">Save</button>';
							echo '</div>';
						}	elseif ($form_type == 'Bill') {
							// check if there is an id, then we are either editing or deleting an existing record
							if ($selected_id != NULL) {
								// this is a currently existing record
								echo '<h1>Edit Bill</h1>';
								$update_type = 'Update';
							} else {
								// this is a new record we are creating
								echo '<h1>Add New Bill</h1>';
								$update_type = 'Insert';
							}
							// print the form type here
							echo '<div class="container">';
								echo '<p id="selected_id" value="'.$selected_id.'">selected_id: '.$selected_id.'</p>';

								echo '<button style="margin:auto; display:inherit;" name="save_button" onclick="send_bill_to_ajax();" value="Save" class="btn btn-success btn-md">Save</button>';
							echo '</div>';
						}
					} else {
						$error_msg = new Library();
						echo '<h2 style="color:red; text-align:center;">'. $error_msg->get_error_msg(). '</h2>';
					}
				?>
    </div>
</div>

<?php
  $footer = new Footer();
  $footer->show_footer();
?>

</body>
</html>
