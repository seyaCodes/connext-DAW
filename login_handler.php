<?php
session_start();
require_once 'db_connect.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get email and password from form
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    // Check if email exists
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Store user data in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            if (!empty($user['avatar'])) {
                $_SESSION['avatar'] = $user['avatar'];
            }
            
            // Redirect to profile page
            header("Location: startup.php");
            exit();
        } else {
            // Invalid password
            header("Location: login.php?error=invalid");
            exit();
        }
    } else {
        // User not found
        header("Location: login.php?error=notfound");
        exit();
    }
} else {
    // If not submitted via POST, redirect to login page
    header("Location: login.php");
    exit();
}
?>