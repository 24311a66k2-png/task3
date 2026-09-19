<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Layout: includes/header.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';

$pageTitle = isset($pageTitle) ? $pageTitle . ' — ' . APP_NAME : APP_NAME . ' — User Management Portal';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="DevConnect — PHP & MySQL User Management, Authentication & CRUD Portal. ApexPlanet Internship Task 3.">
  <meta name="author" content="Pitla Yadagiri">
  <title><?php echo e($pageTitle); ?></title>

  <!-- Google Fonts: Inter & JetBrains Mono -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

  <!-- Bootstrap 5.3.3 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

  <!-- Bootstrap Icons 1.11.3 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Custom Stylesheet -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">
<?php require_once __DIR__ . '/navbar.php'; ?>
<main class="flex-grow-1">
