<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * API Endpoint: admin/search-users.php
 * 
 * Secure AJAX Search Endpoint returning JSON for dynamic user filtering.
 * Authenticated and authorized server-side using prepared statements.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=UTF-8');

// 1. Strict Server-Side Authentication & Role Guard
if (!is_logged_in() || !is_admin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access Denied. Administrator privileges required.']);
    exit;
}

$conn = get_db_connection();
$currentAdminId = (int)current_user_id();

// 2. Retrieve & Sanitize Query Parameters
$search       = trim($_GET['q'] ?? ($_GET['search'] ?? ''));
$roleFilter   = trim($_GET['role'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');

$whereClauses = [];
$params       = [];
$types        = '';

if ($search !== '') {
    $whereClauses[] = "(full_name LIKE ? OR email LIKE ?)";
    $wildcard       = "%" . $search . "%";
    $params[]       = $wildcard;
    $params[]       = $wildcard;
    $types         .= 'ss';
}

if ($roleFilter !== '' && in_array($roleFilter, ['admin', 'user'], true)) {
    $whereClauses[] = "role = ?";
    $params[]       = $roleFilter;
    $types         .= 's';
}

if ($statusFilter !== '' && in_array($statusFilter, ['active', 'inactive', 'suspended'], true)) {
    $whereClauses[] = "status = ?";
    $params[]       = $statusFilter;
    $types         .= 's';
}

$whereSql = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

// 3. Execute Prepared Statement
$sql = "SELECT id, full_name, email, role, status, profile_image, created_at FROM users $whereSql ORDER BY id DESC LIMIT 50";
$stmt = $conn->prepare($sql);

if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$users = [];

while ($row = $result->fetch_assoc()) {
    $userId = (int)$row['id'];
    $users[] = [
        'id'                => $userId,
        'full_name'         => e($row['full_name']),
        'email'             => e($row['email']),
        'role'              => e($row['role']),
        'status'            => e($row['status']),
        'profile_image_url' => get_profile_image_url($row['profile_image']),
        'created_at'        => date('M d, Y', strtotime($row['created_at'])),
        'is_self'           => ($userId === $currentAdminId),
        'edit_url'          => BASE_URL . '/admin/edit-user.php?id=' . $userId,
        'delete_url'        => BASE_URL . '/admin/delete-user.php',
        'csrf_token'        => $_SESSION['csrf_token'] ?? ''
    ];
}

$stmt->close();

// 4. Return JSON Payload
echo json_encode([
    'success' => true,
    'total'   => count($users),
    'users'   => $users
]);
