<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Page: edit-profile.php
 * 
 * User Profile Editing Interface allowing updates to demographic details,
 * biography, profile photo upload (with live preview), and password change.
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php'; // Guard: must be authenticated

// Fetch user data from database
$conn = get_db_connection();
$userId = current_user_id();
$stmt = $conn->prepare("SELECT id, full_name, email, role, status, bio, profile_image, date_of_birth, gender, country FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    set_flash('danger', 'Profile record could not be retrieved.');
    redirect('/logout.php');
}

$pageTitle = 'Edit Profile';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
      
      <!-- Flash Alert Feedback -->
      <?php render_flash(); ?>

      <div class="dc-card shadow-lg p-4 p-md-5">
        <!-- Form Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25">
          <div>
            <h1 class="h3 fw-bold text-light mb-1">Edit Account Profile</h1>
            <p class="text-secondary small mb-0">Update your public credentials and personal preferences</p>
          </div>
          <a href="<?php echo BASE_URL; ?>/profile.php" class="btn btn-dc-outline btn-sm">
            <i class="bi bi-arrow-left me-1"></i> View Profile
          </a>
        </div>

        <!-- Profile Edit Form -->
        <form action="<?php echo BASE_URL; ?>/actions/profile-action.php" method="POST" enctype="multipart/form-data" novalidate>
          <?php echo csrf_input(); ?>

          <!-- Section 1: Avatar Upload with Live Preview -->
          <div class="p-3 mb-4 rounded-3 border border-secondary border-opacity-25" style="background: var(--dc-bg-surface);">
            <label class="form-label fw-bold text-light mb-2">Profile Avatar Photo</label>
            <div class="d-flex flex-column flex-sm-row align-items-center gap-4">
              <div>
                <img 
                  id="avatar-preview"
                  src="<?php echo get_profile_image_url($user['profile_image']); ?>" 
                  alt="Avatar Preview" 
                  class="rounded-circle border border-2 border-primary shadow-sm" 
                  width="96" 
                  height="96" 
                  style="object-fit: cover;"
                >
              </div>
              <div class="flex-grow-1 w-100">
                <input 
                  type="file" 
                  class="form-control" 
                  id="profile_image" 
                  name="profile_image" 
                  accept="image/jpeg,image/png,image/webp"
                >
                <div class="form-text text-secondary small mt-2">
                  <i class="bi bi-info-circle me-1"></i> Supported: JPEG, PNG, WEBP. Max file size: 2 MB. Live preview enabled.
                </div>
              </div>
            </div>
          </div>

          <!-- Section 2: Personal Details -->
          <h2 class="h5 fw-bold text-light mb-3">
            <i class="bi bi-person-lines-fill me-2 text-primary"></i> Personal Information
          </h2>

          <div class="row g-3 mb-3">
            <!-- Full Name -->
            <div class="col-md-6">
              <label for="full_name" class="form-label">Full Name <span class="required-star">*</span></label>
              <input 
                type="text" 
                class="form-control" 
                id="full_name" 
                name="full_name" 
                value="<?php echo e($user['full_name']); ?>" 
                required
              >
            </div>

            <!-- Email (Read-Only) -->
            <div class="col-md-6">
              <label for="email" class="form-label">Email Address</label>
              <input 
                type="email" 
                class="form-control text-muted" 
                id="email" 
                value="<?php echo e($user['email']); ?>" 
                disabled 
                readonly
              >
              <div class="form-text text-secondary small">Email cannot be changed directly for security.</div>
            </div>

            <!-- Date of Birth -->
            <div class="col-md-6">
              <label for="dob" class="form-label">Date of Birth</label>
              <input 
                type="date" 
                class="form-control" 
                id="dob" 
                name="dob" 
                value="<?php echo e($user['date_of_birth'] ?? ''); ?>" 
                max="<?php echo date('Y-m-d'); ?>"
              >
            </div>

            <!-- Gender -->
            <div class="col-md-6">
              <label for="gender" class="form-label">Gender</label>
              <select class="form-select" id="gender" name="gender">
                <option value="prefer-not-to-say" <?php echo ($user['gender'] === 'prefer-not-to-say') ? 'selected' : ''; ?>>Prefer not to say</option>
                <option value="male" <?php echo ($user['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                <option value="female" <?php echo ($user['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                <option value="non-binary" <?php echo ($user['gender'] === 'non-binary') ? 'selected' : ''; ?>>Non-binary</option>
              </select>
            </div>

            <!-- Country -->
            <div class="col-12">
              <label for="country" class="form-label">Country / Region</label>
              <input 
                type="text" 
                class="form-control" 
                id="country" 
                name="country" 
                value="<?php echo e($user['country'] ?? ''); ?>" 
                placeholder="e.g. India, United Kingdom, Canada..."
              >
            </div>

            <!-- Bio -->
            <div class="col-12">
              <label for="bio" class="form-label">Professional Biography</label>
              <textarea 
                class="form-control" 
                id="bio" 
                name="bio" 
                rows="4" 
                placeholder="Tell the DevConnect community about your developer focus, tech stack, and goals..."
              ><?php echo e($user['bio'] ?? ''); ?></textarea>
            </div>
          </div>

          <!-- Section 3: Optional Password Change -->
          <div class="mt-4 pt-4 border-top border-secondary border-opacity-25">
            <h2 class="h5 fw-bold text-light mb-1">
              <i class="bi bi-shield-lock me-2 text-warning"></i> Security & Password Change
            </h2>
            <p class="text-secondary small mb-3">Leave these fields blank if you do not want to alter your login password.</p>

            <div class="row g-3">
              <!-- Current Password -->
              <div class="col-12">
                <label for="current_password" class="form-label">Current Password</label>
                <div class="input-group">
                  <input 
                    type="password" 
                    class="form-control" 
                    id="current_password" 
                    name="current_password" 
                    placeholder="Enter current password to authorize change"
                    autocomplete="current-password"
                  >
                  <button type="button" class="btn password-toggle-btn" data-target="current_password" aria-label="Toggle password visibility">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>

              <!-- New Password -->
              <div class="col-md-6">
                <label for="new_password" class="form-label">New Password</label>
                <div class="input-group">
                  <input 
                    type="password" 
                    class="form-control" 
                    id="new_password" 
                    name="new_password" 
                    placeholder="Min 8 chars"
                    autocomplete="new-password"
                  >
                  <button type="button" class="btn password-toggle-btn" data-target="new_password" aria-label="Toggle password visibility">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>

              <!-- Confirm New Password -->
              <div class="col-md-6">
                <label for="confirm_password" class="form-label">Confirm New Password</label>
                <div class="input-group">
                  <input 
                    type="password" 
                    class="form-control" 
                    id="confirm_password" 
                    name="confirm_password" 
                    placeholder="Re-enter new password"
                    autocomplete="new-password"
                  >
                  <button type="button" class="btn password-toggle-btn" data-target="confirm_password" aria-label="Toggle password visibility">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Buttons -->
          <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top border-secondary border-opacity-25">
            <a href="<?php echo BASE_URL; ?>/profile.php" class="btn btn-outline-secondary">
              Cancel
            </a>
            <button type="submit" class="btn btn-dc-primary px-4">
              <i class="bi bi-save me-1"></i> Save Changes
            </button>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
