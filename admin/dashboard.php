<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Page: admin/dashboard.php
 * 
 * Admin Analytics Dashboard displaying platform KPIs, user metrics,
 * recent registrations, and system environment info.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/admin-auth.php'; // Guard: admin only

$conn = get_db_connection();

// 1. Fetch Real KPI Counts
$totalUsersQuery = $conn->query("SELECT COUNT(*) AS total FROM users");
$totalUsers = $totalUsersQuery ? (int)$totalUsersQuery->fetch_assoc()['total'] : 0;

$activeUsersQuery = $conn->query("SELECT COUNT(*) AS total FROM users WHERE status = 'active'");
$activeUsers = $activeUsersQuery ? (int)$activeUsersQuery->fetch_assoc()['total'] : 0;

$inactiveUsersQuery = $conn->query("SELECT COUNT(*) AS total FROM users WHERE status != 'active'");
$inactiveUsers = $inactiveUsersQuery ? (int)$inactiveUsersQuery->fetch_assoc()['total'] : 0;

$adminCountQuery = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'admin'");
$adminCount = $adminCountQuery ? (int)$adminCountQuery->fetch_assoc()['total'] : 0;

// 2. Fetch Recent Registrations (Limit 5)
$recentStmt = $conn->prepare("SELECT id, full_name, email, role, status, profile_image, created_at FROM users ORDER BY id DESC LIMIT 5");
$recentStmt->execute();
$recentUsers = $recentStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$recentStmt->close();

