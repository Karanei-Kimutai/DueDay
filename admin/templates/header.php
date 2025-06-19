<?php

// Set the default timezone to Africa/Nairobi for all date/time functions
date_default_timezone_set('Africa/Nairobi');
// --------------------
// Correctly navigate two directories up to find the core folder
require_once __DIR__ . '/../../core/init.php';

// --- NEW FLEXIBLE SECURITY CHECK ---
// Define default roles that can access the admin panel.
$admin_roles = ['Admin'];

// Allow a specific page to define its own list of allowed roles.
// If the page doesn't define $allowed_roles, it will use the default $admin_roles.
$effective_allowed_roles = $allowed_roles ?? $admin_roles;

// Security check: If user is not logged in OR their role is not in the allowed list, redirect.
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_name'], $effective_allowed_roles)) {
    header("Location: ../auth/login.php"); // Redirect to login if not authorized
    exit();
}
// --- END NEW SECURITY CHECK ---

// Get admin data for display
$admin_name = $_SESSION['user_fname'] ?? 'Admin';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: <?php echo ucfirst(str_replace(['admin_', '.php'], '', $current_page)); ?></title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="icon" href="../assets/icons/dueday.png" type="image/x-icon">
</head>
<body>
    <div class="admin-container">
        <div class="admin-sidebar">
            <div class="admin-logo"><span>DUEDAY ADMIN</span></div>
            <ul class="admin-nav-menu">
                <li class="<?php if ($current_page == 'admin_dashboard.php') echo 'active'; ?>">
                    <a href="admin_dashboard.php">Dashboard</a>
                </li>
                <li class="<?php if ($current_page == 'admin_users.php' || $current_page == 'edit_user.php') echo 'active'; ?>">
                    <a href="admin_users.php">Users</a>
                </li>
                <li class="<?php if ($current_page == 'admin_venues.php') echo 'active'; ?>">
                    <a href="admin_venues.php">Venues</a>
                </li>
                <li class="<?php if ($current_page == 'admin_classes.php') echo 'active'; ?>">
                    <a href="admin_classes.php">Classes</a>
                </li>
                 <li class="<?php if ($current_page == 'admin_enroll_students.php') echo 'active'; ?>">
                    <a href="admin_enroll_students.php">Enrollment</a>
                </li>
                <li class="<?php if ($current_page == 'admin_manage_schedule.php') echo 'active'; ?>">
                    <a href="admin_manage_schedule.php">Master Schedule</a>
                </li>
                <li class="<?php if ($current_page == 'admin_announcements.php') echo 'active'; ?>">
                    <a href="admin_announcements.php">Announcements</a>
                </li>
                <li><a href="../home.php" target="_blank">View Main Site</a></li>
                <li><a href="../auth/logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="admin-main-content">
            <header class="admin-header">
                <div class="profile-info">Welcome, <?php echo htmlspecialchars($admin_name); ?></div>
            </header>