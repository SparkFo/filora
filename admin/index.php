<?php
// Redirect to login page if not logged in, or to dashboard if logged in
session_start();

// Check if user is logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // Redirect to dashboard
    header('Location: dashboard.php');
    exit;
} else {
    // Redirect to login page
    header('Location: login.php');
    exit;
}
?>