$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
  <!-- Flash Alert Feedback -->
  <?php render_flash(); ?>

  <!-- Admin Welcome Banner -->
  <div class="dc-card p-4 p-md-5 mb-4 shadow-sm">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
      <div>
        <div class="d-flex align-items-center gap-2 mb-1">
          <span class="badge bg-warning text-dark fw-bold px-2 py-1">
            <i class="bi bi-shield-lock-fill me-1"></i> ADMIN PORTAL
          </span>
          <span class="text-secondary small">System Control Center</span>
        </div>
        <h1 class="h3 fw-bold text-light mb-1">Platform Administration & Analytics</h1>
        <p class="text-secondary small mb-0">Overview of user metrics, registration pipeline, and system health</p>
      </div>

      <div class="d-flex flex-wrap gap-2">
        <a href="<?php echo BASE_URL; ?>/admin/users.php" class="btn btn-dc-primary btn-sm">
          <i class="bi bi-people-fill me-1"></i> Manage All Users
        </a>
        <a href="<?php echo BASE_URL; ?>/admin/create-user.php" class="btn btn-warning btn-sm fw-semibold">
          <i class="bi bi-person-plus-fill me-1"></i> Create User
        </a>
      </div>
    </div>
  </div>

  <!-- Metric & Stat Cards -->
  <div class="row g-4 mb-4">
    <!-- Total Users -->
    <div class="col-xl-3 col-sm-6">
      <div class="stat-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="text-secondary small fw-semibold text-uppercase">Total Users</span>
          <div class="p-2 rounded-3 bg-primary bg-opacity-25 text-primary">
            <i class="bi bi-people fs-5"></i>
          </div>
        </div>
        <div class="fs-2 fw-bold text-light"><?php echo number_format($totalUsers); ?></div>
        <div class="text-muted small mt-1">All registered database records</div>
      </div>
    </div>

    <!-- Active Accounts -->
    <div class="col-xl-3 col-sm-6">
      <div class="stat-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="text-secondary small fw-semibold text-uppercase">Active Accounts</span>
          <div class="p-2 rounded-3 bg-success bg-opacity-25 text-success">
            <i class="bi bi-check2-circle fs-5"></i>
          </div>
        </div>
        <div class="fs-2 fw-bold text-success"><?php echo number_format($activeUsers); ?></div>
        <div class="text-muted small mt-1">Accounts authorized to sign in</div>
      </div>
    </div>

    <!-- Inactive Accounts -->
    <div class="col-xl-3 col-sm-6">
      <div class="stat-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="text-secondary small fw-semibold text-uppercase">Inactive / Flagged</span>
          <div class="p-2 rounded-3 bg-danger bg-opacity-25 text-danger">
            <i class="bi bi-person-x fs-5"></i>
          </div>
        </div>
        <div class="fs-2 fw-bold text-danger"><?php echo number_format($inactiveUsers); ?></div>
        <div class="text-muted small mt-1">Suspended or pending review</div>
      </div>
    </div>

    <!-- System Admins -->
    <div class="col-xl-3 col-sm-6">
      <div class="stat-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="text-secondary small fw-semibold text-uppercase">Administrators</span>
          <div class="p-2 rounded-3 bg-warning bg-opacity-25 text-warning">
            <i class="bi bi-shield-shaded fs-5"></i>
          </div>
        </div>
        <div class="fs-2 fw-bold text-warning"><?php echo number_format($adminCount); ?></div>
        <div class="text-muted small mt-1">Full administrative privileges</div>
      </div>
    </div>
  </div>

  <!-- Recent Users & System Specifications -->
  <div class="row g-4">
    <!-- Left Column: Recent Registrations -->
    <div class="col-lg-8">
      <div class="dc-card p-4 h-100">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-secondary border-opacity-25">
          <h2 class="h5 fw-bold text-light mb-0">
            <i class="bi bi-person-lines-fill me-2 text-primary"></i> Recently Registered Users
          </h2>
          <a href="<?php echo BASE_URL; ?>/admin/users.php" class="btn btn-outline-secondary btn-sm">
            View All <i class="bi bi-arrow-right ms-1"></i>
          </a>
        </div>

        <div class="table-responsive">
          <table class="table table-dark table-hover align-middle mb-0">
            <thead class="table-dark-header">
              <tr>
                <th scope="col">User</th>
                <th scope="col">Role</th>
                <th scope="col">Status</th>
                <th scope="col">Registered</th>
                <th scope="col" class="text-end">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($recentUsers)): ?>
                <tr>
                  <td colspan="5" class="text-center py-4 text-muted">No registered users found.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($recentUsers as $u): ?>
                  <tr>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <img 
                          src="<?php echo get_profile_image_url($u['profile_image']); ?>" 
                          alt="Avatar" 
                          class="rounded-circle" 
                          width="34" 
                          height="34" 
                          style="object-fit: cover;"
                        >
                        <div>
                          <div class="fw-semibold text-light"><?php echo e($u['full_name']); ?></div>
                          <div class="text-secondary small font-monospace"><?php echo e($u['email']); ?></div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="badge bg-<?php echo ($u['role'] === 'admin') ? 'warning text-dark' : 'info'; ?>">
                        <?php echo strtoupper(e($u['role'])); ?>
                      </span>
                    </td>
                    <td>
                      <span class="badge bg-<?php echo ($u['status'] === 'active') ? 'success' : 'danger'; ?>">
                        <?php echo strtoupper(e($u['status'])); ?>
                      </span>
                    </td>
                    <td class="text-secondary small">
                      <?php echo date('M d, Y', strtotime($u['created_at'])); ?>
                    </td>
                    <td class="text-end">
                      <a href="<?php echo BASE_URL; ?>/admin/edit-user.php?id=<?php echo (int)$u['id']; ?>" class="btn btn-outline-primary btn-sm py-0 px-2" title="Edit User">
                        <i class="bi bi-pencil"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Right Column: System Environment & Quick Links -->
    <div class="col-lg-4">
      <div class="dc-card p-4 h-100">
        <h2 class="h5 fw-bold text-light mb-3 pb-2 border-bottom border-secondary border-opacity-25">
          <i class="bi bi-hdd-network me-2 text-primary"></i> Server Environment
        </h2>

        <div class="list-group list-group-flush bg-transparent mb-4">
          <div class="list-group-item bg-transparent text-secondary px-0 py-2 d-flex justify-content-between align-items-center">
            <span>PHP Engine:</span>
            <span class="text-light font-monospace small"><?php echo phpversion(); ?></span>
          </div>
          <div class="list-group-item bg-transparent text-secondary px-0 py-2 d-flex justify-content-between align-items-center">
            <span>Database:</span>
            <span class="text-light font-monospace small">MySQL <?php echo $conn->server_info; ?></span>
          </div>
          <div class="list-group-item bg-transparent text-secondary px-0 py-2 d-flex justify-content-between align-items-center">
            <span>DB Extension:</span>
            <span class="text-success font-monospace small">MySQLi (Prepared)</span>
          </div>
          <div class="list-group-item bg-transparent text-secondary px-0 py-2 d-flex justify-content-between align-items-center">
            <span>Database Name:</span>
            <span class="text-light font-monospace small"><?php echo DB_NAME; ?></span>
          </div>
          <div class="list-group-item bg-transparent text-secondary px-0 py-2 d-flex justify-content-between align-items-center">
            <span>Session Fixation:</span>
            <span class="text-success font-monospace small">Regenerated (Safe)</span>
          </div>
        </div>

        <div class="d-grid gap-2">
          <a href="<?php echo BASE_URL; ?>/admin/users.php" class="btn btn-dc-outline py-2">
            <i class="bi bi-search me-1"></i> Search & Filter Users
          </a>
          <a href="<?php echo BASE_URL; ?>/admin/create-user.php" class="btn btn-dc-primary py-2">
            <i class="bi bi-person-plus me-1"></i> Add User Record
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
