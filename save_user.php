<?php
session_start();
require_once 'db_connect.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Get the current user's ID
$user_id = $_SESSION['user_id'];

// Get the ID of the user to save
if (isset($_POST['user_id'])) {
    $saved_user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    
    // Check if the saved_deals table exists
    $table_check = mysqli_query($conn, "SHOW TABLES LIKE 'saved_deals'");
    
    // If the table doesn't exist, create it
    if (mysqli_num_rows($table_check) == 0) {
        $create_table_sql = "CREATE TABLE saved_deals (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            user_id INT(11) NOT NULL,
            saved_user_id INT(11) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_saved_deal (user_id, saved_user_id)
        )";
        
        if (!mysqli_query($conn, $create_table_sql)) {
            echo json_encode([
                'success' => false, 
                'message' => 'Could not create saved_deals table', 
                'debug' => mysqli_error($conn)
            ]);
            exit();
        }
    }
    
    // Check if already saved
    $check_sql = "SELECT * FROM saved_deals WHERE user_id = $user_id AND saved_user_id = $saved_user_id";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (!$check_result) {
        echo json_encode([
            'success' => false, 
            'message' => 'Database error checking saved status', 
            'debug' => mysqli_error($conn)
        ]);
        exit();
    }
    
    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode(['success' => true, 'message' => 'Already saved']);
        exit();
    }
    
    // Save the user
    $insert_sql = "INSERT INTO saved_deals (user_id, saved_user_id) VALUES ($user_id, $saved_user_id)";
    
    if (mysqli_query($conn, $insert_sql)) {
        echo json_encode(['success' => true, 'message' => 'User saved successfully']);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Database error saving user', 
            'debug' => mysqli_error($conn)
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No user ID provided']);
}
?>