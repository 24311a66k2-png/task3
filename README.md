# DevConnect — Full Stack PHP & MySQL User Management & CRUD Portal

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL Version](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/License-MIT-green.badge?style=for-the-badge)](LICENSE)

> **ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)**  
> **Task 3:** Backend Integration & CRUD Operations  
> **Developer:** Pitla Yadagiri  
> **Email:** `24311a66k2@aiml.sreenidhi.edu.in`  
> **GitHub Profile:** [@24311a66k2-png](https://github.com/24311a66k2-png)  
> **Repository:** [https://github.com/24311a66k2-png/task3](https://github.com/24311a66k2-png/task3)

---

## 📖 Executive Summary

**DevConnect** is an enterprise-grade, full-stack user management and developer collaboration web portal built with **PHP 8+**, **MySQL**, **HTML5**, **CSS3**, **Vanilla JavaScript**, and **Bootstrap 5.3.3**. 

Designed for both real-world deployment on Apache servers (XAMPP/WAMP/LAMP) and clear academic evaluation, the system features end-to-end database-backed user authentication, role-based access control (RBAC), multi-criteria profile management with secure profile photo uploads, dynamic dashboard analytics, and an administrative CRUD interface with live search, filtering, and pagination.

---

## 🛡️ Security Architecture & Best Practices

DevConnect implements industry-standard web application security measures:

1. **Prepared Statements on 100% of Queries**:
   - Every single database interaction utilizes `mysqli::prepare()`, typed parameter binding (`bind_param`), and executed statements.
   - Zero SQL string concatenation prevents SQL Injection vulnerabilities across the entire codebase.

2. **Cryptographic Password Hashing**:
   - Passwords are encrypted using PHP's native `password_hash($password, PASSWORD_DEFAULT)` producing secure bcrypt hashes.
   - Verification is handled strictly via `password_verify($password, $hash)` without ever handling or persisting plain-text passwords.

3. **Session Security & Session Fixation Defense**:
   - Secure cookie attributes: `HttpOnly=true`, `SameSite=Lax`, dynamic `Secure` flag on HTTPS.
   - Session identifiers are immediately regenerated upon authentication via `session_regenerate_id(true)`.
   - Complete session lifecycle management with clean `session_destroy()`, cookie expiration, and unsetting upon sign out.

4. **Cross-Site Request Forgery (CSRF) Tokens**:
   - Cryptographically random 32-byte CSRF tokens generated via `random_bytes(32)`.
   - Validated with `hash_equals()` on every POST request (`login`, `register`, `profile`, `admin create`, `admin edit`, `admin delete`).

5. **Cross-Site Scripting (XSS) Prevention**:
   - All dynamic output rendered to the browser is passed through `htmlspecialchars($val, ENT_QUOTES, 'UTF-8')` via the centralized helper function `e()`.

6. **Hardened File Upload Pipeline**:
   - Validates upload errors, file existence, and strict size caps ($\le 2$ MB).
   - Validates file extensions (`jpg`, `jpeg`, `png`, `webp`) AND validates real MIME types using PHP's `finfo` file analysis.
   - Sanitizes names into cryptographically secure random identifiers (`bin2hex(random_bytes(16)) . '.' . $ext`) to prevent path traversal or file overwriting attacks.

7. **Role-Based Access Control (RBAC)**:
   - Clear authorization boundaries separating `admin` and `user` privileges enforced server-side before page rendering.
   - Admin account self-lockout defense prevents administrators from revoking their own admin role, deactivating their account, or deleting themselves.

---

## ✨ Core Feature Highlights

### 1. Public Experience
- **Dynamic Landing Page (`index.php`)**: Adaptive homepage featuring platform overview, feature highlights, internship architectural roadmap, and session-aware navigation bar.
- **User Registration (`register.php`)**: Real-time password strength meter, confirmation password matcher, demographic fields (DOB, Gender, Country), input memory upon validation error, and duplicate email prevention.
- **User Authentication (`login.php`)**: Secure login with credential assistance and quick-fill helper for evaluators.
- **Password Recovery (`forgot-password.php`)**: Controlled recovery pipeline with neutral feedback preventing user account enumeration.

### 2. Authenticated User Experience
- **Interactive User Dashboard (`dashboard.php`)**: Dynamic welcome banner, KPI metrics, dynamic profile completion percentage gauge, user snapshot, and quick operational shortcuts.
- **Public Profile View (`profile.php`)**: High-resolution avatar display, bio presentation, demographic breakdown, and registration timestamps.
- **Profile Customizer (`edit-profile.php`)**: Live avatar preview upon image selection, biographical editing, and optional current-password verified credentials update.

### 3. Administrator Experience (`admin/`)
- **Admin Analytics Dashboard (`admin/dashboard.php`)**: Real-time KPI counts (Total Users, Active Users, Inactive/Suspended Users, Administrators), recent user registration feed, and live server environment specifications.
- **User CRUD Control Center (`admin/users.php`)**:
  - **Create**: Add new user/admin directly via administrative interface (`admin/create-user.php`).
  - **Read**: Tabular listing with avatars, role badges, and status badges.
  - **Search**: Real-time wildcard search querying both user names and emails.
  - **Filter**: Filter by system role (`all`, `admin`, `user`) and account status (`all`, `active`, `inactive`, `suspended`).
  - **Pagination**: Responsive pagination controls maintaining active filter parameters.
  - **Update**: Edit user records, permissions, status, or trigger administrative password overrides (`admin/edit-user.php`).
  - **Delete**: Safe deletion via POST with CSRF verification and automatic profile image cleanup (`admin/delete-user.php`). Self-deletion is actively blocked.

---

## 🗄️ Database Architecture

The application requires a single MySQL database named `devconnect`.

```sql
CREATE DATABASE IF NOT EXISTS `devconnect`
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE `devconnect`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user',
  `status` ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
  `bio` TEXT DEFAULT NULL,
  `profile_image` VARCHAR(255) DEFAULT 'default-avatar.svg',
  `date_of_birth` DATE DEFAULT NULL,
  `gender` ENUM('male', 'female', 'non-binary', 'prefer-not-to-say') DEFAULT 'prefer-not-to-say',
  `country` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`),
  INDEX `idx_role` (`role`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Pre-Seeded Evaluation Accounts
Both accounts are generated with verified bcrypt hashes (`PASSWORD_DEFAULT`):

| Role | Email | Password | Status |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@devconnect.io` | `Admin@12345` | `active` |
| **Standard User** | `user@devconnect.io` | `User@12345` | `active` |

---

## 📂 Project Directory Structure

```text
task3/
├── actions/
│   ├── login-action.php          # POST handler: authentication & session generation
│   ├── password-action.php       # POST handler: password recovery verification
│   ├── profile-action.php        # POST handler: profile updates, avatar upload & pwd change
│   └── register-action.php       # POST handler: registration & duplicate email check
├── admin/
│   ├── create-user.php           # Admin interface: create new user/admin account
│   ├── dashboard.php             # Admin interface: analytics & server metrics
│   ├── delete-user.php           # POST handler: secure user deletion with self-guard
│   ├── edit-user.php             # Admin interface: edit details, roles, and status
│   └── users.php                 # Admin interface: user CRUD table, search, filters & pagination
├── assets/
│   ├── css/
│   │   └── style.css             # Custom dark SaaS theme, glassmorphism & responsive styles
│   └── js/
│       └── script.js             # Live password toggle, strength meter, confirm check & preview
├── config/
│   ├── config.php                # Global constants, session security & dynamic BASE_URL detection
│   └── database.php              # Centralized MySQLi connection & graceful failure handler
├── database/
│   └── devconnect.sql            # Complete database schema with indexes & seed accounts
├── includes/
│   ├── admin-auth.php            # Route guard: restricts access to administrators
│   ├── auth.php                  # Route guard: restricts access to authenticated users
│   ├── footer.php                # Reusable HTML footer & script inclusions
│   ├── functions.php             # Helper library (e(), CSRF, flash alerts, file uploads)
│   ├── header.php                # Reusable HTML head, meta tags, fonts & Bootstrap CSS
│   └── navbar.php                # Dynamic session-aware responsive navigation bar
├── uploads/
│   └── profiles/
│       ├── .gitkeep              # Preserves directory in version control
│       └── default-avatar.svg    # High-quality fallback SVG avatar
├── .gitignore                    # Git exclusion rules
├── dashboard.php                 # Authenticated user dashboard
├── edit-profile.php              # Authenticated user profile editor
├── forgot-password.php           # Password recovery request page
├── index.php                     # Public landing & feature showcase page
├── LICENSE                       # MIT Open-Source License
├── login.php                     # Secure login interface
├── logout.php                    # Session termination & clean sign out
├── profile.php                   # Authenticated user profile view
├── README.md                     # Comprehensive technical documentation & guide
└── register.php                  # User registration page
```

---

## 🚀 Setup & Installation Guide

### Prerequisites
- **XAMPP**, **WAMP**, or **LAMP** with **PHP 8.0+** and **MySQL 5.7+ / MariaDB 10.4+**.
- Modern web browser (Chrome, Edge, Firefox, Safari).

### Step 1: Place Files into Web Root
Clone or copy the project folder into your web server directory:
- **XAMPP (Windows)**: `C:\xampp\htdocs\task3`
- **WAMP (Windows)**: `C:\wamp64\www\task3`
- **Linux (Apache)**: `/var/www/html/task3`

```bash
cd C:\xampp\htdocs
git clone https://github.com/24311a66k2-png/task3.git
```

### Step 2: Start Apache and MySQL
1. Launch the **XAMPP Control Panel**.
2. Start the **Apache** module.
3. Start the **MySQL** module.

### Step 3: Import the Database
Option A — Via phpMyAdmin:
1. Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
2. Click **Import** in the top navigation bar.
3. Select the file: `database/devconnect.sql` from your project folder.
4. Click **Import** at the bottom. The database `devconnect` and table `users` will be automatically created and pre-seeded.

Option B — Via Command Line:
```bash
mysql -u root -p < C:\xampp\htdocs\task3\database\devconnect.sql
```
*(Press Enter if your MySQL root user has no password)*.

### Step 4: Verify Database Configuration
Open `config/database.php` and ensure your local MySQL credentials match:
```php
define('DB_HOST', '127.0.0.1');
define('DB_PORT', 3306);
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'devconnect');
```

### Step 5: Launch Application
Open your web browser and navigate to:
```text
http://localhost/task3
```
*(Note: DevConnect features dynamic BASE_URL detection and runs identically whether hosted at root `http://localhost`, in `/task3`, or in `/devconnect`)*.

---

## 🎥 5–10 Minute Video Demonstration Script

Use this structured script to present your internship project for evaluation or video submission:

### Part 1: Introduction & Architecture (0:00 – 1:30)
- **Script**: *"Hello, my name is Pitla Yadagiri, and this is my submission for Task 3 of the ApexPlanet Full Stack Web Development Internship. Task 3 focuses on Backend Integration & CRUD Operations using PHP and MySQL. I built DevConnect, a full-stack user management and developer collaboration portal. The architecture follows a clean MVC-inspired structure with centralized configurations, helper libraries, prepared MySQLi statements, and strict role-based access control."*
- **Action**: Show `README.md`, folder structure, and the landing page (`index.php`).

### Part 2: Database Schema & Pre-Seeding (1:30 – 2:30)
- **Script**: *"Let's take a look at the database. The application connects to MySQL via `config/database.php`. In phpMyAdmin, we have our `devconnect` database with an indexed `users` table storing full names, emails, bcrypt password hashes, roles, statuses, demographics, and profile avatars. We have pre-seeded accounts for both standard user and admin roles."*
- **Action**: Show phpMyAdmin, the `users` table structure, and password hashes.

### Part 3: User Registration & Validation (2:30 – 4:00)
- **Script**: *"Now let's demonstrate user registration. The form includes client-side real-time password strength metering and confirmation matching. On the backend, `actions/register-action.php` enforces CSRF validation, sanitizes inputs, ensures password length, checks for duplicate emails with a prepared statement, and hashes the password with `password_hash()`. If an error occurs, inputs are preserved. Let's register a new developer account."*
- **Action**: Register an account with live typing, show validation, submit, and observe the success flash message.

### Part 4: Authentication, Dashboard & Profile Management (4:00 – 6:00)
- **Script**: *"Next, let's sign in with the standard user account (`user@devconnect.io`). The login handler verifies the bcrypt hash with `password_verify()` and regenerates the session ID to defend against session fixation attacks. Once logged in, the user dashboard displays dynamic metrics, account status, and profile completion. Let's visit `edit-profile.php`, where we can update our bio, change password, and upload a profile photo. The upload script verifies MIME types using finfo, enforces a 2MB limit, generates a cryptographically unique filename, and cleans up old avatar files."*
- **Action**: Sign in, view dashboard, edit profile, upload an avatar with live preview, save, and show the updated profile.

### Part 5: Administrator Portal & CRUD Operations (6:00 – 8:30)
- **Script**: *"Now let's sign out and log in as an administrator (`admin@devconnect.io`). Because the session role is 'admin', the navbar reveals the Admin Portal. On `admin/dashboard.php`, we see live KPI cards querying total, active, inactive users, and administrators directly from MySQL. Let's navigate to `admin/users.php` to demonstrate full CRUD. We can search users by name or email, filter by role or status, and paginate. Let's create a user directly from the admin panel, edit an existing user to update their status or reset their password, and demonstrate deleting a user. Notice that the delete action is guarded by CSRF and a JavaScript confirmation modal, and administrators are strictly prevented from deleting their own active session."*
- **Action**: Show admin KPIs, perform search and filter, create a user, edit a user, delete a user, and hover over the disabled delete button on the current admin.

### Part 6: Code Quality, Security Summary & Conclusion (8:30 – 10:00)
- **Script**: *"To conclude, 100% of queries use prepared statements, all outputs are escaped with htmlspecialchars, CSRF tokens protect all state-modifying requests, and passwords are fully hashed. The code is modular, well-commented, and passes PHP 8.3 linting with zero syntax errors. Thank you ApexPlanet for this comprehensive internship assignment."*
- **Action**: Show code snippets in `actions/` and `includes/functions.php`, and wrap up.

---

## 📋 Git Commit History & Development Log

This project was developed incrementally across 10 structured commits:

| Commit | Commit Message | Description |
| :---: | :--- | :--- |
| **01** | `chore: initial repository setup & project directory structure` | Configured `.gitignore`, `LICENSE`, and base folder hierarchy. |
| **02** | `feat(db): create MySQL database schema & pre-seeded accounts` | Designed `database/devconnect.sql` with indexes and bcrypt seeds. |
| **03** | `feat(core): implement secure configuration & centralized database connection` | Built `config/config.php` (BASE_URL, session) and `config/database.php`. |
| **04** | `feat(security): create helper library, CSRF defense & authentication route guards` | Implemented `includes/functions.php`, `auth.php`, and `admin-auth.php`. |
| **05** | `feat(ui): create modern responsive layout, dark SaaS styles & client scripts` | Created `header.php`, `navbar.php`, `footer.php`, `style.css`, and `script.js`. |
| **06** | `feat(auth): build user registration interface & backend processing` | Built `register.php` and `actions/register-action.php` with validation. |
| **07** | `feat(auth): implement secure login, session regeneration & password recovery` | Created `login.php`, `forgot-password.php`, and corresponding actions. |
| **08** | `feat(user): build authenticated dashboard & profile management with avatar uploads` | Built `dashboard.php`, `profile.php`, `edit-profile.php`, and image handling. |
| **09** | `feat(admin): build admin analytics dashboard & complete user CRUD management` | Built `admin/dashboard.php`, `users.php`, `create-user.php`, `edit-user.php`, and `delete-user.php`. |
| **10** | `docs: finalize comprehensive README, setup instructions & evaluation video script` | Added exhaustive documentation, security specifications, and demo script. |

---

## ✅ Task 3 Completion Checklist

- [x] PHP 8+ and MySQL environment compatibility.
- [x] MySQL database created with proper data types, primary keys, and indexes.
- [x] Pre-seeded demo evaluation accounts (`admin@devconnect.io` & `user@devconnect.io`).
- [x] Prepared statements used for 100% of database queries (Zero SQL injection risk).
- [x] Passwords securely hashed with `password_hash(PASSWORD_DEFAULT)`.
- [x] Passwords securely verified with `password_verify()`.
- [x] Session management with fixation defense (`session_regenerate_id(true)`).
- [x] Dynamic session-aware navigation bar.
- [x] Robust CSRF token validation on all POST requests.
- [x] XSS output escaping using `htmlspecialchars()` via `e()`.
- [x] Profile avatar upload with MIME validation (`finfo`), size limit, and randomized filenames.
- [x] Role-Based Access Control (RBAC) protecting user and admin routes.
- [x] Complete Admin CRUD: Create, Read, Update, Delete.
- [x] User search by name/email, filtering by role/status, and pagination.
- [x] Self-deletion lockout prevention for administrators.
- [x] Responsive dark SaaS UI using Bootstrap 5.3.3 & custom CSS.
- [x] Comprehensive documentation, setup guide, commit log, and video script.

---

## 📄 License

Distributed under the MIT License. See `LICENSE` for details.

Developed with ❤️ by **Pitla Yadagiri** for the **ApexPlanet Full Stack Web Development Internship**.
