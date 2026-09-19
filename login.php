<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Page: login.php
 * 
 * User Login Interface with CSRF protection, flash feedback, and credential assistance.
 */

$pageTitle = 'Sign In';
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
            <i class="bi bi-box-arrow-in-right"></i>
          </div>
          <h1 class="h3 fw-bold text-light mb-1">Welcome Back</h1>
          <p class="text-secondary small mb-0">Sign in to your DevConnect account</p>
        </div>

        <!-- Testing Credentials Helper for Internship Evaluators -->
        <div class="p-3 mb-4 rounded-3 border border-secondary border-opacity-25 small font-monospace" style="background: var(--dc-bg-surface);">
          <div class="fw-bold text-warning mb-1"><i class="bi bi-key-fill me-1"></i> Demo Evaluation Accounts:</div>
          <div class="text-secondary small">
            Admin: <code>admin@devconnect.io</code> | <code>Admin@12345</code><br>
            User: &nbsp;<code>user@devconnect.io</code> &nbsp;| <code>User@12345</code>
          </div>
        </div>

        <!-- Login Form -->
        <form action="<?php echo BASE_URL; ?>/actions/login-action.php" method="POST" novalidate>
          <?php echo csrf_input(); ?>

          <!-- Email Field -->
          <div class="mb-3">
            <label for="email" class="form-label">
              Email Address <span class="required-star">*</span>
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

          <!-- Password Field -->
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="password" class="form-label mb-0">
                Password <span class="required-star">*</span>
              </label>
              <a href="<?php echo BASE_URL; ?>/forgot-password.php" class="text-decoration-none small text-info">
                Forgot password?
              </a>
            </div>
            <div class="input-group">
              <span class="input-group-text bg-dark border-secondary border-opacity-25 text-secondary">
                <i class="bi bi-key"></i>
              </span>
              <input 
                type="password" 
                class="form-control" 
                id="password" 
                name="password" 
                placeholder="••••••••" 
                required 
                autocomplete="current-password"
              >
              <!-- Show/Hide Password Toggle -->
              <button 
                type="button" 
                class="btn password-toggle-btn" 
                data-target="password" 
                aria-label="Toggle password visibility"
              >
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          <!-- Remember Me Checkbox -->
          <div class="mb-4 form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember">
            <label class="form-check-label text-secondary small" for="remember">
              Remember me on this workstation
            </label>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="btn btn-dc-primary w-100 py-2 fs-6">
            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Account
          </button>
        </form>

        <!-- Footer Link -->
        <div class="text-center mt-4 pt-3 border-top border-secondary border-opacity-10 text-secondary small">
          New to DevConnect? 
          <a href="<?php echo BASE_URL; ?>/register.php" class="text-info text-decoration-none fw-semibold ms-1">
            Create an account
          </a>
        </div>

      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
