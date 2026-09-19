<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Page: admin/create-user.php
 * 
 * Admin User Creation Interface with role assignment, status configuration,
 * duplicate prevention, and prepared statement insertion.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/admin-auth.php'; // Guard: admin only

$conn = get_db_connection();
$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Verify CSRF Token
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Security token invalid or expired. Please submit again.';
    }

    // 2. Retrieve & Sanitize Inputs
    $fullName = sanitize_input($_POST['full_name'] ?? '');
    $email    = strtolower(sanitize_input($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $role     = sanitize_input($_POST['role'] ?? 'user');
    $status   = sanitize_input($_POST['status'] ?? 'active');
    $dob      = sanitize_input($_POST['dob'] ?? '');
    $gender   = sanitize_input($_POST['gender'] ?? 'prefer-not-to-say');
    $country  = sanitize_input($_POST['country'] ?? '');
    $bio      = sanitize_input($_POST['bio'] ?? '');

    $old = [
        'full_name' => $fullName,
        'email'     => $email,
        'role'      => $role,
        'status'    => $status,
        'dob'       => $dob,
        'gender'    => $gender,
        'country'   => $country,
        'bio'       => $bio
    ];

    // 3. Validation
    if (empty($fullName) || mb_strlen($fullName) < 2) {
        $errors[] = 'Full name must be at least 2 characters.';
    }

    if (empty($email) || !is_valid_email($email)) {
        $errors[] = 'A valid email address is required.';
    }

    if (empty($password) || mb_strlen($password) < 8) {
        $errors[] = 'Initial password must be at least 8 characters long.';
    }

    if (!in_array($role, ['user', 'admin'], true)) {
        $role = 'user';
    }

    if (!in_array($status, ['active', 'inactive', 'suspended'], true)) {
        $status = 'active';
    }

    if (!empty($dob)) {
        $dobDate = DateTime::createFromFormat('Y-m-d', $dob);
        if (!$dobDate || $dobDate > new DateTime()) {
            $errors[] = 'Date of birth must be a valid past date.';
        }
    }

    // 4. Check for duplicate email
    if (empty($errors)) {
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            $errors[] = 'An account with this email address already exists in the system.';
        }
        $checkStmt->close();
    }

    // 5. Insert Record if Validation Passes
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $dobVal = !empty($dob) ? $dob : null;
        $countryVal = !empty($country) ? $country : null;
        $bioVal = !empty($bio) ? $bio : null;

        $insertStmt = $conn->prepare("INSERT INTO users (full_name, email, password, role, status, date_of_birth, gender, country, bio) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("sssssssss", $fullName, $email, $hashedPassword, $role, $status, $dobVal, $gender, $countryVal, $bioVal);

        if ($insertStmt->execute()) {
            $insertStmt->close();
            set_flash('success', "User account for <strong>" . e($fullName) . "</strong> has been created successfully.");
            redirect('/admin/users.php');
        } else {
            $insertStmt->close();
            $errors[] = 'Database insertion error. Please try again.';
        }
    }
}

