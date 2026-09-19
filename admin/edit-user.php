<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Page: admin/edit-user.php
 * 
 * Admin User Editing Interface allowing updates to user role, status,
 * credentials, personal details, and administrative password resets.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/admin-auth.php'; // Guard: admin only

$conn = get_db_connection();
$currentAdminId = current_user_id();

// 1. Identify Target User ID
$targetId = (int)($_GET['id'] ?? ($_POST['id'] ?? 0));
if ($targetId <= 0) {
    set_flash('danger', 'Invalid user identifier specified.');
    redirect('/admin/users.php');
}

// 2. Fetch Existing Target User Record
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $targetId);
$stmt->execute();
$targetUser = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$targetUser) {
    set_flash('danger', 'The specified user record was not found in the database.');
    redirect('/admin/users.php');
}

$isSelf = ($targetId === (int)$currentAdminId);
$errors = [];

// 3. Process Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Security token invalid or expired. Please submit again.';
    }

    $fullName    = sanitize_input($_POST['full_name'] ?? '');
    $email       = strtolower(sanitize_input($_POST['email'] ?? ''));
    $role        = sanitize_input($_POST['role'] ?? $targetUser['role']);
    $status      = sanitize_input($_POST['status'] ?? $targetUser['status']);
    $dob         = sanitize_input($_POST['dob'] ?? '');
    $gender      = sanitize_input($_POST['gender'] ?? 'prefer-not-to-say');
    $country     = sanitize_input($_POST['country'] ?? '');
    $bio         = sanitize_input($_POST['bio'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';

    // Validation
    if (empty($fullName) || mb_strlen($fullName) < 2) {
        $errors[] = 'Full name must be at least 2 characters.';
    }

    if (empty($email) || !is_valid_email($email)) {
        $errors[] = 'A valid email address is required.';
    }

    if (!in_array($role, ['user', 'admin'], true)) {
        $role = $targetUser['role'];
    }

    if (!in_array($status, ['active', 'inactive', 'suspended'], true)) {
        $status = $targetUser['status'];
    }

    // Protection: Prevent Admin from locking out their own active session
    if ($isSelf && $role !== 'admin') {
        $errors[] = 'Security policy: You cannot revoke your own administrator privileges.';
        $role = 'admin';
    }

    if ($isSelf && $status !== 'active') {
        $errors[] = 'Security policy: You cannot deactivate or suspend your own active administrator account.';
        $status = 'active';
    }

    if (!empty($newPassword) && mb_strlen($newPassword) < 8) {
        $errors[] = 'If setting a new password, it must be at least 8 characters long.';
    }

    if (!empty($dob)) {
        $dobDate = DateTime::createFromFormat('Y-m-d', $dob);
        if (!$dobDate || $dobDate > new DateTime()) {
            $errors[] = 'Date of birth must be a valid past date.';
        }
    }

    // Duplicate email check (excluding current record)
    if (empty($errors)) {
        $dupStmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
        $dupStmt->bind_param("si", $email, $targetId);
        $dupStmt->execute();
        if ($dupStmt->get_result()->num_rows > 0) {
            $errors[] = 'Another account is already registered with this email address.';
        }
        $dupStmt->close();
    }

    // Execute UPDATE if valid
    if (empty($errors)) {
        $dobVal = !empty($dob) ? $dob : null;
        $countryVal = !empty($country) ? $country : null;
        $bioVal = !empty($bio) ? $bio : null;

        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateStmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, password = ?, role = ?, status = ?, date_of_birth = ?, gender = ?, country = ?, bio = ? WHERE id = ?");
            $updateStmt->bind_param("sssssssssi", $fullName, $email, $hashedPassword, $role, $status, $dobVal, $gender, $countryVal, $bioVal, $targetId);
        } else {
            $updateStmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, role = ?, status = ?, date_of_birth = ?, gender = ?, country = ?, bio = ? WHERE id = ?");
            $updateStmt->bind_param("ssssssssi", $fullName, $email, $role, $status, $dobVal, $gender, $countryVal, $bioVal, $targetId);
        }

        if ($updateStmt->execute()) {
            $updateStmt->close();

            // If updating self, synchronize session
            if ($isSelf) {
                $_SESSION['full_name'] = $fullName;
                $_SESSION['email']     = $email;
            }

            set_flash('success', "User account <strong>" . e($fullName) . "</strong> (ID #$targetId) has been updated successfully.");
            redirect('/admin/users.php');
        } else {
            $updateStmt->close();
            $errors[] = 'Database update error. Please try again.';
        }
    }
}

