<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Page: index.php
 * 
 * Public Landing Page demonstrating session awareness, responsive layout, and full-stack architecture.
 */

$pageTitle = 'Connect. Build. Grow. — User Management & CRUD Portal';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container mt-4">
  <?php render_flash(); ?>
</div>

<!-- Hero Section -->
<section class="py-5">
  <div class="container py-4">
    <div class="row align-items-center g-5">
      <!-- Left Column: Copy & Actions -->
      <div class="col-lg-6">
        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3" style="background: rgba(99, 102, 241, 0.12); border: 1px solid rgba(99, 102, 241, 0.3);">
          <span class="rounded-circle bg-info" style="width: 8px; height: 8px;"></span>
          <span class="small fw-semibold text-info">ApexPlanet Internship • Task 3 Backend Milestone</span>
        </div>

        <h1 class="display-4 fw-extrabold mb-3">
          Manage. Secure. <span class="text-gradient">Scale.</span>
        </h1>

        <p class="lead text-secondary mb-4 fs-6" style="line-height: 1.75;">
          DevConnect demonstrates real-world PHP 8+ and MySQL integration. Featuring role-based authentication, session security, prepared statements, avatar uploads, and full CRUD administration.
        </p>

        <?php if (is_logged_in()): ?>
          <!-- Authenticated User Callout -->
          <div class="p-3 mb-4 rounded-3 border border-secondary border-opacity-25" style="background: var(--dc-bg-surface);">
            <div class="d-flex align-items-center gap-3">
              <img src="<?php echo get_profile_image_url($_SESSION['profile_image'] ?? null); ?>" alt="User Avatar" class="rounded-circle border border-info" width="48" height="48" style="object-fit: cover;">
              <div>
                <div class="fw-bold text-light">Logged in as <?php echo e($_SESSION['full_name']); ?></div>
                <div class="small text-muted"><?php echo e($_SESSION['email']); ?> • Role: <span class="badge bg-<?php echo is_admin() ? 'warning text-dark' : 'info'; ?>"><?php echo strtoupper(e($_SESSION['role'])); ?></span></div>
              </div>
            </div>
          </div>

          <div class="d-flex flex-wrap gap-3">
            <a href="<?php echo BASE_URL; ?>/dashboard.php" class="btn btn-dc-primary btn-lg fs-6">
              <i class="bi bi-speedometer2 me-1"></i> Open Dashboard
            </a>
            <?php if (is_admin()): ?>
              <a href="<?php echo BASE_URL; ?>/admin/users.php" class="btn btn-dc-outline btn-lg fs-6">
                <i class="bi bi-people me-1"></i> Admin User CRUD
              </a>
            <?php else: ?>
              <a href="<?php echo BASE_URL; ?>/profile.php" class="btn btn-dc-outline btn-lg fs-6">
                <i class="bi bi-person-badge me-1"></i> View Profile
              </a>
            <?php endif; ?>
          </div>

        <?php else: ?>
          <!-- Guest CTAs -->
          <div class="d-flex flex-wrap gap-3 mb-4">
            <a href="<?php echo BASE_URL; ?>/register.php" class="btn btn-dc-primary btn-lg fs-6">
              <i class="bi bi-person-plus me-1"></i> Create Account
            </a>
            <a href="<?php echo BASE_URL; ?>/login.php" class="btn btn-dc-outline btn-lg fs-6">
              <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
            </a>
          </div>

          <!-- Seed Account Credentials for Evaluators -->
          <div class="p-3 rounded-3 border border-secondary border-opacity-25 small font-monospace" style="background: var(--dc-bg-surface); max-width: 480px;">
            <div class="fw-bold text-warning mb-1"><i class="bi bi-key-fill me-1"></i> Test Credentials for Evaluation:</div>
            <div class="text-secondary">Admin: <code>admin@devconnect.io</code> | <code>Admin@12345</code></div>
            <div class="text-secondary">User: &nbsp;<code>user@devconnect.io</code> &nbsp;| <code>User@12345</code></div>
          </div>
        <?php endif; ?>
      </div>

      <!-- Right Column: Interactive Architecture Box -->
      <div class="col-lg-6">
        <div class="dc-card shadow-lg">
          <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom border-secondary border-opacity-25">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-server text-info fs-4"></i>
              <span class="fw-bold text-light">PHP 8 &amp; MySQL Engine</span>
            </div>
            <span class="badge bg-success-subtle text-success border border-success-subtle">LIVE DATABASE</span>
          </div>

          <div class="row g-3">
            <div class="col-sm-6">
              <div class="p-3 rounded-3" style="background: var(--dc-bg-input); border: 1px solid var(--dc-border);">
                <div class="text-info fs-4 mb-1"><i class="bi bi-shield-check"></i></div>
                <div class="fw-bold text-light small">Prepared Statements</div>
                <div class="text-secondary" style="font-size: 0.75rem;">100% parameter-bound queries via mysqli to eliminate SQL injection.</div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="p-3 rounded-3" style="background: var(--dc-bg-input); border: 1px solid var(--dc-border);">
                <div class="text-warning fs-4 mb-1"><i class="bi bi-lock-fill"></i></div>
                <div class="fw-bold text-light small">Bcrypt Password Hashing</div>
                <div class="text-secondary" style="font-size: 0.75rem;">Secure salting and hashing using PHP password_hash() and password_verify().</div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="p-3 rounded-3" style="background: var(--dc-bg-input); border: 1px solid var(--dc-border);">
                <div class="text-primary fs-4 mb-1"><i class="bi bi-person-gear"></i></div>
                <div class="fw-bold text-light small">Role-Based Access (RBAC)</div>
                <div class="text-secondary" style="font-size: 0.75rem;">Strict server-side route guards separating Admin and Standard User actions.</div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="p-3 rounded-3" style="background: var(--dc-bg-input); border: 1px solid var(--dc-border);">
                <div class="text-success fs-4 mb-1"><i class="bi bi-cloud-arrow-up-fill"></i></div>
                <div class="fw-bold text-light small">Secure Image Uploads</div>
                <div class="text-secondary" style="font-size: 0.75rem;">MIME-type verification, extension whitelisting, and randomized filenames.</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Features Section -->
