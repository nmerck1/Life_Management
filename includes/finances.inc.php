<?php
declare(strict_types=1);
include '../includes/autoloader.inc.php';

$show_error = false;

$fi_id;
if (isset($_POST['fi_id'])){
	$fi_id = $_POST['fi_id'];
}
$form_type;// this is what type of form we need for this type of record
if (isset($_POST['form_type'])){
	$form_type = $_POST['form_type'];
} else {
	$show_error = true;
}

if (!$show_error) {
	// check form to set
	if ($form_type == 'Expense') {
		// echo out form
		echo '<div class="container">';
			echo '<button style="margin:auto; display:inherit;" name="save_button" onclick="update_finances();" value="Save" class="btn btn-success btn-md">Save</button>';
		echo '</div>';
		// check if there is an id, then we are either editing or deleting an existing record
		if ($fi_id != NULL) {

		} else {

		}
	} elseif ($form_type == 'Income') {
		// check if there is an id, then we are either editing or deleting an existing record
		if ($fi_id != NULL) {

		} else {

		}
	}	elseif ($form_type == 'Passive') {
		// check if there is an id, then we are either editing or deleting an existing record
		if ($fi_id != NULL) {

		} else {

		}
	}	elseif ($form_type == 'Bill') {
		// check if there is an id, then we are either editing or deleting an existing record
		if ($fi_id != NULL) {

		} else {

		}
	}
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

<div class="container-fluid text-center">
  <div class="row content">
    <div class="col-sm-8 text-left">
				<?php
					if ($show_error) {
						$error_msg = new Library();
						echo '<h2 style="color:red; text-align:center;">'. $error_msg->get_error_msg(). '</h2>';
					}
				?>
    </div>
  </div>
</div>

<footer class="container-fluid text-center">
  <p class="bi-egg" style="color:white;"></p>
</footer>

</body>
</html>