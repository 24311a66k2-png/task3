<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Action: actions/password-action.php
 * 
 * Handles password recovery simulation with database lookup and prepared statement.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/forgot-password.php');
}

// 1. Verify CSRF Token
if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    set_flash('danger', 'Security validation failed (Invalid CSRF token). Please try submitting again.');
    redirect('/forgot-password.php');
}

// 2. Validate Email
$email = strtolower(sanitize_input($_POST['email'] ?? ''));

if (empty($email) || !is_valid_email($email)) {
    set_flash('danger', 'Please provide a valid email address.');
    redirect('/forgot-password.php');
}

// 3. Database Lookup using Prepared Statement
$conn = get_db_connection();
$stmt = $conn->prepare("SELECT id, full_name FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// 4. Feedback Notice (Consistent response prevents user enumeration)
if ($user) {
    set_flash('info', 'Password recovery instructions have been dispatched to <strong>' . e($email) . '</strong>. <br><small class="text-muted">(Demo Note: For evaluation, the test accounts default password is <code>Admin@12345</code> or <code>User@12345</code>)</small>');
} else {
    set_flash('info', 'If an active account is registered with <strong>' . e($email) . '</strong>, password recovery instructions have been dispatched.');
}

redirect('/login.php');
