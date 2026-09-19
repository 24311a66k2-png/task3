<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Helper Functions: includes/functions.php
 */

require_once __DIR__ . '/../config/config.php';

/**
 * Safely escape string for HTML output (XSS Prevention)
 * @param mixed $string
 * @return string
 */
function e($string) {
    return htmlspecialchars((string)($string ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Set a session flash message
 * @param string $type ('success', 'danger', 'warning', 'info')
 * @param string $message
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message
    ];
}

/**
 * Retrieve and clear the session flash message
 * @return array|null
 */
function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Render flash message as a Bootstrap 5 dismissible alert
 */
function render_flash() {
    $flash = get_flash();
    if ($flash) {
        $type = e($flash['type']);
        $message = $flash['message']; // Can contain safe HTML if constructed internally
        $icon = match ($type) {
            'success' => 'bi-check-circle-fill',
            'danger'  => 'bi-exclamation-octagon-fill',
            'warning' => 'bi-exclamation-triangle-fill',
            default   => 'bi-info-circle-fill'
        };
        echo "
        <div class=\"alert alert-{$type} alert-dismissible fade show d-flex align-items-center gap-2\" role=\"alert\">
          <i class=\"bi {$icon} fs-5 flex-shrink-0\"></i>
          <div>{$message}</div>
          <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>
        </div>";
    }
}

/**
 * Check if a user is currently authenticated
 * @return bool
 */
function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

/**
 * Check if the currently authenticated user has the 'admin' role
 * @return bool
 */
function is_admin() {
    return is_logged_in() && (($_SESSION['role'] ?? '') === 'admin');
}

/**
 * Get current authenticated user ID
 * @return int|null
 */
function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Redirect safely using BASE_URL prefix
 * @param string $path (e.g. '/dashboard.php' or '/login.php')
 */
function redirect($path) {
    $url = BASE_URL . '/' . ltrim($path, '/');
    header("Location: {$url}");
    exit();
}

/**
 * Generate CSRF hidden input field
 * @return string
 */
function csrf_input() {
    $token = e($_SESSION['csrf_token'] ?? '');
    return "<input type=\"hidden\" name=\"csrf_token\" value=\"{$token}\">";
}

/**
 * Verify submitted CSRF token
 * @param string|null $token
 * @return bool
 */
function verify_csrf_token($token) {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitize basic string input
 * @param string $data
 * @return string
 */
function sanitize_input($data) {
    return trim(strip_tags((string)$data));
}

/**
 * Validate RFC email address format
 * @param string $email
 * @return bool
 */
function is_valid_email($email) {
    return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Return web URL for a user profile avatar
 * @param string|null $imageName
 * @return string
 */
function get_profile_image_url($imageName) {
    if (!empty($imageName) && file_exists(UPLOAD_DIR . $imageName)) {
        return UPLOAD_URL . $imageName;
    }
    return UPLOAD_URL . 'default-avatar.svg';
}

/**
 * Securely handle user profile avatar upload
 * @param array $file ($_FILES['profile_image'])
 * @param string &$error Output parameter for error message
 * @return string|false Filename on success, false on failure
 */
function handle_profile_upload($file, &$error = '') {
    // 1. Check upload error code
    if (!isset($file['error']) || is_array($file['error'])) {
        $error = 'Invalid file upload parameters.';
        return false;
    }

    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // No file uploaded, not an error
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'File upload failed with error code ' . $file['error'];
        return false;
    }

    // 2. Limit file size (2MB max)
    $maxSize = 2 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        $error = 'Profile image size exceeds the 2MB limit.';
        return false;
    }

    // 3. Verify file extension
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExt, $allowedExtensions, true)) {
        $error = 'Invalid image format. Allowed formats: JPG, PNG, WEBP.';
        return false;
    }

    // 4. Verify MIME type using finfo (prevents disguised executable files)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    $allowedMimes = [
        'image/jpeg',
        'image/png',
        'image/webp'
    ];

    if (!in_array($mimeType, $allowedMimes, true)) {
        $error = 'Uploaded file MIME type is not a recognized image.';
        return false;
    }

    // 5. Generate secure random filename
    $newFilename = 'avatar_' . bin2hex(random_bytes(16)) . '.' . $fileExt;
    $targetPath = UPLOAD_DIR . $newFilename;

    // Ensure uploads directory exists
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    // 6. Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        $error = 'Failed to save uploaded image on the server.';
        return false;
    }

    return $newFilename;
}
