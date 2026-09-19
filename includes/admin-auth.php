<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Route Guard: includes/admin-auth.php
 * 
 * Protects administrative routes. Requires active authentication and role === 'admin'.
 */

require_once __DIR__ . '/auth.php';

if (!is_admin()) {
    set_flash('danger', 'Access Denied: You do not have permission to view administrator management pages.');
    redirect('/dashboard.php');
}
