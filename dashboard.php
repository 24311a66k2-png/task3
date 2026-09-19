<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Page: dashboard.php
 * 
 * Authenticated User Dashboard with account metrics, personal overview,
 * and quick access to profile editing and administration.
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php'; // Guard: must be logged in

// Fetch fresh user record from database
$conn = get_db_connection();
$userId = current_user_id();
$stmt = $conn->prepare("SELECT id, full_name, email, role, status, bio, profile_image, date_of_birth, gender, country, created_at, updated_at FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    // Edge case: user record was removed
    set_flash('danger', 'Your account session is invalid.');
    redirect('/logout.php');
}

// Calculate profile completion score
$completionFields = ['full_name', 'email', 'bio', 'profile_image', 'date_of_birth', 'country'];
$filledCount = 0;
foreach ($completionFields as $f) {
    if (!empty($user[$f])) $filledCount++;
}
$completionPercent = round(($filledCount / count($completionFields)) * 100);

$pageTitle = 'User Dashboard';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
  <!-- Flash Alert Feedback -->
  <?php render_flash(); ?>

  <!-- Welcome Hero Banner -->
  <div class="dc-card p-4 p-md-5 mb-4 shadow-sm position-relative overflow-hidden">
    <div class="row align-items-center g-4">
      <div class="col-auto">
        <div class="position-relative">
          <img 
            src="<?php echo get_profile_image_url($user['profile_image']); ?>" 
            alt="Profile Avatar" 
            class="rounded-circle border border-2 border-primary" 
            width="84" 
            height="84" 
            style="object-fit: cover;"
          >
          <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-dark rounded-circle" title="Online Active Session">
            <span class="visually-hidden">Online</span>
          </span>
        </div>
      </div>
      <div class="col">
        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
          <h1 class="h3 fw-bold text-light mb-0"><?php echo e($user['full_name']); ?></h1>
          <span class="badge bg-<?php echo ($user['role'] === 'admin') ? 'warning text-dark' : 'info'; ?>">
            <i class="bi bi-<?php echo ($user['role'] === 'admin') ? 'shield-lock' : 'person-check'; ?> me-1"></i>
            <?php echo strtoupper(e($user['role'])); ?>
          </span>
          <span class="badge bg-<?php echo ($user['status'] === 'active') ? 'success' : 'secondary'; ?>">
            <?php echo strtoupper(e($user['status'])); ?>
          </span>
        </div>
        <p class="text-secondary small mb-0">
          <i class="bi bi-envelope me-1"></i> <?php echo e($user['email']); ?> &nbsp;|&nbsp; 
          <i class="bi bi-calendar3 me-1"></i> Member since <?php echo date('F j, Y', strtotime($user['created_at'])); ?>
        </p>
      </div>
      <div class="col-12 col-md-auto d-flex flex-wrap gap-2">
        <a href="<?php echo BASE_URL; ?>/profile.php" class="btn btn-dc-outline btn-sm">
          <i class="bi bi-person-badge me-1"></i> View Profile
        </a>
        <a href="<?php echo BASE_URL; ?>/edit-profile.php" class="btn btn-dc-primary btn-sm">
          <i class="bi bi-pencil-square me-1"></i> Edit Account
        </a>
        <?php if (is_admin()): ?>
          <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="btn btn-warning btn-sm fw-semibold">
            <i class="bi bi-speedometer me-1"></i> Admin Panel
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Metric & Stat Cards -->
  <div class="row g-4 mb-4">
    <div class="col-md-3 col-sm-6">
      <div class="stat-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="text-secondary small fw-semibold">Account Role</span>
          <i class="bi bi-shield-check text-warning fs-5"></i>
        </div>
        <div class="fs-4 fw-bold text-light text-capitalize"><?php echo e($user['role']); ?></div>
        <div class="text-muted small mt-1">System Permission Tier</div>
      </div>
    </div>

    <div class="col-md-3 col-sm-6">
      <div class="stat-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="text-secondary small fw-semibold">Account Status</span>
          <i class="bi bi-check-circle-fill text-success fs-5"></i>
        </div>
        <div class="fs-4 fw-bold text-light text-capitalize"><?php echo e($user['status']); ?></div>
        <div class="text-muted small mt-1">Full Application Access</div>
      </div>
    </div>

    <div class="col-md-3 col-sm-6">
      <div class="stat-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="text-secondary small fw-semibold">Profile Completion</span>
          <i class="bi bi-pie-chart-fill text-info fs-5"></i>
        </div>
        <div class="fs-4 fw-bold text-light"><?php echo $completionPercent; ?>%</div>
        <div class="progress mt-2" style="height: 6px; background: rgba(255,255,255,0.1);">
          <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $completionPercent; ?>%;" aria-valuenow="<?php echo $completionPercent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
      </div>
    </div>

    <div class="col-md-3 col-sm-6">
      <div class="stat-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="text-secondary small fw-semibold">User ID</span>
          <i class="bi bi-hash text-primary fs-5"></i>
        </div>
        <div class="fs-4 fw-bold text-light">#<?php echo str_pad($user['id'], 4, '0', STR_PAD_LEFT); ?></div>
        <div class="text-muted small mt-1">MySQL Primary Key</div>
      </div>
    </div>
  </div>

  <!-- Account Summary & Quick Operations -->
  <div class="row g-4">
    <!-- Left Column: User Profile Snapshot -->
    <div class="col-lg-8">
      <div class="dc-card p-4 h-100">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-secondary border-opacity-25">
          <h2 class="h5 fw-bold text-light mb-0"><i class="bi bi-person-lines-fill me-2 text-primary"></i> Profile Overview</h2>
          <a href="<?php echo BASE_URL; ?>/edit-profile.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-pencil me-1"></i> Update
          </a>
        </div>

        <div class="row g-3">
          <div class="col-sm-6">
            <div class="p-3 rounded-3 bg-dark bg-opacity-50 border border-secondary border-opacity-10">
              <span class="text-secondary small d-block mb-1">Full Legal Name</span>
              <strong class="text-light"><?php echo e($user['full_name']); ?></strong>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="p-3 rounded-3 bg-dark bg-opacity-50 border border-secondary border-opacity-10">
              <span class="text-secondary small d-block mb-1">Email Address</span>
              <strong class="text-light"><?php echo e($user['email']); ?></strong>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="p-3 rounded-3 bg-dark bg-opacity-50 border border-secondary border-opacity-10">
              <span class="text-secondary small d-block mb-1">Country / Region</span>
              <span class="text-light"><?php echo !empty($user['country']) ? e($user['country']) : '<em class="text-muted">Not specified</em>'; ?></span>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="p-3 rounded-3 bg-dark bg-opacity-50 border border-secondary border-opacity-10">
              <span class="text-secondary small d-block mb-1">Date of Birth</span>
              <span class="text-light"><?php echo !empty($user['date_of_birth']) ? date('M d, Y', strtotime($user['date_of_birth'])) : '<em class="text-muted">Not specified</em>'; ?></span>
            </div>
          </div>
          <div class="col-12">
            <div class="p-3 rounded-3 bg-dark bg-opacity-50 border border-secondary border-opacity-10">
              <span class="text-secondary small d-block mb-1">Developer Bio / Summary</span>
              <p class="text-light mb-0 small" style="white-space: pre-line;">
                <?php echo !empty($user['bio']) ? e($user['bio']) : '<em class="text-muted">No bio added yet. Click "Update" to introduce yourself!</em>'; ?>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column: Quick Actions & Technical Stack -->
    <div class="col-lg-4">
      <div class="dc-card p-4 h-100">
        <h2 class="h5 fw-bold text-light mb-3 pb-2 border-bottom border-secondary border-opacity-25">
          <i class="bi bi-gear-wide-connected me-2 text-primary"></i> Quick Actions
        </h2>
        
        <div class="d-grid gap-2 mb-4">
          <a href="<?php echo BASE_URL; ?>/profile.php" class="btn btn-outline-light text-start py-2">
            <i class="bi bi-person-badge me-2 text-info"></i> View Public Profile
          </a>
          <a href="<?php echo BASE_URL; ?>/edit-profile.php" class="btn btn-outline-light text-start py-2">
            <i class="bi bi-camera me-2 text-primary"></i> Upload Avatar & Change Password
          </a>
          <?php if (is_admin()): ?>
            <a href="<?php echo BASE_URL; ?>/admin/users.php" class="btn btn-outline-warning text-start py-2">
              <i class="bi bi-people me-2 text-warning"></i> Open User CRUD Table
            </a>
            <a href="<?php echo BASE_URL; ?>/admin/create-user.php" class="btn btn-outline-warning text-start py-2">
              <i class="bi bi-person-plus me-2 text-warning"></i> Create New Account (Admin)
            </a>
          <?php endif; ?>
          <a href="<?php echo BASE_URL; ?>/logout.php" class="btn btn-outline-danger text-start py-2">
            <i class="bi bi-box-arrow-right me-2"></i> End Session (Logout)
          </a>
        </div>

        <div class="p-3 rounded-3 bg-dark bg-opacity-50 border border-secondary border-opacity-10 small">
          <div class="fw-semibold text-secondary mb-1"><i class="bi bi-cpu me-1"></i> Technical Stack in Play:</div>
          <ul class="text-muted ps-3 mb-0">
            <li>PHP 8.3 & MySQLi</li>
            <li>Prepared Statements</li>
            <li>Secure File Uploads (Finfo MIME)</li>
            <li>Role-Based Authorization</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
