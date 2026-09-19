<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Script: logout.php
 * 
 * Secure session termination, cookie invalidation, and redirection.
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

// 1. Clear all session variables
$_SESSION = [];

// 2. Invalidate session cookie if active
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// 3. Destroy session on server
session_destroy();

// 4. Initialize clean temporary session for logout flash feedback
session_start();
set_flash('info', 'You have successfully signed out. Thank you for using DevConnect.');

// 5. Redirect to login
redirect('/login.php');
