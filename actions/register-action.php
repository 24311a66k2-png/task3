<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Action: actions/register-action.php
 * 
 * Processes user registration with validation, duplicate check, and password_hash().
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/register.php');
}

// 1. Verify CSRF Token
if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    set_flash('danger', 'Security validation failed (Invalid CSRF token). Please try submitting again.');
    redirect('/register.php');
}

// 2. Sanitize & Retrieve Inputs
$fullName        = sanitize_input($_POST['full_name'] ?? '');
$email           = strtolower(sanitize_input($_POST['email'] ?? ''));
$password        = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$dob             = sanitize_input($_POST['dob'] ?? '');
$gender          = sanitize_input($_POST['gender'] ?? 'prefer-not-to-say');
$country         = sanitize_input($_POST['country'] ?? '');
$termsAccepted   = isset($_POST['terms']);

// Preserve old input for user convenience (excluding passwords)
$_SESSION['old_input'] = [
    'full_name' => $fullName,
    'email'     => $email,
    'dob'       => $dob,
    'gender'    => $gender,
    'country'   => $country
];

// 3. Server-Side Validation
$errors = [];

if (empty($fullName) || mb_strlen($fullName) < 2) {
    $errors[] = 'Full name must be at least 2 characters long.';
}

if (empty($email) || !is_valid_email($email)) {
    $errors[] = 'A valid email address is required.';
}

if (empty($password) || mb_strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters long.';
}

if ($password !== $confirmPassword) {
    $errors[] = 'Passwords do not match.';
}

if (!empty($dob)) {
    $dobDate = DateTime::createFromFormat('Y-m-d', $dob);
    if (!$dobDate || $dobDate > new DateTime()) {
        $errors[] = 'Date of birth must be a valid past date.';
    }
}

$validGenders = ['male', 'female', 'non-binary', 'prefer-not-to-say'];
if (!in_array($gender, $validGenders, true)) {
    $gender = 'prefer-not-to-say';
}

if (!$termsAccepted) {
    $errors[] = 'You must accept the Terms of Service to create an account.';
}

// If basic validation failed, return errors
if (!empty($errors)) {
    set_flash('danger', implode('<br>', $errors));
    redirect('/register.php');
}

// 4. Duplicate Email Verification (Prepared Statement)
$conn = get_db_connection();

$checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$checkStmt->bind_param("s", $email);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    $checkStmt->close();
    set_flash('danger', 'An account is already registered with this email address. Please sign in or use another email.');
    redirect('/register.php');
}
$checkStmt->close();

// 5. Secure Password Hashing
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// 6. Insert New User (Prepared Statement)
$dobValue = !empty($dob) ? $dob : null;
$countryValue = !empty($country) ? $country : null;
$role = 'user';
$status = 'active';

$insertStmt = $conn->prepare("INSERT INTO users (full_name, email, password, date_of_birth, gender, country, role, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$insertStmt->bind_param("ssssssss", $fullName, $email, $passwordHash, $dobValue, $gender, $countryValue, $role, $status);

if ($insertStmt->execute()) {
    $insertStmt->close();
    unset($_SESSION['old_input']);
    set_flash('success', 'Registration successful! Your DevConnect account has been created. You can now sign in.');
    redirect('/login.php');
} else {
    $insertStmt->close();
    set_flash('danger', 'An unexpected database error occurred during registration. Please try again later.');
    redirect('/register.php');
}
