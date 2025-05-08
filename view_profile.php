<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if a user ID is provided in the URL
if (!isset($_GET['id'])) {
    header('Location: search.php');
    exit();
}

$profile_id = mysqli_real_escape_string($conn, $_GET['id']);

// Get user data
$sql = "SELECT * FROM users WHERE id = $profile_id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    header('Location: search.php');
    exit();
}

$user = mysqli_fetch_assoc($result);

// Set default avatar if not available
$avatar_path = "images/user.jpg";
if (!empty($user['avatar'])) {
    $avatar_path = $user['avatar'];
}

// Check if this user is saved
$current_user_id = $_SESSION['user_id'];
$check_saved_sql = "SELECT * FROM saved_deals WHERE user_id = $current_user_id AND saved_user_id = $profile_id";
$check_saved_result = mysqli_query($conn, $check_saved_sql);
$is_saved = mysqli_num_rows($check_saved_result) > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile - <?php echo $user['first_name'] . ' ' . $user['last_name']; ?></title>
    <link rel="stylesheet" href="startup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Header Navigation -->
    <header class="main-header">
        <div class="container header-container">
            <div class="logo-container">
                <img src="images/logo.png" alt="Conext Logo" class="logo">
                <h1>con'next</h1>
            </div>
            <div class="user-controls">
                <div class="user-avatar">
                    <img src="<?php echo !empty($_SESSION['avatar']) ? $_SESSION['avatar'] : 'images/user.jpg'; ?>" alt="User Avatar">
                </div>
                <a href="startup.php" class="back-to-profile">My Profile</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="view-profile-container">
                <div class="profile-header view-profile-header">
                    <div class="profile-avatar">
                        <img src="<?php echo $avatar_path; ?>" alt="Profile Avatar">
                    </div>
                    <div class="profile-info">
                        <h1 class="profile-name"><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></h1>
                        <p class="profile-headline"><?php echo !empty($user['headline']) ? $user['headline'] : ''; ?></p>
                        <p class="profile-role"><?php echo ucfirst($user['role']); ?></p>
                        
                        <?php if (!empty($user['bio'])): ?>
                        <div class="profile-bio">
                            <h3>About</h3>
                            <p><?php echo $user['bio']; ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="profile-actions">
                        <button class="save-user-button <?php echo $is_saved ? 'saved' : ''; ?>" data-id="<?php echo $profile_id; ?>">
                            <i class="<?php echo $is_saved ? 'fas' : 'far'; ?> fa-star"></i> 
                            <?php echo $is_saved ? 'Saved' : 'Save'; ?>
                        </button>
                        <a href="search.php" class="back-to-search">Back to Search</a>
                    </div>
                </div>
                
                <?php if (!empty($user['linkedin']) || !empty($user['twitter']) || !empty($user['facebook']) || !empty($user['angellist']) || !empty($user['website'])): ?>
                <div class="profile-links">
                    <h3>Links</h3>
                    <div class="links-container">
                        <?php if (!empty($user['linkedin'])): ?>
                        <a href="<?php echo $user['linkedin']; ?>" target="_blank" class="profile-link">
                            <i class="fab fa-linkedin"></i> LinkedIn
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($user['twitter'])): ?>
                        <a href="<?php echo $user['twitter']; ?>" target="_blank" class="profile-link">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($user['facebook'])): ?>
                        <a href="<?php echo $user['facebook']; ?>" target="_blank" class="profile-link">
                            <i class="fab fa-facebook"></i> Facebook
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($user['angellist'])): ?>
                        <a href="<?php echo $user['angellist']; ?>" target="_blank" class="profile-link">
                            <i class="fab fa-angellist"></i> AngelList
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($user['website'])): ?>
                        <a href="<?php echo $user['website']; ?>" target="_blank" class="profile-link">
                            <i class="fas fa-globe"></i> Website
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <!-- Branding Section -->
            <div class="footer-branding">
                <div class="logo-container">
                    <img src="images/logo.png" alt="Logo">
                    <span class="logo-name">con'next</span>
                </div>
                <p class="footer-tagline">Connecting next opportunities with next investments</p>
                <div class="social-icons">
                    <a href="https://facebook.com" class="social-icon"><img src="images/Component 1 (4).png" alt="Facebook"></a>
                    <a href="https://twitter.com" class="social-icon"><img src="images/Component 1 (5).png" alt="Twitter"></a>
                    <a href="https://linkedin.com" class="social-icon"><img src="images/Component 1 (6).png" alt="LinkedIn"></a>
                </div>
            </div>
            
            <!-- Navigation Links -->
            <div class="footer-links">
                <!-- Columns omitted for brevity -->
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const saveButton = document.querySelector('.save-user-button');
            
            if (saveButton) {
                saveButton.addEventListener('click', function() {
                    const userId = this.getAttribute('data-id');
                    saveUser(userId, this);
                });
            }
            
            function saveUser(userId, button) {
                fetch('save_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'user_id=' + userId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        button.innerHTML = '<i class="fas fa-star"></i> Saved';
                        button.classList.add('saved');
                    } else {
                        alert('Error saving user. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    </script>
</body>
</html>