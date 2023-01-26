<?php
declare(strict_types=1);
include '../includes/autoloader.inc.php';
include '../includes/function_library.inc.php';

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../pages/login.php");
    exit;
}

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

$user_id = '';	// this is either Update or Insert
if (isset($_GET['user_id'])){
	$user_id = $_GET['user_id'];
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
  $user_theme = $row['user_theme'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php
    $header = new Header();
    $header->show_header($user_theme);
  ?>
</head>
<body>

<?php
	$navbar = new Navbar();
	$navbar->show_header_nav($loggedin, $user_fname, $id_role, $messages);

  $secondary_tab = '';
  $navbar->show_secondary_nav($loggedin, $secondary_tab);

  $finance_nav = new FinanceNavbar();
  $finance_nav->show_header_nav('', $secondary_tab);
?>

<script type="text/javascript">
	function send_to_ajax(){
    // first check form is good
    if (check_form()) {
      // setup the ajax request
  		var xhttp = new XMLHttpRequest();
  		// get variables from inputs below:
  		var selected_id = document.getElementById('selected_id');
  		var update_type = document.getElementById('update_type');
  		var form_type = document.getElementById('form_type');
  		var user_id = document.getElementById('user_id');

      if (form_type.innerHTML != "Bill" && form_type.innerHTML != "Budget") {
        var company = document.getElementById('company');
      }
      if (form_type.innerHTML != "Budget") {
    		var name_value = document.getElementById('name').value;
        //alert("name_value: " + name_value);
        var remove_apostrophes = name_value.replace(/[^\w\s]/gi, '');
        var name_string = remove_apostrophes.replace(/[^a-zA-Z0-9]/g, ' ');
        //alert("name_string: " + name_string);
        // I don't think I want any f#$@ing candy Martha!!!??
      }
      if (form_type.innerHTML == "Expense" || form_type.innerHTML == "Income") {
        var new_company_name_value = document.getElementById('new_company_name').value;
        var remove_apostrophes = new_company_name_value.replace(/[^\w\s]/gi, '');
        var new_company_name_string = remove_apostrophes.replace(/[^a-zA-Z0-9]/g, ' ');
      }
  		if (form_type.innerHTML == "Expense" || form_type.innerHTML == "Budget" && update_type.innerHTML == "Insert"){
  			var category = document.getElementById('category');
  			var category_value = category.options[category.selectedIndex].value;
  		}
  		var amount = document.getElementById("amount");
      if (form_type.innerHTML != "Bill" && form_type.innerHTML != "Budget") {
        var date = document.getElementById('date');
        var notes_value = document.getElementById('notes').value;
        //alert("notes_value: " + notes_value);
        var remove_apostrophes = notes_value.replace(/[^.\w\s]/gi, '');
        var notes_string = remove_apostrophes.replace(/[^a-zA-Z0-9.]/g, ' ');
        //alert("notes_string: " + notes_string);
      }
  		//if (form_type.innerHTML == "Bill") {
        //var freq = document.getElementById('freq');
  			//var freq_value = freq.options[freq.selectedIndex].value;
      //}

  		// create link to send GET variables through
  		var query_string = "../ajax/finances.ajax.php";
  		query_string += "?selected_id=" + selected_id.innerHTML;
  		query_string += "&update_type=" + update_type.innerHTML;
  		query_string += "&form_type=" + form_type.innerHTML;
  		query_string += "&user_id=" + user_id.innerHTML;

      if (form_type.innerHTML != "Bill" && form_type.innerHTML != "Budget") {
        query_string += "&company=" + company.value;
      }
      if (form_type.innerHTML == "Expense" || form_type.innerHTML == "Income") {
        query_string += "&new_company_name=" + new_company_name_string;
      }
      if (form_type.innerHTML != "Budget") {
    		query_string += "&name=" + name_string;
      }
  		if (form_type.innerHTML == "Expense" || form_type.innerHTML == "Budget"){
  			query_string += "&category=" + category_value;
  		}
  		query_string += "&amount=" + amount.value;
      if (form_type.innerHTML != "Bill" && form_type.innerHTML != "Budget") {
    		query_string += "&date=" + date.value;
    		query_string += "&notes=" + notes_string;
      }

  		xhttp.onreadystatechange = function() {
  			if (this.readyState == 4 && this.status == 200) {
  			 document.getElementById("test").innerHTML = this.responseText;
  			}
  		};
  		xhttp.open("GET", query_string, true);
  		xhttp.send();

  		// when the data is returned after ajax, it redirects back to inventory
  		window.location = "../pages/manage.php";
    } else {
      alert('Form needs to be filled out');
    }

	}

  function update_input_search(new_string) {
      //alert("typed key");
      // setup the ajax request
  		var xhttp = new XMLHttpRequest();
  		// create link to send GET variables through
  		var query_string = "../ajax/search_companies.ajax.php";
  		query_string += "?comp_name=" + new_string;

  		xhttp.onreadystatechange = function() {
  			if (this.readyState == 4 && this.status == 200) {
  			     document.getElementById("search_options_popup").innerHTML = this.responseText;
  			}
  		};
  		xhttp.open("GET", query_string, true);
  		xhttp.send();

      // show the popup
      close_popup(false);
	}

  function insert_selected_name(new_comp_name) {
      var company_input = document.getElementById('company');
      company_input.value = new_comp_name;
      company_input.innerHTML = new_comp_name;
      close_popup(true);
  }

  function close_popup(bool) {
    if (bool) {
        document.getElementById("search_options_popup").style.display = "none";
    } else {
        document.getElementById("search_options_popup").style.display = "block";
    }
  }

  function check_form(){
    var form_type = document.getElementById('form_type');
    if (form_type.innerHTML != "Budget") {
      var name = document.getElementById('name');
    }
    if (form_type.innerHTML == "Expense" || form_type.innerHTML == "Income") {
      var new_company_name = document.getElementById('new_company_name');
      var company = document.getElementById('company');
      if (new_company_name.value == '' && company.value == 'Other') {
        return false;
      }
    }
		var amount = document.getElementById("amount");

    if (form_type.innerHTML != "Budget") {
      if (name.value == '' || amount.value == 0) {
        return false;
      }
    } else {
      if (amount.value == 0) {
        return false;
      }
    }
    return true;
  }

  function update_element_value(element, value){
    element.value = value;
  }

  function show_new_input(that){
    if (that.value == "Other") {
        //alert("check");
        document.getElementById("new_company").style.display = "block";
    } else {
        document.getElementById("new_company").style.display = "none";
    }
  }
</script>

<?php
	echo '<p id="selected_id" style="display:none;" value="'.$selected_id.'">'.$selected_id.'</p>';
	echo '<p id="form_type" style="display:none;" value="'.$form_type.'">'.$form_type.'</p>'; //
	echo '<p id="user_id" style="display:none;" value="'.$user_id.'">'.$user_id.'</p>';

	echo '<p id="test"></p>';

  echo '<div class="mainContentContainer">';
?>
    <div class="container" style="height:600px; text-align:right; width:390px;">
        <div>
				<?php
					if (!$show_error) {
						// check form to set
						if ($form_type == 'Expense') {
							// default variables
							$update_type = "";
							$company = "";  // default for now
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
							echo '<p id="update_type" value="'.$update_type.'" style="display:none;">'.$update_type.'</p>';
              //echo '<i style="color:grey;">
              //(If the company you are looking for doesn\'t show up in the company dropdown list, then set the Company to \'Other\' and leave the company name in the notes section so I can add it later.)
              //</i>';
              echo '<br><br>';

							// print the form type here
							echo '<div class="container">';
                echo '<br>';
								//echo '<label>Company: </label>';
								//echo '<input type="text" id="company" value="'.$company.'" placeholder="Ingles, QT, Wal-Mart, etc."></input>';
                library_get_companies_dropdown($company);

                echo '<p id="new_company" style="display:none;">';
                echo '<label>New Company: </label>';
                echo '<input type="text" id="new_company_name" value="" onchange="update_element_value(this, this.value)"></input>';
                echo '</p>';

								echo '<br>';
								echo '<label>Name: </label>';
								echo '<input type="text" id="name" value="'.$name.'" onchange="update_element_value(this, this.value)"></input>';
								echo '<br>';

								library_get_categories_dropdown($cat_id);

								//echo '<input type="text" id="category" value="'.$category.'" placeholder="Food, Entertainment, Gas, etc."></input>';
								echo '<br>';
								echo '<label>Amount: </label>';
								echo '<input type="number" id="amount" value="'.$amount.'" placeholder="x.xx" style="text-align:right;" onchange="update_element_value(this, this.value)"></input>';
								echo '<br>';
								echo '<label>Date: </label>';
								echo '<input type="date" id="date" value="'.$date.'"></input>';
								echo '<br>';
	              echo '<textarea id="notes" style="height:100px; width:300px;" placeholder="(Notes)">'.$notes.'</textarea>';
								echo '<br>';

								echo '<button style="margin:auto; display:inherit;" name="save_button" onclick="send_to_ajax();" value="Save" class="btn btn-success btn-md">Save</button>';
							echo '</div>';
						} elseif ($form_type == 'Income') {
								// default variables
								$update_type = "";
								$company = "";
								$name = "";
								$amount = 0.00;
								$date = date('Y-m-d');	// default to today
								$notes = "";
								// check if there is an id, then we are either editing or deleting an existing record
								if ($selected_id != NULL) {// this is a currently existing record
									echo '<h1>Edit Income</h1>';
									$update_type = 'Update';
									// load variables from selected_id
									// get values from selected id in table:
										$sql = "SELECT * FROM finance_incomes WHERE fi_id = '".$selected_id."' ";
										$dbh = new Dbh();
										$stmt = $dbh->connect()->query($sql);
										//echo $sql;
										// should only populate one row of data
										while ($row = $stmt->fetch()) {
											//echo $row['fe_company'];
											$company = $row['fi_company'];
											$name = $row['fi_name'];
											// format date
											//echo $row['fe_date'];
											$get_date = date_create($row['fi_date']);
											$formatted_date = date_format($get_date, 'Y-m-d');
											//echo $formatted_date;
											$date = $formatted_date;
											$amount = $row['fi_amount'];
											$notes = $row['fi_notes'];
										}
								} else {
									// this is a new record we are creating
									echo '<h1>Add New Income</h1>';
									$update_type = 'Insert';
								}
								echo '<p id="update_type" value="'.$update_type.'" style="display:none;">'.$update_type.'</p>';
                //echo '<i style="color:grey;">
                //(If the company you are looking for doesn\'t show up in the company dropdown list, then set the Company to \'Other\' and leave the company name in the notes section so I can add it later.)
                //</i>';
                echo '<br><br>';

								// print the form type here
								echo '<div class="container">';

									library_get_companies_dropdown($company);

                  echo '<p id="new_company" style="display:none;">';
                  echo '<label>New Company: </label>';
  								echo '<input type="text" id="new_company_name" value="" onchange="update_element_value(this, this.value)"></input>';
                  echo '</p>';

									echo '<br>';
									echo '<label>Name: </label>';
									echo '<input type="text" id="name" value="'.$name.'" onchange="update_element_value(this, this.value)"></input>';
									echo '<br>';
									echo '<label>Amount: </label>';
									echo '<input type="number" id="amount" value="'.$amount.'" placeholder="x.xx" style="text-align:right;" onchange="update_element_value(this, this.value)"></input>';
									echo '<br>';
									echo '<label>Date: </label>';
									echo '<input type="date" id="date" value="'.$date.'"></input>';
									echo '<br>';
									echo '<textarea id="notes" style="height:100px; width:300px;" placeholder="(Notes)">'.$notes.'</textarea>';
									echo '<br>';

									echo '<button style="margin:auto; display:inherit;" name="save_button" onclick="send_to_ajax();" value="Save" class="btn btn-success btn-md">Save</button>';
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

								echo '<button style="margin:auto; display:inherit;" name="save_button" onclick="send_to_ajax();" value="Save" class="btn btn-success btn-md">Save</button>';
							echo '</div>';
						}	elseif ($form_type == 'Bill') {
              // default variables
              $update_type = "";
              $name = "";
              $amount = 0.00;
              $freq = "";
              // check if there is an id, then we are either editing or deleting an existing record
              if ($selected_id != NULL) {// this is a currently existing record
                echo '<h1>Edit Bill</h1>';
                $update_type = 'Update';
                $editable_name = 'readonly';
                // load variables from selected_id
                // get values from selected id in table:
                  $sql = "SELECT * FROM current_bills WHERE bill_id = '".$selected_id."' ";
                  $dbh = new Dbh();
                  $stmt = $dbh->connect()->query($sql);
                  //echo $sql;
                  // should only populate one row of data
                  while ($row = $stmt->fetch()) {
                    $name = $row['bill_name'];
                    // format date
                    //echo $row['fe_date'];
                    $get_date = date_create($row['bill_created']);
                    $formatted_date = date_format($get_date, 'Y-m-d');
                    //echo $formatted_date;
                    $date = $formatted_date;
                    $amount = $row['bill_amount'];
                    $freq = $row['bill_freq'];
                  }
              } else {
                // this is a new record we are creating
                echo '<h1>Add New Bill</h1>';
                $update_type = 'Insert';
                $editable_name = '';
              }
              echo '<p id="update_type" value="'.$update_type.'" style="display:none;">'.$update_type.'</p>';
              // print the form type here
              echo '<div class="container">';

                echo '<label>Name: </label>';
                echo '<input type="text" id="name" value="'.$name.'" '.$editable_name.'></input>';
                echo '<br>';
                echo '<label>Amount: </label>';
                echo '<input type="number" id="amount" value="'.$amount.'" placeholder="x.xx" style="text-align:right;" onchange="update_element_value(this, this.value)"></input>';
                echo '<br>';

                //library_get_freq_dropdown($freq);
                echo '<p style="color:grey;">(Per month)</p>';

                echo '<br>';
                echo '<button style="margin:auto; display:inherit;" name="save_button" onclick="send_to_ajax();" value="Save" class="btn btn-success btn-md">Save</button>';
              echo '</div>';
						} elseif ($form_type == 'Budget') {
              // default variables
              $update_type = "";
              $name = "";
              $amount = 0.00;
              $freq = "";
              $exclude_names = array(); // define an array to add names to exclude
              // check if there is an id, then we are either editing or deleting an existing record
              if ($selected_id != NULL) {// this is a currently existing record
                echo '<h1>Edit Budget</h1>';
                $update_type = 'Update';
                $show_dropdown = false;
                // load variables from selected_id
                // get values from selected id in table:
                  $sql = "SELECT *
                          FROM budgets bud
                          LEFT JOIN categories cat ON bud.id_category = cat.cat_id
                          WHERE bud.bud_id = '".$selected_id."'
                  ";
                  //echo $sql;
                  $dbh = new Dbh();
                  $stmt = $dbh->connect()->query($sql);
                  // should only populate one row of data
                  while ($row = $stmt->fetch()) {
                    $name = $row['cat_name'];
                    $amount = $row['bud_amount'];
                  }
              } else {
                // this is a new record we are creating
                echo '<h1>Add New Budget</h1>';
                $update_type = 'Insert';
                $show_dropdown = true;

                $sql = "
                        SELECT *
                        FROM budgets bud
                        LEFT JOIN categories cat ON bud.id_category = cat.cat_id

                        WHERE bud.is_active = 1
                        AND cat.is_active = 1
                        AND bud.id_user = ".$user_id.";
                ";
                //echo $sql;
                $dbh = new Dbh();
                $stmt = $dbh->connect()->query($sql);
                // should only populate one row of data
                while ($row = $stmt->fetch()) {
                  array_push($exclude_names, $row['cat_name']);
                }
              }
              echo '<p id="update_type" value="'.$update_type.'" style="display:none;">'.$update_type.'</p>';
              // print the form type here
              echo '<div class="container">';

                if ($show_dropdown == false){
                  echo '<p style="text-align:center;">'.$name.'</p>';
                } else {
                  library_exclude_get_categories_dropdown($exclude_names); // takes an array of the names not to include in the dropdown
                  echo '<br>';
                }

                echo '<label>Amount: </label>';
                echo '<input type="number" id="amount" value="'.$amount.'" placeholder="x.xx" style="text-align:right;" onchange="update_element_value(this, this.value)"></input>';
                echo '<br>';

                //library_get_freq_dropdown($freq);
                echo '<p style="color:grey;">(Per month)</p>';

                echo '<br>';
                echo '<button style="margin:auto; display:inherit;" name="save_button" onclick="send_to_ajax();" value="Save" class="btn btn-success btn-md">Save</button>';
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

  echo '</div>';  // ends the main content container for making footer always display at bottom 

  $footer = new Footer();
  $footer->show_footer();
?>

</body>
</html>
