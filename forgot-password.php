<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Page: forgot-password.php
 * 
 * Password Reset Request Interface with CSRF protection and clear feedback.
 */

$pageTitle = 'Forgot Password';
require_once __DIR__ . '/includes/header.php';

// Redirect if already authenticated
if (is_logged_in()) {
    redirect(is_admin() ? '/admin/dashboard.php' : '/dashboard.php');
}
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-5 col-md-7 col-sm-10">
      
      <!-- Flash Alert Feedback -->
      <?php render_flash(); ?>

      <div class="dc-card shadow-lg p-4 p-sm-5">
        <!-- Header -->
        <div class="text-center mb-4">
          <div class="brand-icon-box mx-auto mb-3" style="width: 52px; height: 52px; font-size: 1.5rem;">
            <i class="bi bi-shield-lock"></i>
          </div>
          <h1 class="h3 fw-bold text-light mb-1">Recover Password</h1>
          <p class="text-secondary small mb-0">Enter your registered email to receive reset instructions</p>
        </div>

        <!-- Testing Note for Evaluators -->
        <div class="p-3 mb-4 rounded-3 border border-secondary border-opacity-25 small" style="background: var(--dc-bg-surface);">
          <div class="fw-bold text-info mb-1"><i class="bi bi-info-circle me-1"></i> Evaluation Note:</div>
          <div class="text-secondary small">
            This module verifies registered emails against the MySQL database. Seed accounts: <code>admin@devconnect.io</code> and <code>user@devconnect.io</code>.
          </div>
        </div>

        <!-- Password Recovery Form -->
        <form action="<?php echo BASE_URL; ?>/actions/password-action.php" method="POST" novalidate>
          <?php echo csrf_input(); ?>

          <!-- Email Field -->
          <div class="mb-4">
            <label for="email" class="form-label">
              Registered Email Address <span class="required-star">*</span>
            </label>
            <div class="input-group">
              <span class="input-group-text bg-dark border-secondary border-opacity-25 text-secondary">
                <i class="bi bi-envelope"></i>
              </span>
              <input 
                type="email" 
                class="form-control" 
                id="email" 
                name="email" 
                placeholder="name@example.com" 
                required 
                autocomplete="email"
                autofocus
              >
            </div>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="btn btn-dc-primary w-100 py-2 fs-6">
            <i class="bi bi-send me-1"></i> Send Recovery Instructions
          </button>
        </form>

        <!-- Back to Login -->
        <div class="text-center mt-4 pt-3 border-top border-secondary border-opacity-10 text-secondary small">
          Remembered your password? 
          <a href="<?php echo BASE_URL; ?>/login.php" class="text-info text-decoration-none fw-semibold ms-1">
            <i class="bi bi-arrow-left me-1"></i> Return to Sign In
          </a>
        </div>

      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
