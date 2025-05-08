<?php
// Database structure check script
// Use this to verify your database is set up correctly for search

session_start();
require_once 'db_connect.php';

// Only allow access from authorized users (admins)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied. Admin access required.";
    exit();
}

// Check required tables
$tables = [
    'users' => [
        'id', 'first_name', 'last_name', 'email', 'password', 'headline', 'bio',
        'avatar', 'linkedin', 'twitter', 'facebook', 'angellist', 'website',
        'role', 'company_stage', 'company_location', 'round_size', 'created_at'
    ],
    'saved_deals' => ['id', 'user_id', 'saved_user_id', 'created_at'],
];

echo "<h1>Database Structure Check</h1>";

// Check each table and columns
foreach ($tables as $table => $columns) {
    echo "<h2>Checking table: $table</h2>";
    
    // Check if table exists
    $table_query = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($table_query) == 0) {
        echo "<p style='color:red;'>❌ Table '$table' does not exist!</p>";
        
        // Provide SQL to create the table
        if ($table == 'users') {
            echo "<pre>
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `headline` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `angellist` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `role` enum('investor','startup','admin') DEFAULT NULL,
  `company_stage` enum('Seed','Series A','Series B','Growth') DEFAULT NULL,
  `company_location` enum('North America','Europe','Asia','Africa','South America','Australia') DEFAULT NULL,
  `round_size` enum('$100k - $500k','$500k - $1M','$1M - $5M','$5M+') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
);
</pre>";
        } elseif ($table == 'saved_deals') {
            echo "<pre>
CREATE TABLE `saved_deals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `saved_user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_saved_idx` (`user_id`,`saved_user_id`),
  KEY `saved_user_id` (`saved_user_id`),
  CONSTRAINT `saved_deals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `saved_deals_ibfk_2` FOREIGN KEY (`saved_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);
</pre>";
        }
        continue;
    }
    
    echo "<p style='color:green;'>✓ Table '$table' exists</p>";
    
    // Check columns in table
    $columns_query = mysqli_query($conn, "SHOW COLUMNS FROM `$table`");
    $existing_columns = [];
    while ($column = mysqli_fetch_assoc($columns_query)) {
        $existing_columns[] = $column['Field'];
    }
    
    // Check for missing columns
    $missing_columns = array_diff($columns, $existing_columns);
    if (!empty($missing_columns)) {
        echo "<p style='color:red;'>❌ Missing columns: " . implode(', ', $missing_columns) . "</p>";
        
        // Generate ALTER TABLE statements for missing columns
        foreach ($missing_columns as $missing_column) {
            $column_def = '';
            switch ($missing_column) {
                case 'role':
                    $column_def = "ADD `role` enum('investor','startup','admin') DEFAULT NULL AFTER `website`";
                    break;
                case 'company_stage':
                    $column_def = "ADD `company_stage` enum('Seed','Series A','Series B','Growth') DEFAULT NULL AFTER `role`";
                    break;
                case 'company_location':
                    $column_def = "ADD `company_location` enum('North America','Europe','Asia','Africa','South America','Australia') DEFAULT NULL AFTER `company_stage`";
                    break;
                case 'round_size':
                    $column_def = "ADD `round_size` enum('$100k - $500k','$500k - $1M','$1M - $5M','$5M+') DEFAULT NULL AFTER `company_location`";
                    break;
                case 'created_at':
                    $column_def = "ADD `created_at` timestamp NOT NULL DEFAULT current_timestamp() AFTER `round_size`";
                    break;
                default:
                    $column_def = "ADD `$missing_column` VARCHAR(255) DEFAULT NULL";
            }
            
            echo "<pre>ALTER TABLE `$table` $column_def;</pre>";
        }
    } else {
        echo "<p style='color:green;'>✓ All required columns exist</p>";
    }
}

// Test query for search
echo "<h2>Testing Search Query</h2>";

$test_sql = "SELECT id, first_name, last_name, headline, role, avatar FROM users WHERE id != 1";
$test_result = mysqli_query($conn, $test_sql);

if (!$test_result) {
    echo "<p style='color:red;'>❌ Test search query failed: " . mysqli_error($conn) . "</p>";
} else {
    $test_count = mysqli_num_rows($test_result);
    echo "<p style='color:green;'>✓ Test search query successful: Found $test_count users</p>";
    
    // Show a few results
    echo "<h3>Sample Data:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Role</th><th>Headline</th></tr>";
    
    $count = 0;
    while ($row = mysqli_fetch_assoc($test_result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['first_name'] . " " . $row['last_name'] . "</td>";
        echo "<td>" . $row['role'] . "</td>";
        echo "<td>" . $row['headline'] . "</td>";
        echo "</tr>";
        
        $count++;
        if ($count >= 5) break; // Show only 5 rows max
    }
    
    echo "</table>";
}

// Check if search is working with all relevant fields
echo "<h2>Checking Search Query With All Filters</h2>";

$full_test_sql = "SELECT id, first_name, last_name, headline, role, avatar FROM users 
                  WHERE id != 1 
                  AND (first_name LIKE '%test%' OR last_name LIKE '%test%' OR headline LIKE '%test%' OR bio LIKE '%test%')
                  AND company_stage = 'Series A'
                  AND company_location = 'Europe'
                  AND round_size = '$1M - $5M'
                  ORDER BY first_name ASC";

$full_test_result = mysqli_query($conn, $full_test_sql);

if (!$full_test_result) {
    echo "<p style='color:red;'>❌ Full search query failed: " . mysqli_error($conn) . "</p>";
    echo "<p>This suggests there might be issues with the column structure or data types.</p>";
} else {
    echo "<p style='color:green;'>✓ Full search query successful</p>";
    echo "<p>Your database structure appears to support the search functionality correctly.</p>";
}
?>