<section id="features" class="py-5 border-top border-secondary border-opacity-10" style="background: var(--dc-bg-surface);">
  <div class="container py-4">
    <div class="text-center max-w-700 mx-auto mb-5">
      <span class="badge bg-info-subtle text-info border border-info-subtle mb-2">FULL STACK CAPABILITIES</span>
      <h2 class="h2 fw-bold mb-3">Complete CRUD &amp; User Management</h2>
      <p class="text-secondary small">
        Demonstrating all essential backend operations required by the ApexPlanet Task 3 internship syllabus.
      </p>
    </div>

    <div class="row g-4">
      <div class="col-md-3 col-sm-6">
        <div class="dc-card dc-card-hover h-100 text-center p-4">
          <div class="mb-3 text-info fs-1"><i class="bi bi-file-earmark-plus"></i></div>
          <h3 class="h5 fw-bold text-light mb-2">CREATE</h3>
          <p class="text-secondary small mb-0">Self-registration with duplicate email prevention and admin user creation form.</p>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="dc-card dc-card-hover h-100 text-center p-4">
          <div class="mb-3 text-primary fs-1"><i class="bi bi-table"></i></div>
          <h3 class="h5 fw-bold text-light mb-2">READ</h3>
          <p class="text-secondary small mb-0">User dashboards, detailed profile cards, and searchable admin user data tables.</p>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="dc-card dc-card-hover h-100 text-center p-4">
          <div class="mb-3 text-warning fs-1"><i class="bi bi-pencil-square"></i></div>
          <h3 class="h5 fw-bold text-light mb-2">UPDATE</h3>
          <p class="text-secondary small mb-0">Profile modification, password changes, avatar uploads, and admin role/status edits.</p>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="dc-card dc-card-hover h-100 text-center p-4">
          <div class="mb-3 text-danger fs-1"><i class="bi bi-trash3"></i></div>
          <h3 class="h5 fw-bold text-light mb-2">DELETE</h3>
          <p class="text-secondary small mb-0">Secure POST-only user deletion with confirmation and admin self-deletion safeguards.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- About Section -->
<section id="about" class="py-5">
  <div class="container py-4">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <h2 class="h2 fw-bold text-light mb-3">Built for ApexPlanet Full Stack Track</h2>
        <p class="text-secondary mb-3">
          This portal fulfills all core requirements for Task 3 of the ApexPlanet 60-Day Internship. It moves beyond static interfaces to deliver a resilient backend powered by native PHP 8 and relational MySQL.
        </p>
        <p class="text-secondary mb-4">
          Every query leverages <code>mysqli</code> prepared statements, session fixation defenses, and cryptographic password verification.
        </p>
        <div class="d-flex gap-2">
          <a href="<?php echo BASE_URL; ?>/register.php" class="btn btn-dc-primary btn-sm">Join Portal</a>
          <a href="<?php echo BASE_URL; ?>/login.php" class="btn btn-dc-outline btn-sm">Member Login</a>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="p-4 rounded-3 border border-secondary border-opacity-25" style="background: var(--dc-bg-card);">
          <h3 class="h5 fw-bold text-light mb-3"><i class="bi bi-check2-all text-success me-2"></i> Verified Security Checklist</h3>
          <ul class="list-unstyled text-secondary small mb-0">
            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> Zero SQL string concatenation — 100% prepared statements</li>
            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> Strong password hashing via <code>password_hash()</code></li>
            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> Session regeneration upon login to thwart fixation</li>
            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> CSRF token protection on all state-changing POST forms</li>
            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> HTML output escaping with <code>htmlspecialchars()</code></li>
            <li><i class="bi bi-check-circle text-success me-2"></i> Server-side MIME &amp; extension validation for avatar uploads</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
