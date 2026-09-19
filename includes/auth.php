<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Route Guard: includes/auth.php
 * 
 * Protects authenticated routes. Redirects unauthenticated visitors to login.php.
 */

require_once __DIR__ . '/functions.php';

if (!is_logged_in()) {
    set_flash('warning', 'Please sign in to access your dashboard and account settings.');
    redirect('/login.php');
}
