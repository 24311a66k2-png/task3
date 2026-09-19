<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Database Connection: config/database.php
 * 
 * Centralized MySQL connection using mysqli with prepared statement support.
 */

// Database Configuration Constants
defined('DB_HOST') or define('DB_HOST', '127.0.0.1');
defined('DB_USER') or define('DB_USER', 'root');
defined('DB_PASS') or define('DB_PASS', '');
defined('DB_NAME') or define('DB_NAME', 'devconnect');
defined('DB_PORT') or define('DB_PORT', 3306);

// Establish mysqli connection with error handling
try {
    // Enable mysqli report mode for exceptions
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    
    // Set standard UTF-8 charset
    $conn->set_charset('utf8mb4');

} catch (mysqli_sql_exception $e) {
    // Log technical error internally for developers
    error_log('[DevConnect DB Error] ' . $e->getMessage());

    // Provide friendly, secure explanation to student / evaluator without leaking passwords
    $dbErrorTitle = 'Database Connection Notice';
    $dbErrorMessage = 'Could not connect to the MySQL database <strong>' . htmlspecialchars(DB_NAME) . '</strong>.<br>' .
      'Please ensure your MySQL server (XAMPP / WAMP) is running, and that you have imported the schema from <code>database/devconnect.sql</code> via phpMyAdmin.';
    
    // If not included via a script that handles it, render a clean error card
    if (!defined('SUPPRESS_DB_ERROR_PAGE')) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title><?php echo $dbErrorTitle; ?> — DevConnect</title>
          <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
          <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
          <style>
            body { background-color: #0a0e17; color: #f8fafc; font-family: system-ui, sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
            .error-card { max-width: 580px; background: #151e33; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; padding: 32px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
          </style>
        </head>
        <body>
          <div class="container d-flex justify-content-center">
            <div class="error-card text-center">
              <div class="mb-3 text-warning"><i class="bi bi-database-exclamation fs-1"></i></div>
              <h2 class="h4 fw-bold mb-3"><?php echo $dbErrorTitle; ?></h2>
              <p class="text-secondary small mb-4"><?php echo $dbErrorMessage; ?></p>
              <div class="text-start bg-dark p-3 rounded small font-monospace text-muted mb-4">
                <strong>Setup Steps in XAMPP:</strong><br>
                1. Open XAMPP Control Panel & Start Apache + MySQL<br>
                2. Navigate to: <code>http://localhost/phpmyadmin/</code><br>
                3. Click "Import" & select <code>database/devconnect.sql</code><br>
                4. Refresh this page
              </div>
              <a href="javascript:location.reload()" class="btn btn-outline-info btn-sm">
                <i class="bi bi-arrow-clockwise me-1"></i> Retry Connection
              </a>
            </div>
          </div>
        </body>
        </html>
        <?php
        exit();
    }
}

/**
 * Returns the active mysqli connection instance
 * @return mysqli
 */
function get_db_connection() {
    global $conn;
    return $conn;
}
