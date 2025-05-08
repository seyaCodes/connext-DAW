<?php
// Include database connection
session_start();
require_once 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if password update was requested
    if (!empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        // Verify passwords match
        if ($_POST['new_password'] === $_POST['confirm_password']) {
            // Hash the new password
            $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            
            // Update password in database
            $sql = "UPDATE users SET password = '$new_password' WHERE id = $user_id";
            
            if (mysqli_query($conn, $sql)) {
                header("Location: startup.php?update=success");
                exit();
            } else {
                header("Location: startup.php?update=error");
                exit();
            }
        } else {
            header("Location: startup.php?update=password_mismatch");
            exit();
        }
    } else {
        // No changes were made
        header("Location: startup.php");
        exit();
    }
}
?>