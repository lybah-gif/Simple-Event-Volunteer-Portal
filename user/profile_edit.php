<?php
// user/profile_edit.php
require_once __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header('Location: /simple-event-portal/index.php');
    exit;
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'User';

$err = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = esc($_POST['name'] ?? '');
    $email = esc($_POST['email'] ?? '');

    if (!$name || !$email) {
        $err = 'Name & Email required.';
    } else {
        $stmt = $mysqli->prepare("UPDATE users SET name=?, email=? WHERE id=?");
        $stmt->bind_param('ssi', $name, $email, $userId);
        if ($stmt->execute()) {
            $_SESSION['user_name'] = $name;
            $success = 'Profile updated successfully.';
        } else {
            $err = 'Database error. Please try again.';
        }
    }
}

$stmt = $mysqli->prepare("SELECT name, email FROM users WHERE id=?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Profile - VolunteerHub</title>
    <link rel="stylesheet" href="/simple-event-portal/assets/css/main.css">
</head>
<body class="dashboard-container">
    <!-- Sidebar -->
    <aside class="dashboard-sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
            </div>
            <span class="sidebar-brand-text">VolunteerHub</span>
        </div>
        
        <nav class="sidebar-nav">
            <a href="/simple-event-portal/user/dashboard.php" class="sidebar-nav-item">
                <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
                <span>Dashboard</span>
            </a>
            <a href="/simple-event-portal/user/events.php?my_events=1" class="sidebar-nav-item">
                <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>My Events</span>
            </a>
            <a href="/simple-event-portal/user/profile_edit.php" class="sidebar-nav-item active">
                <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>Profile</span>
            </a>
            <a href="/simple-event-portal/logout.php" class="sidebar-nav-item">
                <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span>Logout</span>
            </a>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <div class="dashboard-main">
        <!-- Top Header -->
        <header class="dashboard-header">
            <div class="header-inner">
                <form method="GET" action="/simple-event-portal/user/dashboard.php" class="search-container" style="display: flex; align-items: center; position: relative;">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" name="search" class="search-input" placeholder="Search events..." onkeypress="if(event.key === 'Enter') this.form.submit();">
                </form>
                
                <div class="header-right">
                    <a href="/simple-event-portal/user/dashboard.php" class="notification-btn" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </a>
                    
                    <div class="user-profile">
                        <div class="user-info">
                            <div class="user-name"><?php echo htmlspecialchars($userName); ?></div>
                            <div class="user-role">Volunteer</div>
                        </div>
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($userName, 0, 2)); ?>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <div class="welcome-section animate-fade-in-up">
                <h1 class="welcome-title">Edit Profile</h1>
                <p class="welcome-subtitle">Update your personal information and preferences.</p>
            </div>
            
            <div style="max-width: 600px; margin: 0 auto;">
                <div class="stat-card animate-fade-in-up">
                    <?php if ($err): ?>
                        <div class="error-message"><?php echo htmlspecialchars($err); ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>
                    
                    <form method="post" action="/simple-event-portal/user/profile_edit.php">
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <div class="form-input-wrapper">
                                <svg class="form-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <input type="text" name="name" class="form-input" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <div class="form-input-wrapper">
                                <svg class="form-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <input type="email" name="email" class="form-input" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                        </div>
                        
                        <div style="display: flex; gap: 12px; margin-top: 24px;">
                            <button type="submit" class="form-submit" style="flex: 1;">
                                Save Changes
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                            <a href="/simple-event-portal/user/dashboard.php" class="btn-outline" style="padding: 14px 24px; text-decoration: none; display: flex; align-items: center;">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
