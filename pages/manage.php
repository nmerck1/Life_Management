<?php
// Include necessary files
require_once '../includes/autoloader.inc.php';
require_once '../includes/function_library.inc.php';
require_once '../includes/Database.php'; // Assume this is the class for database operations
require_once '../includes/User.php';       // Assume this is a class for user operations

// Initialize the session and user authentication
session_start();
User::checkLoginAndRedirect();

// Get user details and messages
$user = new User($_SESSION['user_id']);
$userDetails = $user->getUserDetails();
$messages = library_get_num_notifications($user->getId());

// Handle date search
$dateSearch = User::getDateSearch();

// HTML templates
include 'templates/header.php'; // Header HTML
include 'templates/navbar.php'; // Navbar HTML
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php renderHeader($userDetails['user_theme']); ?>
</head>
<body>
    <?php
        renderNavbar($userDetails, $messages);
        renderFinanceNavbar($dateSearch);

        // External JavaScript
        echo '<script type="text/javascript" src="../js/finances_shared.js"></script>';

        // Main content
        echo '<div class="container">';
        library_monthly_tables("Current", strtotime($dateSearch), $user->getId(), 'Manage');
        echo '</div>';

        // Footer
        include 'templates/footer.php'; // Footer HTML
    ?>
</body>
</html>
