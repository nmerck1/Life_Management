<?php
include '../includes/autoloader.inc.php';
include '../includes/function_library.inc.php';

$comp_name = '';
if ($_GET['comp_name']) {
  $comp_name = $_GET['comp_name'];
}

$search_string = $comp_name . "%";

$sql = "
        SELECT *
        FROM companies
        WHERE (comp_name LIKE '".$search_string."') # OR comp_name = 'Other' // took this out because users can now just type in a new company name isntead
        AND is_active = 1
        ORDER BY comp_name ASC;
";

$create_search_results = "<table class='search_popup_table'>";
if ($conn->query($sql)) {
  $dbh = new Dbh();
  $stmt = $dbh->connect()->query($sql);
  while ($row = $stmt->fetch()) {
    $create_search_results .= "<tr><td><button onclick='insert_selected_name(this.innerHTML);'>".$row['comp_name']."</button></td></tr>";
  }
}
$create_search_results .= "</table>";

echo $create_search_results;
