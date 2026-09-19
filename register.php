<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Page: register.php
 * 
 * User Registration Interface with server & client validation,
 * password strength indicator, confirmation matcher, and demographic inputs.
 */

$pageTitle = 'Create Account';
require_once __DIR__ . '/includes/header.php';

// Redirect if already authenticated
if (is_logged_in()) {
    redirect(is_admin() ? '/admin/dashboard.php' : '/dashboard.php');
}

// Retrieve old form inputs if validation failed
$old = $_SESSION['old_input'] ?? [];
$oldFullName = $old['full_name'] ?? '';
$oldEmail    = $old['email'] ?? '';
$oldDob      = $old['dob'] ?? '';
$oldGender   = $old['gender'] ?? 'prefer-not-to-say';
$oldCountry  = $old['country'] ?? '';
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-6 col-md-8 col-sm-11">
      
      <!-- Flash Alert Feedback -->
      <?php render_flash(); ?>

      <div class="dc-card shadow-lg p-4 p-sm-5">
        <!-- Header -->
        <div class="text-center mb-4">
          <div class="brand-icon-box mx-auto mb-3" style="width: 52px; height: 52px; font-size: 1.5rem;">
            <i class="bi bi-person-plus-fill"></i>
          </div>
          <h1 class="h3 fw-bold text-light mb-1">Create an Account</h1>
          <p class="text-secondary small mb-0">Join the DevConnect engineering developer community</p>
        </div>

        <!-- Registration Form -->
        <form action="<?php echo BASE_URL; ?>/actions/register-action.php" method="POST" novalidate>
          <?php echo csrf_input(); ?>

          <!-- Full Name -->
          <div class="mb-3">
            <label for="full_name" class="form-label">
              Full Name <span class="required-star">*</span>
            </label>
            <div class="input-group">
              <span class="input-group-text bg-dark border-secondary border-opacity-25 text-secondary">
                <i class="bi bi-person"></i>
              </span>
              <input 
                type="text" 
                class="form-control" 
                id="full_name" 
                name="full_name" 
                value="<?php echo e($oldFullName); ?>"
                placeholder="e.g. Alex Mercer" 
                required 
                autocomplete="name"
                autofocus
              >
            </div>
          </div>

          <!-- Email Address -->
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
                value="<?php echo e($oldEmail); ?>"
                placeholder="name@example.com" 
                required 
                autocomplete="email"
              >
            </div>
            <div class="form-text text-secondary small">We will never share your email with third parties.</div>
          </div>

          <!-- Password & Strength Meter -->
          <div class="mb-3">
            <label for="password" class="form-label">
              Password <span class="required-star">*</span>
            </label>
            <div class="input-group">
              <span class="input-group-text bg-dark border-secondary border-opacity-25 text-secondary">
                <i class="bi bi-lock"></i>
              </span>
              <input 
                type="password" 
                class="form-control" 
                id="password" 
                name="password" 
                placeholder="Minimum 8 characters" 
                required 
                autocomplete="new-password"
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
            
            <!-- Password Strength Bar & Label -->
            <div class="mt-2">
              <div class="d-flex justify-content-between align-items-center mb-1 small">
                <span class="text-secondary">Password Strength:</span>
                <span id="strength-label" class="fw-semibold text-secondary">None</span>
              </div>
              <div class="strength-meter-bar">
                <div id="strength-bar-fill" class="strength-meter-fill"></div>
              </div>
            </div>
          </div>

          <!-- Confirm Password -->
          <div class="mb-3">
            <label for="confirm_password" class="form-label">
              Confirm Password <span class="required-star">*</span>
            </label>
            <div class="input-group">
              <span class="input-group-text bg-dark border-secondary border-opacity-25 text-secondary">
                <i class="bi bi-shield-check"></i>
              </span>
              <input 
                type="password" 
                class="form-control" 
                id="confirm_password" 
                name="confirm_password" 
                placeholder="Re-enter password" 
                required 
                autocomplete="new-password"
              >
              <button 
                type="button" 
                class="btn password-toggle-btn" 
                data-target="confirm_password" 
                aria-label="Toggle password visibility"
              >
                <i class="bi bi-eye"></i>
              </button>
            </div>
            <!-- Match Indicator -->
            <div id="password-match-status" class="password-match-indicator"></div>
          </div>

          <!-- Date of Birth & Gender Row -->
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label for="dob" class="form-label">Date of Birth</label>
              <input 
                type="date" 
                class="form-control" 
                id="dob" 
                name="dob" 
                value="<?php echo e($oldDob); ?>"
                max="<?php echo date('Y-m-d'); ?>"
              >
            </div>
            <div class="col-md-6">
              <label for="gender" class="form-label">Gender</label>
              <select class="form-select" id="gender" name="gender">
                <option value="prefer-not-to-say" <?php echo ($oldGender === 'prefer-not-to-say') ? 'selected' : ''; ?>>Prefer not to say</option>
                <option value="male" <?php echo ($oldGender === 'male') ? 'selected' : ''; ?>>Male</option>
                <option value="female" <?php echo ($oldGender === 'female') ? 'selected' : ''; ?>>Female</option>
                <option value="non-binary" <?php echo ($oldGender === 'non-binary') ? 'selected' : ''; ?>>Non-binary</option>
              </select>
            </div>
          </div>

          <!-- Country Selection -->
          <div class="mb-3">
            <label for="country" class="form-label">Country / Region</label>
            <input 
              type="text" 
              class="form-control" 
              id="country" 
              name="country" 
              value="<?php echo e($oldCountry); ?>"
              placeholder="e.g. India, United States, Germany..."
            >
          </div>

          <!-- Terms Checkbox -->
          <div class="mb-4 form-check">
            <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
            <label class="form-check-label text-secondary small" for="terms">
              I agree to the <a href="#" class="text-info text-decoration-none">Terms of Service</a> and <a href="#" class="text-info text-decoration-none">Privacy Policy</a> <span class="required-star">*</span>
            </label>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="btn btn-dc-primary w-100 py-2 fs-6">
            <i class="bi bi-person-check me-1"></i> Complete Registration
          </button>
        </form>

        <!-- Footer Link -->
        <div class="text-center mt-4 pt-3 border-top border-secondary border-opacity-10 text-secondary small">
          Already have an account? 
          <a href="<?php echo BASE_URL; ?>/login.php" class="text-info text-decoration-none fw-semibold ms-1">
            Sign In here
          </a>
        </div>

      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