$pageTitle = 'Create User — Admin Portal';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
      
      <!-- Flash Alert Feedback -->
      <?php render_flash(); ?>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger shadow-sm mb-4">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>
          <strong>Please address the following errors:</strong>
          <ul class="mb-0 mt-2 ps-3">
            <?php foreach ($errors as $err): ?>
              <li><?php echo e($err); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="dc-card shadow-lg p-4 p-md-5">
        <!-- Form Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25">
          <div>
            <span class="badge bg-warning text-dark fw-bold mb-1">ADMINISTRATOR</span>
            <h1 class="h3 fw-bold text-light mb-1">Create New User Account</h1>
            <p class="text-secondary small mb-0">Register and configure user accounts directly with administrative rights</p>
          </div>
          <a href="<?php echo BASE_URL; ?>/admin/users.php" class="btn btn-dc-outline btn-sm">
            <i class="bi bi-arrow-left me-1"></i> User List
          </a>
        </div>

        <!-- Create Form -->
        <form method="POST" action="<?php echo BASE_URL; ?>/admin/create-user.php" novalidate>
          <?php echo csrf_input(); ?>

          <h2 class="h6 fw-bold text-light mb-3 text-uppercase tracking-wider">
            <i class="bi bi-shield-lock me-1 text-warning"></i> Account Credentials & Access
          </h2>

          <div class="row g-3 mb-4">
            <!-- Full Name -->
            <div class="col-md-6">
              <label for="full_name" class="form-label">Full Name <span class="required-star">*</span></label>
              <input 
                type="text" 
                class="form-control" 
                id="full_name" 
                name="full_name" 
                value="<?php echo e($old['full_name'] ?? ''); ?>" 
                placeholder="e.g. Rachel Adams" 
                required
              >
            </div>

            <!-- Email -->
            <div class="col-md-6">
              <label for="email" class="form-label">Email Address <span class="required-star">*</span></label>
              <input 
                type="email" 
                class="form-control" 
                id="email" 
                name="email" 
                value="<?php echo e($old['email'] ?? ''); ?>" 
                placeholder="user@example.com" 
                required
              >
            </div>

            <!-- Initial Password -->
            <div class="col-md-4">
              <label for="password" class="form-label">Temporary Password <span class="required-star">*</span></label>
              <div class="input-group">
                <input 
                  type="password" 
                  class="form-control" 
                  id="password" 
                  name="password" 
                  placeholder="Min 8 characters" 
                  required
                >
                <button type="button" class="btn password-toggle-btn" data-target="password" aria-label="Toggle password visibility">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>

            <!-- Role Assignment -->
            <div class="col-md-4">
              <label for="role" class="form-label">System Role <span class="required-star">*</span></label>
              <select class="form-select" id="role" name="role" required>
                <option value="user" <?php echo (($old['role'] ?? 'user') === 'user') ? 'selected' : ''; ?>>Standard User</option>
                <option value="admin" <?php echo (($old['role'] ?? '') === 'admin') ? 'selected' : ''; ?>>Administrator</option>
              </select>
            </div>

            <!-- Account Status -->
            <div class="col-md-4">
              <label for="status" class="form-label">Account Status <span class="required-star">*</span></label>
              <select class="form-select" id="status" name="status" required>
                <option value="active" <?php echo (($old['status'] ?? 'active') === 'active') ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo (($old['status'] ?? '') === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                <option value="suspended" <?php echo (($old['status'] ?? '') === 'suspended') ? 'selected' : ''; ?>>Suspended</option>
              </select>
            </div>
          </div>

          <h2 class="h6 fw-bold text-light mb-3 text-uppercase tracking-wider pt-3 border-top border-secondary border-opacity-25">
            <i class="bi bi-person-lines-fill me-1 text-primary"></i> Demographic Details (Optional)
          </h2>

          <div class="row g-3 mb-4">
            <!-- Date of Birth -->
            <div class="col-md-4">
              <label for="dob" class="form-label">Date of Birth</label>
              <input 
                type="date" 
                class="form-control" 
                id="dob" 
                name="dob" 
                value="<?php echo e($old['dob'] ?? ''); ?>" 
                max="<?php echo date('Y-m-d'); ?>"
              >
            </div>

            <!-- Gender -->
            <div class="col-md-4">
              <label for="gender" class="form-label">Gender</label>
              <select class="form-select" id="gender" name="gender">
                <option value="prefer-not-to-say" <?php echo (($old['gender'] ?? 'prefer-not-to-say') === 'prefer-not-to-say') ? 'selected' : ''; ?>>Prefer not to say</option>
                <option value="male" <?php echo (($old['gender'] ?? '') === 'male') ? 'selected' : ''; ?>>Male</option>
                <option value="female" <?php echo (($old['gender'] ?? '') === 'female') ? 'selected' : ''; ?>>Female</option>
                <option value="non-binary" <?php echo (($old['gender'] ?? '') === 'non-binary') ? 'selected' : ''; ?>>Non-binary</option>
              </select>
            </div>

            <!-- Country -->
            <div class="col-md-4">
              <label for="country" class="form-label">Country / Region</label>
              <input 
                type="text" 
                class="form-control" 
                id="country" 
                name="country" 
                value="<?php echo e($old['country'] ?? ''); ?>" 
                placeholder="e.g. India"
              >
            </div>

            <!-- Bio -->
            <div class="col-12">
              <label for="bio" class="form-label">User Biography</label>
              <textarea 
                class="form-control" 
                id="bio" 
                name="bio" 
                rows="3" 
                placeholder="Brief administrative notes or user introduction..."
              ><?php echo e($old['bio'] ?? ''); ?></textarea>
            </div>
          </div>

          <!-- Form Buttons -->
          <div class="d-flex justify-content-end gap-2 pt-3 border-top border-secondary border-opacity-25">
            <a href="<?php echo BASE_URL; ?>/admin/users.php" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-dc-primary px-4">
              <i class="bi bi-person-check-fill me-1"></i> Save & Create Account
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
