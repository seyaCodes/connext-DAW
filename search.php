<?php
session_start();
require_once 'db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get search parameters
$stage = isset($_GET['stage']) ? $_GET['stage'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$round_size = isset($_GET['round_size']) ? $_GET['round_size'] : '';
$search_term = isset($_GET['search']) ? $_GET['search'] : '';

// Prepare base query
$sql = "SELECT * FROM users WHERE id != $user_id";

// Add search filters
if (!empty($search_term)) {
    $sql .= " AND (first_name LIKE '%$search_term%' OR last_name LIKE '%$search_term%' OR headline LIKE '%$search_term%' OR bio LIKE '%$search_term%')";
}

if (!empty($stage) && $stage != 'All stages') {
    $sql .= " AND company_stage = '$stage'";
}

if (!empty($location) && $location != 'All locations') {
    $sql .= " AND company_location = '$location'";
}

if (!empty($round_size) && $round_size != 'Any') {
    $sql .= " AND round_size = '$round_size'";
}

// Add ordering
$sql .= " ORDER BY first_name ASC";

// For debugging
$_SESSION['last_query'] = $sql;

// Get user data
$result = mysqli_query($conn, $sql);

// Count results
$result_count = $result ? mysqli_num_rows($result) : 0;

// Get list of saved users
$saved_users = [];
$saved_query = "SELECT saved_user_id FROM saved_deals WHERE user_id = $user_id";
$saved_result = mysqli_query($conn, $saved_query);
if ($saved_result) {
    while ($row = mysqli_fetch_assoc($saved_result)) {
        $saved_users[] = $row['saved_user_id'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connext - Search</title>
    <link rel="stylesheet" href="search.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="main-header">
        <div class="container header-container">
            <div class="logo-container">
                <img src="images/logo.png" alt="Connext Logo" class="logo">
                <p><a href="main.html">con'next</a></p>
            </div>
            <div class="user-controls">
                <div class="user-avatar">
                    <img src="<?php echo !empty($_SESSION['avatar']) ? $_SESSION['avatar'] : 'images/user.jpg'; ?>" alt="User Avatar">
                </div>
                <a href="startup.php" class="startup-button">Go to Profile</a>
            </div>
        </div>
    </header>
    
    <main class="main-content">
        <div class="container">
            <div class="search-container">
                <h1 class="search-title">Find Investment Opportunities</h1>
                
                <!-- Debug information -->
                <div class="debug-info" style="background: #f8f9fa; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; font-size: 12px;">
                    <p><strong>Debug - Last SQL Query:</strong> <?php echo $_SESSION['last_query'] ?? ''; ?></p>
                    <p><strong>Result Count:</strong> <?php echo $result_count; ?></p>
                </div>
                
                <!-- Search filters -->
                <form action="search.php" method="GET" class="search-form">
                    <div class="search-filters">
                        <div class="filter-group">
                            <label>Company stage</label>
                            <select name="stage">
                                <option value="All stages">All stages</option>
                                <option value="Seed" <?php if($stage == 'Seed') echo 'selected'; ?>>Seed</option>
                                <option value="Series A" <?php if($stage == 'Series A') echo 'selected'; ?>>Series A</option>
                                <option value="Series B" <?php if($stage == 'Series B') echo 'selected'; ?>>Series B</option>

<option value="Growth" <?php if($stage == 'Growth') echo 'selected'; ?>>Growth</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label>Company HQ</label>
                            <select name="location">
                                <option value="All locations">All locations</option>
                                <option value="North America" <?php if($location == 'North America') echo 'selected'; ?>>North America</option>
                                <option value="Europe" <?php if($location == 'Europe') echo 'selected'; ?>>Europe</option>
                                <option value="Asia" <?php if($location == 'Asia') echo 'selected'; ?>>Asia</option>
                                <option value="Africa" <?php if($location == 'Africa') echo 'selected'; ?>>Africa</option>
                                <option value="South America" <?php if($location == 'South America') echo 'selected'; ?>>South America</option>
                                <option value="Australia" <?php if($location == 'Australia') echo 'selected'; ?>>Australia</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label>Round size</label>
                            <select name="round_size">
                                <option value="Any">Any</option>
                                <option value="$100k - $500k" <?php if($round_size == '$100k - $500k') echo 'selected'; ?>>$100k - $500k</option>
                                <option value="$500k - $1M" <?php if($round_size == '$500k - $1M') echo 'selected'; ?>>$500k - $1M</option>
                                <option value="$1M - $5M" <?php if($round_size == '$1M - $5M') echo 'selected'; ?>>$1M - $5M</option>
                                <option value="$5M+" <?php if($round_size == '$5M+') echo 'selected'; ?>>$5M+</option>
                            </select>
                        </div>
                        
                        <button type="button" class="clear-filters" onclick="window.location='search.php'">Clear filters</button>
                    </div>
                    
                    <!-- Search bar -->
                    <div class="search-bar">
                        <input type="text" name="search" placeholder="SaaS, fintech, marketplace..." value="<?php echo htmlspecialchars($search_term); ?>">
                        <button type="submit" class="search-button">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </form>
                
                <!-- Search Results -->
                <div class="search-results">
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <div class="user-list">
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <div class="user-card">
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <img src="<?php echo !empty($row['avatar']) ? $row['avatar'] : 'images/user.jpg'; ?>" alt="User Avatar">
                                        </div>
                                        <div class="user-details">
                                            <h3 class="user-name"><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></h3>
                                            <p class="user-headline"><?php echo $row['headline'] ?? ''; ?></p>
                                            <p class="user-role"><?php echo ucfirst($row['role'] ?? 'User'); ?></p>
                                            
                                            <div class="user-metadata">

<?php if (!empty($row['company_stage'])): ?>
                                                    <span class="metadata-item"><i class="fas fa-chart-line"></i> <?php echo $row['company_stage']; ?></span>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($row['company_location'])): ?>
                                                    <span class="metadata-item"><i class="fas fa-map-marker-alt"></i> <?php echo $row['company_location']; ?></span>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($row['round_size'])): ?>
                                                    <span class="metadata-item"><i class="fas fa-dollar-sign"></i> <?php echo $row['round_size']; ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="user-actions">
                                        <a href="view_profile.php?id=<?php echo $row['id']; ?>" class="view-profile">View Profile</a>
                                        <?php
                                            $is_saved = in_array($row['id'], $saved_users);
                                            $save_btn_class = $is_saved ? 'save-contact saved' : 'save-contact';
                                            $save_btn_icon = $is_saved ? 'fas fa-star' : 'far fa-star';
                                            $save_btn_text = $is_saved ? 'Saved' : 'Save';
                                        ?>
                                        <button class="<?php echo $save_btn_class; ?>" data-id="<?php echo $row['id']; ?>">
                                            <i class="<?php echo $save_btn_icon; ?>"></i> <?php echo $save_btn_text; ?>
                                        </button>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <!-- Empty State Message -->
                        <div class="empty-state">
                            <p class="empty-message">No users found matching your search criteria.</p>
                            <p class="search-help">Try adjusting your filters or search terms.</p>
                        </div>
                    <?php endif; ?>
                </div>
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
      <!-- Investors Column -->
      <div class="footer-column">
        <h3 class="footer-heading">for Investors</h3>
        <nav class="footer-nav">
          <a href="investors/why-invest.html">Why invest</a>
          <a href="investors/how-it-works.html">How it works</a>
          <a href="faqi.html">FAQ</a>
          <a href="tr.html">Risks</a>
          <a href="pap.html">Privacy Policy</a>
        </nav>
      </div>
      
      <!-- Startups Column -->
      <div class="footer-column">
        <h3 class="footer-heading">for Startups</h3>
        <nav class="footer-nav">
          <a href="startups/guide.html">Guide</a>
          <a href="faqs.html">FAQ</a>
          <a href="tr.html">Risks</a>
          <a href="pap.html">Privacy Policy</a>
        </nav>
      </div>
      
      <!-- Company Column -->
      <div class="footer-column">
        <h3 class="footer-heading">Company</h3>
        <nav class="footer-nav">
          <a href="abtus.html">About</a>
          <a href="tr.html">Terms & Risks</a>
          <a href="help.html">Help</a>
        </nav>
      </div>
    </div>
  </div>
</footer>
    <script>
        // Add save functionality
        document.addEventListener('DOMContentLoaded', function() {
            const saveButtons = document.querySelectorAll('.save-contact');
            
            saveButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-id');
                    saveUser(userId, this);
                });
            });
            
            function saveUser(userId, button) {
                fetch('save_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'user_id=' + userId,
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success || data.message === 'Already saved') {
                        button.innerHTML = '<i class="fas fa-star"></i> Saved';
                        button.classList.add('saved');
                    } else {
                        alert('Error saving user.

Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error saving user. Please try again.');
                });
            }
        });
    </script>
</body>
</html>