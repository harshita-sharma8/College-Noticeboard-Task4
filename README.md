# College-Noticeboard-Task4
# 🔒 Task 4: Security Enhancement & Access Control

## Overview
In Task 4, the **College Notice Board System** was significantly enhanced to protect user data, prevent common web vulnerabilities, and enforce strict permission levels across different user roles.

---

## Key Security Features Implemented

### 1. SQL Injection Prevention
* Implemented **PHP Prepared Statements** (`mysqli_stmt`) for all database operations (login, registration, notice posting, and updates).
* Separated user inputs from SQL execution logic, completely eliminating SQL Injection risks.

### 2. Password Encryption
* Replaced plain-text password storage with **Bcrypt Hashing** (`password_hash()` and `password_verify()`).
* Passwords are hashed before being stored in the database, ensuring raw passwords are never exposed.

### 3. Role-Based Access Control (RBAC)
* Enforced strict access boundaries between **Admin** and **Student** roles.
* Restricted administrative features (creating, deleting, or editing notices) exclusively to authenticated Admin users.
* Blocked unauthorized page navigation via direct URL access.

### 4. Secure Session & Auth Management
* Secured user session lifecycle with proper authentication checks on every restricted page.
* Built robust logout mechanisms that invalidate session tokens and destroy session cookies completely.

---

## Database & Setup Notes
* **Database Schema:** `noticeboard.sql` includes all updated table structures (`users`, `notices`) and role definitions.
* **Compatibility:** Tested on local XAMPP environment (Apache Server & MySQL Database).

* task4/
├── api/
│   └── get_notices.php       # JSON API Endpoint for Async Requests
├── classes/
│   ├── CategoryNotice.php    # Extended Class for Category Logic
│   ├── Notice.php            # Core Base Class for Notice CRUD
│   └── Notification.php      # Class for Student Notification Tracking
├── config/
│   └── Database.php         # PDO Database Connection
├── security/
│   ├── login.php           # login page for admin and student portal
│   ├── logout.php           # logout page for admin and student portal
│   └── register.php        # registeration of student portal before the dashboard
├── admin_dashboard.php       # Administrative Control Panel (CRUD)
├── index.php              # Student Portal & Dynamic Notice View
├── noticeboard.sql               # database schema of table `users`and `notices`
└── README.md                 # Project Documentation
