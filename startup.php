<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

$avatar_path = "images/user.jpg";
if (!empty($user['avatar'])) {
    $avatar_path = $user['avatar'];
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Conext - Connect Investors with Startups</title>
    <link rel="stylesheet" href="startup.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
  </head>
    <body>
      <header class="main-header">
        <div class="container header-container">
          <div class="logo-container">
            <img src="images/logo.png" alt="Conext Logo" class="logo" />
            <h1>con'next</h1>
          </div>
          <div class="user-controls">
            <div class="user-avatar">
              <img src="<?php echo $avatar_path; ?>" alt="User Avatar" />
            </div>
            <a href="logout.php" class="logout-link">Logout</a>
          </div>
        </div>
      </header>

      <nav class="sub-nav">
        <div class="container">
          <ul>
            <li class="active"><a href="#" id="profile-tab">My profile</a></li>
            <li><a href="#" id="applications-tab">Applications</a></li>
            <li>
              <a href="#" id="saved-tab"><i class="fas fa-star"></i> Saved</a>
            </li>
            <li><a href="#" id="settings-tab">Settings</a></li>
          </ul>
        </div>
      </nav>

      <main class="main-content">
        <div class="container">
          <section class="profile-section" id="profile-view">
            <div class="profile-header">
              <div class="profile-avatar">
                <img src="<?php echo $avatar_path; ?>" alt="Profile Avatar" />
              </div>
              <div class="profile-info">
                <h1 class="profile-name"><?php echo !empty($user['first_name']) ? $user['first_name'] . ' ' . $user['last_name'] : 'Guest User'; ?></h1>
                <p class="profile-headline"><?php echo !empty($user['headline']) ? $user['headline'] : ''; ?></p>
                <p class="profile-creation">
                  <i class="fas fa-map-marker-alt"></i> Member since
                  <?php echo date('M Y', strtotime($user['created_at'] ?? 'now')); ?>
                </p>
                <?php if (!empty($user['bio'])): ?>
                <div class="profile-bio">
                  <h3>About</h3>
                  <p><?php echo $user['bio']; ?></p>
                </div>
                <?php endif; ?>





<div class="profile-details">
  <?php if (!empty($user['role'])): ?>
  <div class="detail-item">
    <h3>Role</h3>
    <p><?php echo ucfirst($user['role']); ?></p>
  </div>
  <?php endif; ?>
  
  <?php if (!empty($user['company_location'])): ?>
  <div class="detail-item">
    <h3>Location</h3>
    <p><i class="fas fa-map-marker-alt"></i> <?php echo $user['company_location']; ?></p>
  </div>
  <?php endif; ?>
  
  <?php if (!empty($user['company_stage']) && $user['role'] == 'startup'): ?>
  <div class="detail-item">
    <h3>Company Stage</h3>
    <p><i class="fas fa-chart-line"></i> <?php echo $user['company_stage']; ?></p>
  </div>
  <?php endif; ?>
  
  <?php if (!empty($user['round_size']) && $user['role'] == 'startup'): ?>
  <div class="detail-item">
    <h3>Funding Round</h3>
    <p><i class="fas fa-dollar-sign"></i> <?php echo $user['round_size']; ?></p>
  </div>
  <?php endif; ?>
</div>
              </div>

              
              <div class="profile-actions">
                <button class="edit-profile-button">
                  <i class="fas fa-cog"></i> Edit my profile
                </button>
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
          </section>

          <section
            class="profile-section"
            id="profile-edit"
            style="display: none"
          >
            <h1 class="section-title">Edit my profile</h1>

            <div class="edit-profile-container">
              <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                <div class="edit-column">
                  <h2>About you</h2>

                  <div class="form-group">
                    <label for="avatar">Avatar</label>
                    <div class="file-upload-container">
                      <input type="file" name="avatar" id="avatar" class="file-input" accept="image/*" />
                      <button type="button" class="file-upload-button">Choose a file</button>
                      <span class="file-name">No file chosen</span>
                    </div>
                    <p class="file-requirements">
                      A square .jpg, .gif, or .png image 200x200 or larger
                    </p>
                  </div>

                  <div class="form-group">
                    <label for="first-name">First name</label>
                    <input
                      type="text"
                      id="first-name"
                      name="first_name"
                      value="<?php echo $user['first_name'] ?? ''; ?>"
                    />
                  </div>

                  <div class="form-group">
                    <label for="last-name">Last name</label>
                    <input
                      type="text"
                      id="last-name"
                      name="last_name"
                      value="<?php echo $user['last_name'] ?? ''; ?>"
                    />
                  </div>

                  <div class="form-group">
                    <label for="headline">Headline</label>
                    <input
                      type="text"
                      id="headline"
                      name="headline"
                      placeholder="e.g., Angel Investor, Tech Entrepreneur"
                      value="<?php echo $user['headline'] ?? ''; ?>"
                    />
                  </div>

                  <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea
                      id="bio"
                      name="bio"
                      rows="6"
                      placeholder="Tell others about yourself, your experience, and investment interests"
                    ><?php echo $user['bio'] ?? ''; ?></textarea>
                  </div>
                </div>

                <div class="edit-column">
                  <h2>Links</h2>

                  <div class="form-group">
                    <label for="angellist">AngelList</label>
                    <input
                      type="url"
                      id="angellist"
                      name="angellist"
                      placeholder="https://angel.co/username"
                      value="<?php echo $user['angellist'] ?? ''; ?>"
                    />
                  </div>

                  <div class="form-group">
                    <label for="facebook">Facebook</label>
                    <input
                      type="url"
                      id="facebook"
                      name="facebook"
                      placeholder="https://facebook.com/username"
                      value="<?php echo $user['facebook'] ?? ''; ?>"
                    />
                  </div>

                  <div class="form-group">
                    <label for="twitter">Twitter</label>
                    <input
                      type="url"
                      id="twitter"
                      name="twitter"
                      placeholder="https://twitter.com/username"
                      value="<?php echo $user['twitter'] ?? ''; ?>"
                    />
                  </div>

                  <div class="form-group">
                    <label for="linkedin">LinkedIn</label>
                    <input
                      type="url"
                      id="linkedin"
                      name="linkedin"
                      placeholder="https://www.linkedin.com/in/username"
                      value="<?php echo $user['linkedin'] ?? ''; ?>"
                    />
                  </div>

                  <div class="form-group">
                    <label for="website">Website</label>
                    <input
                      type="url"
                      id="website"
                      name="website"
                      placeholder="https://example.com"
                      value="<?php echo $user['website'] ?? ''; ?>"
                    />
                  </div>

                  <div class="form-actions">
                    <button type="button" class="cancel-button">Cancel</button>
                    <button type="submit" class="save-button">Save</button>
                  </div>
                </div>
              </form>
            </div>
          </section>

          <section
            class="content-section"
            id="applications-view"
            style="display: none"
          >
            <h1 class="section-title">Applications</h1>

            <div class="empty-state">
              <div class="empty-state-icon">
                <i class="fas fa-file-alt"></i>
              </div>
              <p class="empty-state-message">No connect applications yet</p>
              <a href="search.php" class="browse-deals-button">Browse live deals</a>
            </div>
          </section>

          <section class="content-section" id="saved-view" style="display: none">
            <h1 class="section-title">Saved</h1>

            <div class="empty-state">
              <div class="empty-state-icon">
                <i class="fas fa-star"></i>
              </div>
              <p class="empty-state-message">
                No connection opportunities saved yet
              </p>
              <a href="search.php" class="browse-deals-button">Browse live deals</a>
            </div>
          </section>

          <section
            class="content-section"
            id="settings-view"
            style="display: none"
          >
            <h1 class="section-title">Settings</h1>

            <div class="settings-container">
              <form action="update_settings.php" method="POST">
                <div class="settings-section">
                  <h2>Account Settings</h2>

                  <div class="form-group">
                    <label for="email">Email Address</label>
                    <input
                      type="email"
                      id="email"
                      name="email"
                      value="<?php echo $user['email']; ?>"
                      readonly
                    />
                  </div>

                  <div class="form-group">
                    <label for="new-password">New Password</label>
                    <input type="password" id="new-password" name="new_password" />
                  </div>

                 
  <div class="form-group">
    <label for="role">Role</label>
    <select id="role" name="role">
      <option value="investor" <?php echo (isset($user['role']) && $user['role'] == 'investor') ? 'selected' : ''; ?>>Investor</option>
      <option value="startup" <?php echo (isset($user['role']) && $user['role'] == 'startup') ? 'selected' : ''; ?>>Startup</option>
    </select>
  </div>

  <div class="form-group" id="company-stage-group" <?php echo (isset($user['role']) && $user['role'] == 'investor') ? 'style="display:none;"' : ''; ?>>
    <label for="company-stage">Company Stage</label>
    <select id="company-stage" name="company_stage">
      <option value="">Select stage</option>
      <option value="Seed" <?php echo (isset($user['company_stage']) && $user['company_stage'] == 'Seed') ? 'selected' : ''; ?>>Seed</option>
      <option value="Series A" <?php echo (isset($user['company_stage']) && $user['company_stage'] == 'Series A') ? 'selected' : ''; ?>>Series A</option>
      <option value="Series B" <?php echo (isset($user['company_stage']) && $user['company_stage'] == 'Series B') ? 'selected' : ''; ?>>Series B</option>
      <option value="Growth" <?php echo (isset($user['company_stage']) && $user['company_stage'] == 'Growth') ? 'selected' : ''; ?>>Growth</option>
    </select>
  </div>

  <div class="form-group">
    <label for="company-location">Location</label>
    <select id="company-location" name="company_location">
      <option value="">Select location</option>
      <option value="North America" <?php echo (isset($user['company_location']) && $user['company_location'] == 'North America') ? 'selected' : ''; ?>>North America</option>
      <option value="Europe" <?php echo (isset($user['company_location']) && $user['company_location'] == 'Europe') ? 'selected' : ''; ?>>Europe</option>
      <option value="Asia" <?php echo (isset($user['company_location']) && $user['company_location'] == 'Asia') ? 'selected' : ''; ?>>Asia</option>
      <option value="Africa" <?php echo (isset($user['company_location']) && $user['company_location'] == 'Africa') ? 'selected' : ''; ?>>Africa</option>
      <option value="South America" <?php echo (isset($user['company_location']) && $user['company_location'] == 'South America') ? 'selected' : ''; ?>>South America</option>
      <option value="Australia" <?php echo (isset($user['company_location']) && $user['company_location'] == 'Australia') ? 'selected' : ''; ?>>Australia</option>
    </select>
  </div>

  <div class="form-group" id="round-size-group" <?php echo (isset($user['role']) && $user['role'] == 'investor') ? 'style="display:none;"' : ''; ?>>
    <label for="round-size">Funding Round Size</label>
    <select id="round-size" name="round_size">
      <option value="">Select funding round size</option>
      <option value="$100k - $500k" <?php echo (isset($user['round_size']) && $user['round_size'] == '$100k - $500k') ? 'selected' : ''; ?>>$100k - $500k</option>
      <option value="$500k - $1M" <?php echo (isset($user['round_size']) && $user['round_size'] == '$500k - $1M') ? 'selected' : ''; ?>>$500k - $1M</option>
      <option value="$1M - $5M" <?php echo (isset($user['round_size']) && $user['round_size'] == '$1M - $5M') ? 'selected' : ''; ?>>$1M - $5M</option>
      <option value="$5M+" <?php echo (isset($user['round_size']) && $user['round_size'] == '$5M+') ? 'selected' : ''; ?>>$5M+</option>
    </select>
  </div>
                    <label for="confirm-password">Confirm New Password</label>
                    <input
                      type="password"
                      id="confirm-password"
                      name="confirm_password"
                    />
                  </div>

                  <button type="submit" class="save-button settings-save">Save Changes</button>
                </div>
              </form>

              <div class="settings-section danger-zone">
                <h2>Danger Zone</h2>
                <p>
                  Once you delete your account, there is no going back. Please be
                  certain.
                </p>
                <div class="danger-buttons">
                  <button class="delete-account-button" id="delete-account">Delete Account</button>
                  <a href="logout.php" class="logout-button">Log Out</a>
                </div>
              </div>
            </div>
          </section>
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

    <!-- JavaScript to toggle between views -->
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        // Get tab elements
        const profileTab = document.getElementById("profile-tab");
        const applicationsTab = document.getElementById("applications-tab");
        const savedTab = document.getElementById("saved-tab");
        const settingsTab = document.getElementById("settings-tab");

        // Get view elements
        const profileView = document.getElementById("profile-view");
        const profileEdit = document.getElementById("profile-edit");
        const applicationsView = document.getElementById("applications-view");
        const savedView = document.getElementById("saved-view");
        const settingsView = document.getElementById("settings-view");

        // Edit and Cancel buttons
        const editProfileBtn = document.querySelector(".edit-profile-button");
        const cancelBtn = document.querySelector(".cancel-button");

        // Delete account button
        const deleteAccountBtn = document.getElementById("delete-account");
        if (deleteAccountBtn) {
          deleteAccountBtn.addEventListener("click", function() {
            if (confirm("Are you sure you want to delete your account? This action cannot be undone.")) {
              window.location.href = "delete_account.php";
            }
          });
        }

        // File upload handling
        const fileInput = document.getElementById("avatar");
        const fileButton = document.querySelector(".file-upload-button");
        const fileName = document.querySelector(".file-name");
        
        if (fileButton && fileInput && fileName) {
          fileButton.addEventListener("click", function() {
            fileInput.click();
          });
          
          
          fileInput.addEventListener("change", function() {
            if (fileInput.files.length > 0) {
              fileName.textContent = fileInput.files[0].name;
            } else {
              fileName.textContent = "No file chosen";
            }
          });
        }

        // Hide all views function
        function hideAllViews() {
          profileView.style.display = "none";
          profileEdit.style.display = "none";
          applicationsView.style.display = "none";
          savedView.style.display = "none";
          settingsView.style.display = "none";

          // Remove active class from all tabs
          document.querySelectorAll(".sub-nav li").forEach((item) => {
            item.classList.remove("active");
          });
        }

        // Tab click handlers
        profileTab.parentElement.addEventListener("click", function (e) {
          e.preventDefault();
          hideAllViews();
          profileView.style.display = "block";
          this.classList.add("active");
        });

        applicationsTab.parentElement.addEventListener("click", function (e) {
          e.preventDefault();
          hideAllViews();
          applicationsView.style.display = "block";
          this.classList.add("active");
        });

        savedTab.parentElement.addEventListener("click", function (e) {
          e.preventDefault();
          hideAllViews();
          savedView.style.display = "block";
          this.classList.add("active");
        });

        settingsTab.parentElement.addEventListener("click", function (e) {
          e.preventDefault();
          hideAllViews();
          settingsView.style.display = "block";
          this.classList.add("active");
        });

        // Edit profile button
        if (editProfileBtn) {
          editProfileBtn.addEventListener("click", function () {
            profileView.style.display = "none";
            profileEdit.style.display = "block";
          });
        }

        // Cancel button
        if (cancelBtn) {
          cancelBtn.addEventListener("click", function () {
            profileEdit.style.display = "none";
            profileView.style.display = "block";
          });
        }
        
        // Show success message if update was successful
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('update') === 'success') {
          alert('Profile updated successfully!');
        } else if (urlParams.get('update') === 'error') {
          alert('Error updating profile. Please try again.');
        }
      });

      document.addEventListener("DOMContentLoaded", function() {
  // Role selector functionality
  const roleSelect = document.getElementById("role");
  const companyStageGroup = document.getElementById("company-stage-group");
  const roundSizeGroup = document.getElementById("round-size-group");
  
  if (roleSelect) {
    roleSelect.addEventListener("change", function() {
      // Show/hide fields based on role
      if (this.value === "startup") {
        companyStageGroup.style.display = "block";
        roundSizeGroup.style.display = "block";
      } else {
        companyStageGroup.style.display = "none";
        roundSizeGroup.style.display = "none";
      }
    });
  }
});
    </script>
  </body>
</html>