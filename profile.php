<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Page: profile.php
 * 
 * User Profile View Interface displaying account information,
 * demographic attributes, bio, avatar, and system metadata.
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php'; // Guard: must be authenticated

// Fetch user data from database
$conn = get_db_connection();
$userId = current_user_id();
$stmt = $conn->prepare("SELECT id, full_name, email, role, status, bio, profile_image, date_of_birth, gender, country, created_at, updated_at FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    set_flash('danger', 'Profile record could not be retrieved.');
    redirect('/logout.php');
}

$pageTitle = 'My Profile';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
      
      <!-- Flash Alert Feedback -->
      <?php render_flash(); ?>

      <!-- Profile Header Card -->
      <div class="dc-card shadow-lg p-4 p-md-5 mb-4 position-relative">
        <div class="d-flex flex-column flex-sm-row align-items-center gap-4 text-center text-sm-start">
          <!-- Avatar Preview -->
          <div class="position-relative">
            <img 
              src="<?php echo get_profile_image_url($user['profile_image']); ?>" 
              alt="<?php echo e($user['full_name']); ?>" 
              class="rounded-circle border border-3 border-primary shadow" 
              width="110" 
              height="110" 
              style="object-fit: cover;"
            >
            <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-dark rounded-circle" title="Active Account"></span>
          </div>

          <!-- User Identifiers -->
          <div class="flex-grow-1">
            <div class="d-flex flex-wrap justify-content-center justify-content-sm-start align-items-center gap-2 mb-2">
              <h1 class="h3 fw-bold text-light mb-0"><?php echo e($user['full_name']); ?></h1>
              <span class="badge bg-<?php echo ($user['role'] === 'admin') ? 'warning text-dark' : 'info'; ?>">
                <i class="bi bi-<?php echo ($user['role'] === 'admin') ? 'shield-lock' : 'person-check'; ?> me-1"></i>
                <?php echo strtoupper(e($user['role'])); ?>
              </span>
              <span class="badge bg-<?php echo ($user['status'] === 'active') ? 'success' : 'secondary'; ?>">
                <?php echo strtoupper(e($user['status'])); ?>
              </span>
            </div>

            <p class="text-secondary small mb-3">
              <i class="bi bi-envelope me-1"></i> <?php echo e($user['email']); ?> &nbsp;|&nbsp; 
              <i class="bi bi-geo-alt me-1"></i> <?php echo !empty($user['country']) ? e($user['country']) : 'Global Community'; ?>
            </p>

            <div class="d-flex flex-wrap justify-content-center justify-content-sm-start gap-2">
              <a href="<?php echo BASE_URL; ?>/edit-profile.php" class="btn btn-dc-primary btn-sm">
                <i class="bi bi-pencil-square me-1"></i> Edit Profile & Avatar
              </a>
              <a href="<?php echo BASE_URL; ?>/dashboard.php" class="btn btn-dc-outline btn-sm">
                <i class="bi bi-speedometer2 me-1"></i> Dashboard
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Profile Details Card -->
      <div class="dc-card shadow-lg p-4 p-md-5 mb-4">
        <h2 class="h5 fw-bold text-light mb-4 pb-2 border-bottom border-secondary border-opacity-25">
          <i class="bi bi-person-lines-fill me-2 text-primary"></i> Personal Details & Biography
        </h2>

        <!-- Bio Section -->
        <div class="mb-4">
          <label class="text-secondary small fw-semibold text-uppercase tracking-wider d-block mb-2">About / Bio</label>
          <div class="p-3 rounded-3 bg-dark bg-opacity-50 border border-secondary border-opacity-15">
            <?php if (!empty($user['bio'])): ?>
              <p class="text-light mb-0" style="white-space: pre-line;"><?php echo e($user['bio']); ?></p>
            <?php else: ?>
              <p class="text-muted fst-italic mb-0">No bio provided yet. You can introduce yourself by editing your profile.</p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Demographic Details Grid -->
        <div class="row g-3 mb-4">
          <div class="col-sm-6">
            <div class="p-3 rounded-3 bg-dark bg-opacity-50 border border-secondary border-opacity-15">
              <span class="text-secondary small d-block mb-1">Date of Birth</span>
              <strong class="text-light">
                <?php echo !empty($user['date_of_birth']) ? date('F j, Y', strtotime($user['date_of_birth'])) : '<span class="text-muted">Not specified</span>'; ?>
              </strong>
            </div>
          </div>

          <div class="col-sm-6">
            <div class="p-3 rounded-3 bg-dark bg-opacity-50 border border-secondary border-opacity-15">
              <span class="text-secondary small d-block mb-1">Gender</span>
              <strong class="text-light text-capitalize">
                <?php echo str_replace('-', ' ', e($user['gender'])); ?>
              </strong>
            </div>
          </div>

          <div class="col-sm-6">
            <div class="p-3 rounded-3 bg-dark bg-opacity-50 border border-secondary border-opacity-15">
              <span class="text-secondary small d-block mb-1">Country / Region</span>
              <strong class="text-light">
                <?php echo !empty($user['country']) ? e($user['country']) : '<span class="text-muted">Not specified</span>'; ?>
              </strong>
            </div>
          </div>

          <div class="col-sm-6">
            <div class="p-3 rounded-3 bg-dark bg-opacity-50 border border-secondary border-opacity-15">
              <span class="text-secondary small d-block mb-1">System User ID</span>
              <strong class="text-primary font-monospace">
                #<?php echo str_pad($user['id'], 5, '0', STR_PAD_LEFT); ?>
              </strong>
            </div>
          </div>
        </div>

        <!-- Security & Metadata Footer -->
        <h2 class="h6 fw-bold text-light mb-3 pt-2 border-top border-secondary border-opacity-25">
          <i class="bi bi-clock-history me-2 text-primary"></i> Account Timestamps
        </h2>
        <div class="row g-3 small text-secondary">
          <div class="col-sm-6">
            <span>Account Registered:</span>
            <span class="text-light ms-1"><?php echo date('Y-m-d H:i:s', strtotime($user['created_at'])); ?></span>
          </div>
          <div class="col-sm-6">
            <span>Last Profile Update:</span>
            <span class="text-light ms-1"><?php echo date('Y-m-d H:i:s', strtotime($user['updated_at'])); ?></span>
          </div>
        </div>

      </div>

    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
