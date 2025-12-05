<?php  
// admin/dashboard.php
require_once __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /simple-event-portal/index.php');
    exit;
}

$userName = $_SESSION['user_name'] ?? 'Admin';

// Get stats
$stats = [
    'events' => $mysqli->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'] ?? 0,
    'volunteers' => $mysqli->query("SELECT COUNT(*) as count FROM volunteers")->fetch_assoc()['count'] ?? 0,
    'tasks' => $mysqli->query("SELECT COUNT(*) as count FROM tasks")->fetch_assoc()['count'] ?? 0,
    'registrations' => $mysqli->query("SELECT COUNT(*) as count FROM event_registrations")->fetch_assoc()['count'] ?? 0
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - VolunteerHub</title>
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
            <a href="/simple-event-portal/admin/dashboard.php" class="sidebar-nav-item active">
                <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
                <span>Dashboard</span>
            </a>
            <a href="/simple-event-portal/admin/events_list.php" class="sidebar-nav-item">
                <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>Events</span>
            </a>
            <a href="/simple-event-portal/admin/volunteers_list.php" class="sidebar-nav-item">
                <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span>Volunteers</span>
            </a>
            <a href="/simple-event-portal/admin/tasks_list.php" class="sidebar-nav-item">
                <svg class="sidebar-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <span>Tasks</span>
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
                <div class="search-container">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" class="search-input" placeholder="Search...">
                </div>
                
                <div class="header-right">
                    <button class="notification-btn">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span class="notification-dot"></span>
                    </button>
                    
                    <div class="user-profile">
                        <div class="user-info">
                            <div class="user-name"><?php echo htmlspecialchars($userName); ?></div>
                            <div class="user-role">Admin</div>
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
                <h1 class="welcome-title">Admin Dashboard</h1>
                <p class="welcome-subtitle">Manage events, volunteers, and tasks across the community portal.</p>
            </div>
            
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card animate-fade-in-up">
                    <div class="stat-header">
                        <div class="stat-icon-wrapper purple">
                            <svg class="stat-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <svg class="trending-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="stat-label">Total Events</div>
                    <div class="stat-value"><?php echo $stats['events']; ?></div>
                </div>
                
                <div class="stat-card animate-fade-in-up" style="animation-delay: 0.1s;">
                    <div class="stat-header">
                        <div class="stat-icon-wrapper pink">
                            <svg class="stat-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <svg class="trending-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="stat-label">Volunteers</div>
                    <div class="stat-value"><?php echo $stats['volunteers']; ?></div>
                </div>
                
                <div class="stat-card animate-fade-in-up" style="animation-delay: 0.2s;">
                    <div class="stat-header">
                        <div class="stat-icon-wrapper blue">
                            <svg class="stat-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <svg class="trending-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="stat-label">Tasks</div>
                    <div class="stat-value"><?php echo $stats['tasks']; ?></div>
                </div>
                
                <div class="stat-card animate-fade-in-up" style="animation-delay: 0.3s;">
                    <div class="stat-header">
                        <div class="stat-icon-wrapper purple">
                            <svg class="stat-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <svg class="trending-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="stat-label">Registrations</div>
                    <div class="stat-value"><?php echo $stats['registrations']; ?></div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="section-header" style="margin-top: 48px;">
                <h2 class="section-title">Quick Actions</h2>
            </div>
            
            <div class="events-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
                <a href="/simple-event-portal/admin/events_create.php" class="event-card" style="text-decoration: none; cursor: pointer;">
                    <div class="event-content">
                        <div style="text-align: center; padding: 40px;">
                            <svg width="60" height="60" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--primary-purple); margin-bottom: 16px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <h3 class="event-title">Create Event</h3>
                            <p style="color: var(--text-gray);">Add a new volunteer event</p>
                        </div>
                    </div>
                </a>
                
                <a href="/simple-event-portal/admin/volunteers_add.php" class="event-card" style="text-decoration: none; cursor: pointer;">
                    <div class="event-content">
                        <div style="text-align: center; padding: 40px;">
                            <svg width="60" height="60" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--primary-pink); margin-bottom: 16px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <h3 class="event-title">Add Volunteer</h3>
                            <p style="color: var(--text-gray);">Register a new volunteer</p>
                        </div>
                    </div>
                </a>
                
                <a href="/simple-event-portal/admin/tasks_assign.php" class="event-card" style="text-decoration: none; cursor: pointer;">
                    <div class="event-content">
                        <div style="text-align: center; padding: 40px;">
                            <svg width="60" height="60" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--primary-blue); margin-bottom: 16px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="event-title">Assign Task</h3>
                            <p style="color: var(--text-gray);">Create and assign tasks</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