$pageTitle = 'Edit User #' . $targetId . ' — Admin Portal';
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
          <strong>Please address the following issues:</strong>
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
            <div class="d-flex align-items-center gap-2 mb-1">
              <span class="badge bg-warning text-dark fw-bold">ADMIN EDIT</span>
              <span class="text-secondary small font-monospace">User ID #<?php echo str_pad($targetUser['id'], 4, '0', STR_PAD_LEFT); ?></span>
              <?php if ($isSelf): ?>
                <span class="badge bg-info text-dark">Your Account</span>
              <?php endif; ?>
            </div>
            <h1 class="h3 fw-bold text-light mb-1">Edit User Profile</h1>
            <p class="text-secondary small mb-0">Modify permissions, status, and credentials for <?php echo e($targetUser['full_name']); ?></p>
          </div>
          <a href="<?php echo BASE_URL; ?>/admin/users.php" class="btn btn-dc-outline btn-sm">
            <i class="bi bi-arrow-left me-1"></i> User List
          </a>
        </div>

        <!-- Edit Form -->
        <form method="POST" action="<?php echo BASE_URL; ?>/admin/edit-user.php?id=<?php echo $targetId; ?>" novalidate>
          <?php echo csrf_input(); ?>
          <input type="hidden" name="id" value="<?php echo $targetId; ?>">

          <!-- Account & Role Configuration -->
          <h2 class="h6 fw-bold text-light mb-3 text-uppercase tracking-wider">
            <i class="bi bi-shield-check me-1 text-warning"></i> Access Control & Role
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
                value="<?php echo e($_POST['full_name'] ?? $targetUser['full_name']); ?>" 
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
                value="<?php echo e($_POST['email'] ?? $targetUser['email']); ?>" 
                required
              >
            </div>

            <!-- Role Selection -->
            <div class="col-md-6">
              <label for="role" class="form-label">System Role <span class="required-star">*</span></label>
              <select class="form-select" id="role" name="role" <?php echo $isSelf ? 'disabled' : ''; ?>>
                <option value="user" <?php echo (($targetUser['role'] === 'user')) ? 'selected' : ''; ?>>Standard User</option>
                <option value="admin" <?php echo (($targetUser['role'] === 'admin')) ? 'selected' : ''; ?>>Administrator</option>
              </select>
              <?php if ($isSelf): ?>
                <input type="hidden" name="role" value="admin">
                <div class="form-text text-warning small">You cannot demote your own account.</div>
              <?php endif; ?>
            </div>

            <!-- Status Selection -->
            <div class="col-md-6">
              <label for="status" class="form-label">Account Status <span class="required-star">*</span></label>
              <select class="form-select" id="status" name="status" <?php echo $isSelf ? 'disabled' : ''; ?>>
                <option value="active" <?php echo (($targetUser['status'] === 'active')) ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo (($targetUser['status'] === 'inactive')) ? 'selected' : ''; ?>>Inactive</option>
                <option value="suspended" <?php echo (($targetUser['status'] === 'suspended')) ? 'selected' : ''; ?>>Suspended</option>
              </select>
              <?php if ($isSelf): ?>
                <input type="hidden" name="status" value="active">
                <div class="form-text text-warning small">You cannot deactivate your own account.</div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Password Override -->
          <div class="p-3 mb-4 rounded-3 border border-secondary border-opacity-25" style="background: var(--dc-bg-surface);">
            <label for="new_password" class="form-label fw-semibold text-warning mb-1">
              <i class="bi bi-key-fill me-1"></i> Admin Password Reset (Optional)
            </label>
            <p class="text-secondary small mb-2">Leave blank to keep existing password unchanged.</p>
            <div class="input-group">
              <input 
                type="password" 
                class="form-control" 
                id="new_password" 
                name="new_password" 
                placeholder="Enter new password to override (min 8 chars)"
              >
              <button type="button" class="btn password-toggle-btn" data-target="new_password" aria-label="Toggle password visibility">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          <!-- Demographics -->
          <h2 class="h6 fw-bold text-light mb-3 text-uppercase tracking-wider pt-2 border-top border-secondary border-opacity-25">
            <i class="bi bi-person-lines-fill me-1 text-primary"></i> Demographic Details
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
                value="<?php echo e($_POST['dob'] ?? ($targetUser['date_of_birth'] ?? '')); ?>" 
                max="<?php echo date('Y-m-d'); ?>"
              >
            </div>

            <!-- Gender -->
            <div class="col-md-4">
              <label for="gender" class="form-label">Gender</label>
              <?php $curGender = $_POST['gender'] ?? ($targetUser['gender'] ?? 'prefer-not-to-say'); ?>
              <select class="form-select" id="gender" name="gender">
                <option value="prefer-not-to-say" <?php echo ($curGender === 'prefer-not-to-say') ? 'selected' : ''; ?>>Prefer not to say</option>
                <option value="male" <?php echo ($curGender === 'male') ? 'selected' : ''; ?>>Male</option>
                <option value="female" <?php echo ($curGender === 'female') ? 'selected' : ''; ?>>Female</option>
                <option value="non-binary" <?php echo ($curGender === 'non-binary') ? 'selected' : ''; ?>>Non-binary</option>
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
                value="<?php echo e($_POST['country'] ?? ($targetUser['country'] ?? '')); ?>" 
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
              ><?php echo e($_POST['bio'] ?? ($targetUser['bio'] ?? '')); ?></textarea>
            </div>
          </div>

          <!-- Timestamps Information -->
          <div class="p-3 mb-4 rounded-3 bg-dark bg-opacity-50 border border-secondary border-opacity-10 small text-secondary">
            <div class="row">
              <div class="col-sm-6">
                <strong>Registered At:</strong> <?php echo date('Y-m-d H:i:s', strtotime($targetUser['created_at'])); ?>
              </div>
              <div class="col-sm-6">
                <strong>Last Updated:</strong> <?php echo date('Y-m-d H:i:s', strtotime($targetUser['updated_at'])); ?>
              </div>
            </div>
          </div>

          <!-- Buttons -->
          <div class="d-flex justify-content-end gap-2 pt-3 border-top border-secondary border-opacity-25">
            <a href="<?php echo BASE_URL; ?>/admin/users.php" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-dc-primary px-4">
              <i class="bi bi-save me-1"></i> Save User Changes
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
