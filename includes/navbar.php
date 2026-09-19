<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Layout: includes/navbar.php
 */

$currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
?>
<nav class="navbar navbar-expand-lg navbar-dark navbar-devconnect sticky-top" aria-label="Main Navigation">
  <div class="container">
    <!-- Brand Logo -->
    <a class="navbar-brand" href="<?php echo BASE_URL; ?>/index.php">
      <span class="brand-icon-box" aria-hidden="true">
        <i class="bi bi-code-slash"></i>
      </span>
      <span><?php echo APP_NAME; ?></span>
    </a>

    <!-- Mobile Hamburger Toggle -->
    <button 
      class="navbar-toggler" 
      type="button" 
      data-bs-toggle="collapse" 
      data-bs-target="#navbarNav" 
      aria-controls="navbarNav" 
      aria-expanded="false" 
      aria-label="Toggle navigation menu"
    >
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navigation Menu -->
    <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?php echo ($currentScript === 'index.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/index.php">
            <i class="bi bi-house me-1"></i> Home
          </a>
        </li>

        <?php if (is_logged_in()): ?>
          <!-- Authenticated Links -->
          <li class="nav-item">
            <a class="nav-link <?php echo ($currentScript === 'dashboard.php' && strpos($_SERVER['SCRIPT_NAME'], '/admin/') === false) ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/dashboard.php">
              <i class="bi bi-speedometer2 me-1"></i> Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($currentScript === 'profile.php' || $currentScript === 'edit-profile.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/profile.php">
              <i class="bi bi-person-badge me-1"></i> Profile
            </a>
          </li>

          <?php if (is_admin()): ?>
            <!-- Admin Dropdown -->
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-warning fw-semibold <?php echo (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false) ? 'active' : ''; ?>" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-shield-lock me-1"></i> Admin Portal
              </a>
              <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow" aria-labelledby="adminDropdown">
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/dashboard.php"><i class="bi bi-graph-up me-2"></i> Admin Analytics</a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/users.php"><i class="bi bi-people me-2"></i> Manage Users</a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/create-user.php"><i class="bi bi-person-plus me-2"></i> Create New User</a></li>
              </ul>
            </li>
          <?php endif; ?>

        <?php else: ?>
          <!-- Guest Links -->
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_URL; ?>/index.php#features">
              <i class="bi bi-stars me-1"></i> Features
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_URL; ?>/index.php#about">
              <i class="bi bi-info-circle me-1"></i> About
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($currentScript === 'login.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/login.php">
              Sign In
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($currentScript === 'register.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/register.php">
              Register
            </a>
          </li>
        <?php endif; ?>
      </ul>

      <!-- Action Buttons -->
      <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
        <?php if (is_logged_in()): ?>
          <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2 py-1 px-2 rounded-pill" type="button" id="userMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="<?php echo get_profile_image_url($_SESSION['profile_image'] ?? null); ?>" alt="Avatar" class="rounded-circle" width="28" height="28" style="object-fit: cover;">
              <span class="small fw-semibold text-light me-1"><?php echo e($_SESSION['full_name'] ?? 'User'); ?></span>
              <span class="badge bg-<?php echo is_admin() ? 'warning text-dark' : 'info'; ?>" style="font-size: 0.65rem;">
                <?php echo is_admin() ? 'ADMIN' : 'USER'; ?>
              </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow" aria-labelledby="userMenuButton">
              <li><h6 class="dropdown-header text-muted small"><?php echo e($_SESSION['email'] ?? ''); ?></h6></li>
              <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/profile.php"><i class="bi bi-person me-2"></i> My Profile</a></li>
              <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/edit-profile.php"><i class="bi bi-pencil-square me-2"></i> Edit Account</a></li>
              <li><hr class="dropdown-divider border-secondary border-opacity-25"></li>
              <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Sign Out</a></li>
            </ul>
          </div>
        <?php else: ?>
          <a href="<?php echo BASE_URL; ?>/login.php" class="btn btn-dc-outline btn-sm">
            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
          </a>
          <a href="<?php echo BASE_URL; ?>/register.php" class="btn btn-dc-primary btn-sm">
            <i class="bi bi-person-plus me-1"></i> Register Free
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
