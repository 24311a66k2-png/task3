<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Action: admin/delete-user.php
 * 
 * Secure User Deletion Handler with CSRF verification, self-deletion prevention,
 * avatar file cleanup, and prepared DELETE statement execution.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/admin-auth.php'; // Guard: admin only

// 1. Strict POST Request Enforcement
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/users.php');
}

// 2. CSRF Token Verification
if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    set_flash('danger', 'Security validation failed (Invalid or expired CSRF token). Deletion aborted.');
    redirect('/admin/users.php');
}

$targetId = (int)($_POST['id'] ?? 0);
$currentAdminId = (int)current_user_id();

// 3. Prevent Self-Account Deletion
if ($targetId <= 0 || $targetId === $currentAdminId) {
    set_flash('danger', 'Security Policy Violation: You cannot delete your own active administrator account.');
    redirect('/admin/users.php');
}

$conn = get_db_connection();

// 4. Locate Target User Record & Avatar
$findStmt = $conn->prepare("SELECT id, full_name, profile_image FROM users WHERE id = ? LIMIT 1");
$findStmt->bind_param("i", $targetId);
$findStmt->execute();
$userToDelete = $findStmt->get_result()->fetch_assoc();
$findStmt->close();

if (!$userToDelete) {
    set_flash('warning', 'The user account you attempted to delete does not exist.');
    redirect('/admin/users.php');
}

// 5. Delete Uploaded Profile Avatar File if Custom
$avatar = $userToDelete['profile_image'];
if ($avatar && $avatar !== 'default-avatar.svg' && $avatar !== 'default-avatar.png') {
    $avatarPath = UPLOAD_DIR . $avatar;
    if (file_exists($avatarPath)) {
        @unlink($avatarPath);
    }
}

// 6. Execute Prepared DELETE Statement
$delStmt = $conn->prepare("DELETE FROM users WHERE id = ? LIMIT 1");
$delStmt->bind_param("i", $targetId);

if ($delStmt->execute()) {
    $delStmt->close();
    set_flash('success', "User account <strong>" . e($userToDelete['full_name']) . "</strong> (ID #$targetId) has been permanently removed.");
} else {
    $delStmt->close();
    set_flash('danger', 'Failed to remove user account due to a database constraint error.');
}

redirect('/admin/users.php');
