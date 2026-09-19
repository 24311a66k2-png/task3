<?php
/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Layout: includes/footer.php
 */
?>
</main>

<footer class="footer-devconnect mt-auto" aria-label="Site Footer">
  <div class="container">
    <div class="row g-4 mb-4">
      <!-- Brand & Summary -->
      <div class="col-lg-5 col-md-6">
        <a class="navbar-brand mb-3 d-inline-flex" href="<?php echo BASE_URL; ?>/index.php">
          <span class="brand-icon-box" aria-hidden="true">
            <i class="bi bi-code-slash"></i>
          </span>
          <span><?php echo APP_NAME; ?></span>
        </a>
        <p class="text-secondary small pe-lg-4">
          A secure, full-stack User Management and Profile Portal demonstrating PHP 8+, MySQL prepared statements, session authentication, and role-based CRUD operations.
        </p>
        <div class="d-flex gap-2">
          <a href="https://github.com/24311a66k2-png" target="_blank" rel="noopener noreferrer" class="footer-social-link" aria-label="GitHub Profile">
            <i class="bi bi-github"></i>
          </a>
          <a href="https://www.linkedin.com/in/pitla-yadagiri-07332b438" target="_blank" rel="noopener noreferrer" class="footer-social-link" aria-label="LinkedIn Profile">
            <i class="bi bi-linkedin"></i>
          </a>
          <a href="#" class="footer-social-link" aria-label="Discord Community">
            <i class="bi bi-discord"></i>
          </a>
        </div>
      </div>

      <!-- Quick Links -->
      <div class="col-lg-3 col-md-6 col-6">
        <h4 class="h6 text-white fw-bold mb-3">Portal Navigation</h4>
        <a href="<?php echo BASE_URL; ?>/index.php" class="footer-link">Home</a>
        <?php if (is_logged_in()): ?>
          <a href="<?php echo BASE_URL; ?>/dashboard.php" class="footer-link">Dashboard</a>
          <a href="<?php echo BASE_URL; ?>/profile.php" class="footer-link">User Profile</a>
          <a href="<?php echo BASE_URL; ?>/edit-profile.php" class="footer-link">Edit Settings</a>
        <?php else: ?>
          <a href="<?php echo BASE_URL; ?>/login.php" class="footer-link">User Login</a>
          <a href="<?php echo BASE_URL; ?>/register.php" class="footer-link">Create Account</a>
          <a href="<?php echo BASE_URL; ?>/forgot-password.php" class="footer-link">Password Reset</a>
        <?php endif; ?>
      </div>

      <!-- Internship Track Info -->
      <div class="col-lg-4 col-md-12">
        <h4 class="h6 text-white fw-bold mb-3">ApexPlanet Internship Details</h4>
        <ul class="list-unstyled text-secondary small mb-0">
          <li class="mb-1"><strong>Intern:</strong> Pitla Yadagiri</li>
          <li class="mb-1"><strong>Track:</strong> 60-Day Full Stack (PHP &amp; MySQL)</li>
          <li class="mb-1"><strong>Milestone:</strong> Task 3 — Backend Integration &amp; CRUD</li>
          <li class="mb-1"><strong>Database:</strong> MySQL via <code>mysqli</code> Prepared Statements</li>
          <li><strong>Architecture:</strong> Modular MVC-like Structure</li>
        </ul>
      </div>
    </div>

    <!-- Copyright -->
    <div class="d-flex flex-wrap justify-content-between align-items-center pt-3 border-top border-secondary border-opacity-10 small text-secondary">
      <div>
        &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?> Portal. All rights reserved.
      </div>
      <div>
        Engineered with <i class="bi bi-heart-fill text-danger mx-1"></i> for ApexPlanet Internship Task 3
      </div>
    </div>
  </div>
</footer>

<!-- Bootstrap 5.3.3 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<!-- Project JavaScript -->
<script src="<?php echo BASE_URL; ?>/assets/js/script.js"></script>
</body>
</html>
