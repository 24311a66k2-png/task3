<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Configuration: config/config.php
 */

// 1. Error Reporting Configuration (Development Mode)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 2. Start Secure Session
if (session_status() === PHP_SESSION_NONE) {
    // Session cookie parameters for security
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// 3. Application Constants
if (!defined('APP_NAME')) {
    define('APP_NAME', 'DevConnect');
}

// 4. Dynamic Base URL Detection
// Automatically resolves regardless of folder name, nesting, or root placement
if (!defined('BASE_URL')) {
    if (!empty($_SERVER['DOCUMENT_ROOT'])) {
        $docRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']) ?: $_SERVER['DOCUMENT_ROOT']);
        $appRoot = str_replace('\\', '/', realpath(dirname(__DIR__)) ?: dirname(__DIR__));
        if ($docRoot && strpos($appRoot, $docRoot) === 0) {
            $rel = substr($appRoot, strlen($docRoot));
            define('BASE_URL', rtrim($rel, '/'));
        } else {
            define('BASE_URL', '');
        }
    } else {
        define('BASE_URL', '');
    }
}

// 5. File Upload Directory Paths
if (!defined('UPLOAD_DIR')) {
    define('UPLOAD_DIR', dirname(__DIR__) . '/uploads/profiles/');
}
if (!defined('UPLOAD_URL')) {
    define('UPLOAD_URL', BASE_URL . '/uploads/profiles/');
}

// 6. Initialize CSRF Token if not present
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
