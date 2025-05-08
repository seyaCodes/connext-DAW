<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Basic profile information
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name'] ?? '');
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name'] ?? '');
    $headline = mysqli_real_escape_string($conn, $_POST['headline'] ?? '');
    $bio = mysqli_real_escape_string($conn, $_POST['bio'] ?? '');
    
    // Social links
    $linkedin = mysqli_real_escape_string($conn, $_POST['linkedin'] ?? '');
    $twitter = mysqli_real_escape_string($conn, $_POST['twitter'] ?? '');
    $facebook = mysqli_real_escape_string($conn, $_POST['facebook'] ?? '');
    $angellist = mysqli_real_escape_string($conn, $_POST['angellist'] ?? '');
    $website = mysqli_real_escape_string($conn, $_POST['website'] ?? '');
    
    // Searchable fields - ensure these are properly captured
    $role = mysqli_real_escape_string($conn, $_POST['role'] ?? '');
    $company_stage = mysqli_real_escape_string($conn, $_POST['company_stage'] ?? '');
    $company_location = mysqli_real_escape_string($conn, $_POST['company_location'] ?? '');
    $round_size = mysqli_real_escape_string($conn, $_POST['round_size'] ?? '');
    
    // Handle avatar upload
    $avatar_path = '';
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['avatar']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            // Create unique filename
            $new_filename = uniqid() . '.' . $ext;
            $upload_dir = 'uploads/avatars/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $destination = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destination)) {
                $avatar_path = $destination;
            }
        }
    }
    
    // Update user in database
    $sql = "UPDATE users SET 
            first_name = '$first_name', 
            last_name = '$last_name', 
            headline = '$headline', 
            bio = '$bio', 
            linkedin = '$linkedin', 
            twitter = '$twitter', 
            facebook = '$facebook', 
            angellist = '$angellist', 
            website = '$website',
            role = '$role'";
    
    // Only add company_stage if role is startup or if value exists
    if (!empty($company_stage)) {
        $sql .= ", company_stage = '$company_stage'";
    }
    
    // Add company_location if value exists
    if (!empty($company_location)) {
        $sql .= ", company_location = '$company_location'";
    }
    
    // Only add round_size if role is startup or if value exists
    if (!empty($round_size)) {
        $sql .= ", round_size = '$round_size'";
    }
    
    // Add avatar to update query if a new one was uploaded
    if (!empty($avatar_path)) {
        $sql .= ", avatar = '$avatar_path'";
        // Update session variable
        $_SESSION['avatar'] = $avatar_path;
    }
    
    $sql .= " WHERE id = $user_id";
    
    // For debugging - can remove in production
    $_SESSION['last_query'] = $sql;
    
    if (mysqli_query($conn, $sql)) {
        // Debug info - can remove in production
        $_SESSION['profile_updated'] = true;
        
        // Update session variables with the new profile information
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        $_SESSION['role'] = $role;
        
        header("Location: startup.php?update=success");
        exit();
    } else {
        // Debug info - can remove in production
        $_SESSION['profile_update_error'] = mysqli_error($conn);
        
        header("Location: startup.php?update=error");
        exit();
    }
} else {
    // If not POST request, redirect back to profile
    header("Location: startup.php");
    exit();
}
?>