<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Page: admin/users.php
 * 
 * User Management CRUD Table with search, role/status filtering,
 * pagination, safe deletion protection, and action buttons.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/admin-auth.php'; // Guard: admin only

$conn = get_db_connection();

// 1. Capture Filters and Pagination
$search       = trim($_GET['search'] ?? '');
$roleFilter   = trim($_GET['role'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');
$page         = max(1, (int)($_GET['page'] ?? 1));
$perPage      = 10;
$offset       = ($page - 1) * $perPage;

// 2. Build Dynamic Prepared SQL Query
$whereClauses = [];
$params       = [];
$types        = '';

if ($search !== '') {
    $whereClauses[] = "(full_name LIKE ? OR email LIKE ?)";
    $searchWildcard = "%" . $search . "%";
    $params[]       = $searchWildcard;
    $params[]       = $searchWildcard;
    $types         .= 'ss';
}

if ($roleFilter !== '' && in_array($roleFilter, ['admin', 'user'], true)) {
    $whereClauses[] = "role = ?";
    $params[]       = $roleFilter;
    $types         .= 's';
}

if ($statusFilter !== '' && in_array($statusFilter, ['active', 'inactive', 'suspended'], true)) {
    $whereClauses[] = "status = ?";
    $params[]       = $statusFilter;
    $types         .= 's';
}

$whereSql = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

// 3. Count Total Matching Records
$countSql = "SELECT COUNT(*) AS total FROM users $whereSql";
$countStmt = $conn->prepare($countSql);
if (!empty($types)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalRecords = (int)$countStmt->get_result()->fetch_assoc()['total'];
$countStmt->close();

$totalPages = max(1, (int)ceil($totalRecords / $perPage));
if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $perPage;
}

// 4. Fetch Paginated Records
$dataSql = "SELECT id, full_name, email, role, status, profile_image, created_at FROM users $whereSql ORDER BY id DESC LIMIT ? OFFSET ?";
$dataStmt = $conn->prepare($dataSql);

$dataTypes = $types . 'ii';
$dataParams = array_merge($params, [$perPage, $offset]);
$dataStmt->bind_param($dataTypes, ...$dataParams);
$dataStmt->execute();
$users = $dataStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$dataStmt->close();

$currentAdminId = current_user_id();

$pageTitle = 'Manage Users';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
  <!-- Flash Alert Feedback -->
  <?php render_flash(); ?>

  <!-- Header Card -->
  <div class="dc-card p-4 mb-4 shadow-sm">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
      <div>
        <div class="d-flex align-items-center gap-2 mb-1">
          <span class="badge bg-warning text-dark fw-bold">ADMINISTRATION</span>
          <span class="text-secondary small">CRUD Management</span>
        </div>
        <h1 class="h3 fw-bold text-light mb-1">User Management System</h1>
        <p class="text-secondary small mb-0">Search, inspect, edit, or remove user accounts securely</p>
      </div>

      <div class="d-flex gap-2">
        <a href="<?php echo BASE_URL; ?>/admin/create-user.php" class="btn btn-dc-primary btn-sm">
          <i class="bi bi-person-plus me-1"></i> Add New User
        </a>
        <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="btn btn-dc-outline btn-sm">
          <i class="bi bi-speedometer2 me-1"></i> Dashboard
        </a>
      </div>
    </div>
  </div>

  <!-- Search & Filter Controls -->
  <div class="dc-card p-4 mb-4">
    <form method="GET" action="<?php echo BASE_URL; ?>/admin/users.php" class="row g-3 align-items-end">
      <!-- Search Input -->
      <div class="col-lg-4 col-md-6">
        <label for="search" class="form-label small text-secondary fw-semibold">Search by Name or Email</label>
        <div class="input-group">
          <span class="input-group-text bg-dark border-secondary border-opacity-25 text-secondary">
            <i class="bi bi-search"></i>
          </span>
          <input 
            type="text" 
            class="form-control" 
            id="search" 
            name="search" 
            value="<?php echo e($search); ?>" 
            placeholder="Type name or email..."
          >
        </div>
      </div>

      <!-- Role Filter -->
      <div class="col-lg-3 col-md-3 col-6">
        <label for="role" class="form-label small text-secondary fw-semibold">Filter by Role</label>
        <select class="form-select" id="role" name="role">
          <option value="">All Roles</option>
          <option value="user" <?php echo ($roleFilter === 'user') ? 'selected' : ''; ?>>User Only</option>
          <option value="admin" <?php echo ($roleFilter === 'admin') ? 'selected' : ''; ?>>Admin Only</option>
        </select>
      </div>

      <!-- Status Filter -->
      <div class="col-lg-3 col-md-3 col-6">
        <label for="status" class="form-label small text-secondary fw-semibold">Filter by Status</label>
        <select class="form-select" id="status" name="status">
          <option value="">All Statuses</option>
          <option value="active" <?php echo ($statusFilter === 'active') ? 'selected' : ''; ?>>Active</option>
          <option value="inactive" <?php echo ($statusFilter === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
          <option value="suspended" <?php echo ($statusFilter === 'suspended') ? 'selected' : ''; ?>>Suspended</option>
        </select>
      </div>

      <!-- Action Buttons -->
      <div class="col-lg-2 col-md-12 d-flex gap-2">
        <button type="submit" class="btn btn-dc-primary w-100">
          <i class="bi bi-funnel me-1"></i> Filter
        </button>
        <?php if ($search !== '' || $roleFilter !== '' || $statusFilter !== ''): ?>
          <a href="<?php echo BASE_URL; ?>/admin/users.php" class="btn btn-outline-secondary" title="Reset Filters">
            <i class="bi bi-arrow-counterclockwise"></i>
          </a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- Users Table Card -->
  <div class="dc-card shadow-sm p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <span class="text-secondary small">
        Showing <strong><?php echo count($users); ?></strong> of <strong><?php echo $totalRecords; ?></strong> total matching records
      </span>
    </div>

    <div class="table-responsive">
      <table class="table table-dark table-hover align-middle mb-0">
        <thead class="table-dark-header">
          <tr>
            <th scope="col" style="width: 70px;">ID</th>
            <th scope="col">User</th>
            <th scope="col">Email</th>
            <th scope="col">Role</th>
            <th scope="col">Status</th>
            <th scope="col">Registered</th>
            <th scope="col" class="text-end" style="width: 140px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($users)): ?>
            <tr>
              <td colspan="7" class="text-center py-5 text-secondary">
                <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                No users found matching the selected criteria.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($users as $u): ?>
              <tr>
                <td class="font-monospace text-secondary">#<?php echo str_pad($u['id'], 4, '0', STR_PAD_LEFT); ?></td>
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
                    <span class="fw-semibold text-light"><?php echo e($u['full_name']); ?></span>
                  </div>
                </td>
                <td class="font-monospace small text-secondary"><?php echo e($u['email']); ?></td>
                <td>
                  <span class="badge bg-<?php echo ($u['role'] === 'admin') ? 'warning text-dark' : 'info'; ?>">
                    <?php echo strtoupper(e($u['role'])); ?>
                  </span>
                </td>
                <td>
                  <span class="badge bg-<?php echo ($u['status'] === 'active') ? 'success' : 'secondary'; ?>">
                    <?php echo strtoupper(e($u['status'])); ?>
                  </span>
                </td>
                <td class="text-secondary small">
                  <?php echo date('M d, Y', strtotime($u['created_at'])); ?>
                </td>
                <td class="text-end">
                  <div class="d-inline-flex align-items-center gap-1">
                    <!-- Edit Button -->
                    <a href="<?php echo BASE_URL; ?>/admin/edit-user.php?id=<?php echo (int)$u['id']; ?>" class="btn btn-outline-primary btn-sm py-1 px-2" title="Edit User">
                      <i class="bi bi-pencil-square"></i>
                    </a>

                    <!-- Safe Delete Button (Forbidden for Self) -->
                    <?php if ((int)$u['id'] === (int)$currentAdminId): ?>
                      <button class="btn btn-outline-secondary btn-sm py-1 px-2" disabled title="Cannot delete your active account">
                        <i class="bi bi-lock"></i>
                      </button>
                    <?php else: ?>
                      <form action="<?php echo BASE_URL; ?>/admin/delete-user.php" method="POST" class="d-inline confirm-delete-form" data-user-name="<?php echo e($u['full_name']); ?>">
                        <?php echo csrf_input(); ?>
                        <input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
                        <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-2" title="Delete User">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination Controls -->
    <?php if ($totalPages > 1): ?>
      <nav aria-label="Users pagination" class="mt-4">
        <ul class="pagination pagination-sm justify-content-center mb-0">
          <?php
          $queryParams = $_GET;
          ?>
          <!-- Previous Page -->
          <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
            <?php 
              $queryParams['page'] = $page - 1;
              $prevUrl = BASE_URL . '/admin/users.php?' . http_build_query($queryParams);
            ?>
            <a class="page-link bg-dark text-light border-secondary border-opacity-25" href="<?php echo $prevUrl; ?>">
              <i class="bi bi-chevron-left"></i>
            </a>
          </li>

          <!-- Page Numbers -->
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php 
              $queryParams['page'] = $i;
              $numUrl = BASE_URL . '/admin/users.php?' . http_build_query($queryParams);
            ?>
            <li class="page-item <?php echo ($page === $i) ? 'active' : ''; ?>">
              <a class="page-link <?php echo ($page === $i) ? 'bg-primary border-primary' : 'bg-dark text-light border-secondary border-opacity-25'; ?>" href="<?php echo $numUrl; ?>">
                <?php echo $i; ?>
              </a>
            </li>
          <?php endfor; ?>

          <!-- Next Page -->
          <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
            <?php 
              $queryParams['page'] = $page + 1;
              $nextUrl = BASE_URL . '/admin/users.php?' . http_build_query($queryParams);
            ?>
            <a class="page-link bg-dark text-light border-secondary border-opacity-25" href="<?php echo $nextUrl; ?>">
              <i class="bi bi-chevron-right"></i>
            </a>
          </li>
        </ul>
      </nav>
    <?php endif; ?>

  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
