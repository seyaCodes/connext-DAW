<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Delete saved deals
$delete_saved = "DELETE FROM saved_deals WHERE user_id = $user_id OR saved_user_id = $user_id";
mysqli_query($conn, $delete_saved);

// Delete user
$delete_user = "DELETE FROM users WHERE id = $user_id";
if (mysqli_query($conn, $delete_user)) {
    // Destroy session
    session_destroy();
    
    // Redirect to login page with success message
    header("Location: login.php?account=deleted");
    exit();
} else {
    // Redirect back with error
    header("Location: startup.php?delete=error");
    exit();
}
?>