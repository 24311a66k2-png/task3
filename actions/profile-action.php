<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Action: actions/profile-action.php
 * 
 * Handles profile information updates, password modifications, and secure avatar uploads.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/profile.php');
}

// 1. Verify CSRF Token
if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    set_flash('danger', 'Security token expired or invalid. Please try submitting again.');
    redirect('/edit-profile.php');
}

$userId = current_user_id();
$conn = get_db_connection();

// 2. Fetch Existing User Record
$userStmt = $conn->prepare("SELECT id, full_name, email, password, profile_image FROM users WHERE id = ? LIMIT 1");
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$currentUser = $userStmt->get_result()->fetch_assoc();
$userStmt->close();

if (!$currentUser) {
    set_flash('danger', 'User account not found.');
    redirect('/logout.php');
}

// 3. Sanitize Profile Inputs
$fullName = sanitize_input($_POST['full_name'] ?? '');
$bio      = sanitize_input($_POST['bio'] ?? '');
$dob      = sanitize_input($_POST['dob'] ?? '');
$gender   = sanitize_input($_POST['gender'] ?? 'prefer-not-to-say');
$country  = sanitize_input($_POST['country'] ?? '');

$errors = [];

if (empty($fullName) || mb_strlen($fullName) < 2) {
    $errors[] = 'Full name must be at least 2 characters long.';
}

if (!empty($dob)) {
    $dobDate = DateTime::createFromFormat('Y-m-d', $dob);
    if (!$dobDate || $dobDate > new DateTime()) {
        $errors[] = 'Date of birth must be a valid date in the past.';
    }
}

$validGenders = ['male', 'female', 'non-binary', 'prefer-not-to-say'];
if (!in_array($gender, $validGenders, true)) {
    $gender = 'prefer-not-to-say';
}

// 4. Handle Optional Password Change
$currentPassword = $_POST['current_password'] ?? '';
$newPassword     = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$updatePassword  = false;
$newPasswordHash = null;

if (!empty($currentPassword) || !empty($newPassword) || !empty($confirmPassword)) {
    if (empty($currentPassword)) {
        $errors[] = 'Please enter your current password to authorize a password change.';
    } elseif (!password_verify($currentPassword, $currentUser['password'])) {
        $errors[] = 'The current password provided is incorrect.';
    } elseif (empty($newPassword) || mb_strlen($newPassword) < 8) {
        $errors[] = 'New password must contain at least 8 characters.';
    } elseif ($newPassword !== $confirmPassword) {
        $errors[] = 'New password confirmation does not match.';
    } else {
        $updatePassword = true;
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    }
}

// 5. Handle Secure Profile Image Upload
$avatarFilename = $currentUser['profile_image'];
$uploadError = '';

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
    $newAvatar = handle_profile_upload($_FILES['profile_image'], $uploadError);
    if ($newAvatar === false) {
        $errors[] = $uploadError;
    } elseif ($newAvatar !== null) {
        // Delete old custom profile image if it was not the default SVG
        if ($avatarFilename && $avatarFilename !== 'default-avatar.svg' && $avatarFilename !== 'default-avatar.png') {
            $oldImagePath = UPLOAD_DIR . $avatarFilename;
            if (file_exists($oldImagePath)) {
                @unlink($oldImagePath);
            }
        }
        $avatarFilename = $newAvatar;
    }
}

// If errors encountered, redirect back with error list
if (!empty($errors)) {
    set_flash('danger', implode('<br>', $errors));
    redirect('/edit-profile.php');
}

// 6. Execute Prepared UPDATE Query
$dobValue = !empty($dob) ? $dob : null;
$countryValue = !empty($country) ? $country : null;

if ($updatePassword && $newPasswordHash) {
    $updateStmt = $conn->prepare("UPDATE users SET full_name = ?, bio = ?, date_of_birth = ?, gender = ?, country = ?, profile_image = ?, password = ? WHERE id = ?");
    $updateStmt->bind_param("sssssssi", $fullName, $bio, $dobValue, $gender, $countryValue, $avatarFilename, $newPasswordHash, $userId);
} else {
    $updateStmt = $conn->prepare("UPDATE users SET full_name = ?, bio = ?, date_of_birth = ?, gender = ?, country = ?, profile_image = ? WHERE id = ?");
    $updateStmt->bind_param("ssssssi", $fullName, $bio, $dobValue, $gender, $countryValue, $avatarFilename, $userId);
}

if ($updateStmt->execute()) {
    $updateStmt->close();
    
    // Refresh session data with updated name & avatar
    $_SESSION['full_name']     = $fullName;
    $_SESSION['profile_image'] = $avatarFilename;

    set_flash('success', 'Your profile details have been updated successfully!');
    redirect('/profile.php');
} else {
    $updateStmt->close();
    set_flash('danger', 'Failed to update profile due to an unexpected database error.');
    redirect('/edit-profile.php');
}
