<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Action: actions/login-action.php
 * 
 * Authenticates user credentials via password_verify() and initializes secure session.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/login.php');
}

// 1. Verify CSRF Token
if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    set_flash('danger', 'Security validation failed (Invalid CSRF token). Please try signing in again.');
    redirect('/login.php');
}

// 2. Sanitize & Retrieve Inputs
$email    = strtolower(sanitize_input($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

// Basic field checks
if (empty($email) || empty($password)) {
    set_flash('danger', 'Please provide both your email address and password.');
    redirect('/login.php');
}

// 3. Query User using Prepared Statement
$conn = get_db_connection();

$stmt = $conn->prepare("SELECT id, full_name, email, password, role, status, profile_image FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$user = $result->fetch_assoc();
$stmt->close();

// 4. Verify Password (Constant-time verification prevents timing attacks)
if (!$user || !password_verify($password, $user['password'])) {
    // Generic error message prevents account enumeration
    set_flash('danger', 'Invalid email or password. Please verify your credentials and try again.');
    redirect('/login.php');
}

// 5. Verify Account Status
if ($user['status'] !== 'active') {
    set_flash('warning', 'Your account has been deactivated. Please contact the administrator for assistance.');
    redirect('/login.php');
}

// 6. Prevent Session Fixation & Initialize Authenticated Session
session_regenerate_id(true);

$_SESSION['user_id']       = (int)$user['id'];
$_SESSION['full_name']     = $user['full_name'];
$_SESSION['email']         = $user['email'];
$_SESSION['role']          = $user['role'];
$_SESSION['profile_image'] = $user['profile_image'];

// 7. Role-Based Redirection with Welcome Notice
set_flash('success', 'Welcome back, <strong>' . e($user['full_name']) . '</strong>! You have signed in successfully.');

if ($user['role'] === 'admin') {
    redirect('/admin/dashboard.php');
} else {
    redirect('/dashboard.php');
}
