<?php
require_once __DIR__ . '/../config/db.php';

// Only allow logged-in users with role 'user'
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header('Location: /simple-event-portal/index.php');
    exit;
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'User';

// Handle messages from registration or other actions
$msg = '';
$msgType = '';
if (isset($_GET['msg']) && isset($_GET['type'])) {
    $msg = urldecode($_GET['msg']);
    $msgType = $_GET['type'];
}

// Handle event unregistration from dashboard
if (isset($_GET['unregister'])) {
    $event_id = intval($_GET['unregister']);
    $stmt = $mysqli->prepare("DELETE FROM event_registrations WHERE user_id=? AND event_id=?");
    $stmt->bind_param('ii', $userId, $event_id);
    if ($stmt->execute()) {
        $msg = 'Successfully unregistered from the event.';
        $msgType = 'success';
    } else {
        $msg = 'Failed to unregister. Please try again.';
        $msgType = 'error';
    }
}

// Handle search
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : 'All';

// Get registered events
$registeredEvents = [];
$stmt = $mysqli->prepare("
    SELECT e.*, 
        (SELECT COUNT(*) FROM event_registrations r WHERE r.event_id = e.id) AS reg_count
    FROM events e
    INNER JOIN event_registrations er ON e.id = er.event_id
    WHERE er.user_id = ?
    ORDER BY e.event_date ASC
    LIMIT 2
");
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $registeredEvents[] = $row;
}

// Get available events (not registered) with search and filter
$availableEvents = [];
$sql = "
    SELECT e.*, 
        (SELECT COUNT(*) FROM event_registrations r WHERE r.event_id = e.id) AS reg_count
    FROM events e
    WHERE e.id NOT IN (SELECT event_id FROM event_registrations WHERE user_id = ?)
    AND e.event_date >= CURDATE()
";

$params = [$userId];
$types = 'i';

if ($searchQuery) {
    $sql .= " AND (e.title LIKE ? OR e.description LIKE ? OR e.venue LIKE ?)";
    $searchParam = "%$searchQuery%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= 'sss';
}

// Note: Category filter would need a category column in events table
// For now, we'll just apply search filter

$sql .= " ORDER BY e.event_date ASC LIMIT 20";

$stmt = $mysqli->prepare($sql);
if (count($params) > 0) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $availableEvents[] = $row;
}

// Calculate stats
$stmt = $mysqli->prepare("SELECT COUNT(*) as count FROM event_registrations WHERE user_id = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$res = $stmt->get_result();
$stats['registered_events'] = $res->fetch_assoc()['count'] ?? 0;

// Get tasks count
$userEmail = '';
$stmt = $mysqli->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$res = $stmt->get_result();
if ($u = $res->fetch_assoc()) $userEmail = $u['email'];

$volId = 0;
if ($userEmail) {
    $stmt = $mysqli->prepare("SELECT id FROM volunteers WHERE email = ?");
    $stmt->bind_param('s', $userEmail);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($r = $res->fetch_assoc()) $volId = $r['id'];
}

$stats['upcoming_tasks'] = 0;
if ($volId) {
    $stmt = $mysqli->prepare("SELECT COUNT(*) as count FROM tasks WHERE volunteer_id = ? AND status != 'completed'");
    $stmt->bind_param('i', $volId);
    $stmt->execute();
    $res = $stmt->get_result();
    $stats['upcoming_tasks'] = $res->fetch_assoc()['count'] ?? 0;
}

// Estimate volunteer hours
$stats['volunteer_hours'] = $stats['registered_events'] * 4;

// Get notifications (upcoming events in next 3 days)
$notifications = [];
$stmt = $mysqli->prepare("
    SELECT e.title, e.event_date, e.event_time
    FROM events e
    INNER JOIN event_registrations er ON e.id = er.event_id
    WHERE er.user_id = ?
    AND e.event_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)
    ORDER BY e.event_date ASC
    LIMIT 5
");
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard - VolunteerHub</title>
    <link rel="stylesheet" href="/simple-event-portal/assets/css/main.css">
    <style>
        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 8px;
    background: white;
    border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            min-width: 320px;
            max-height: 400px;
            overflow-y: auto;
            z-index: 50;
            display: none;
        }
        .notification-dropdown.show {
            display: block;
        }
        .notification-header {
            padding: 16px;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
            color: var(--text-dark);
        }
        .notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
            transition: background 0.2s;
        }
        .notification-item:hover {
            background: #f9fafb;
        }
        .notification-item:last-child {
            border-bottom: none;
        }
        .notification-text {
            font-size: 14px;
            color: var(--text-dark);
            margin-bottom: 4px;
        }
        .notification-time {
            font-size: 12px;
            color: var(--text-gray);
        }
        .notification-empty {
            padding: 24px;
            text-align: center;
            color: var(--text-gray);
}
</style>
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
            <a href="/simple-event-portal/user/dashboard.php" class="sidebar-nav-item active">
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
            <a href="/simple-event-portal/user/profile_edit.php" class="sidebar-nav-item">
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
                    <input type="text" name="search" class="search-input" placeholder="Search events..." value="<?php echo htmlspecialchars($searchQuery); ?>" onkeypress="if(event.key === 'Enter') this.form.submit();">
                    <?php if ($categoryFilter !== 'All'): ?>
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($categoryFilter); ?>">
                    <?php endif; ?>
                </form>
                
                <div class="header-right">
                    <div style="position: relative;">
                        <button class="notification-btn" id="notificationBtn" type="button">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <?php if (count($notifications) > 0): ?>
                                <span class="notification-dot"></span>
                            <?php endif; ?>
                        </button>
                        <div class="notification-dropdown" id="notificationDropdown">
                            <div class="notification-header">Notifications</div>
                            <?php if (count($notifications) > 0): ?>
                                <?php foreach ($notifications as $notif): ?>
                                    <div class="notification-item">
                                        <div class="notification-text">
                                            <strong><?php echo htmlspecialchars($notif['title']); ?></strong> is coming up soon!
                                        </div>
                                        <div class="notification-time">
                                            <?php 
                                            $date = new DateTime($notif['event_date']);
                                            echo $date->format('M d, Y');
                                            if ($notif['event_time']) {
                                                $time = new DateTime($notif['event_time']);
                                                echo ' at ' . $time->format('g:i A');
                                            }
                                            ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="notification-empty">No new notifications</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
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
            <?php if ($msg): ?>
                <div class="<?php echo $msgType === 'success' ? 'success-message' : 'error-message'; ?>" style="margin-bottom: 24px;">
                    <?php echo htmlspecialchars($msg); ?>
                </div>
            <?php endif; ?>
            
            <div class="welcome-section animate-fade-in-up">
                <h1 class="welcome-title">Welcome back, <?php echo htmlspecialchars($userName); ?>! 👋</h1>
                <p class="welcome-subtitle">Here's what's happening with your volunteer journey.</p>
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
                    <div class="stat-label">Registered Events</div>
                    <div class="stat-value"><?php echo $stats['registered_events']; ?></div>
    </div>

                <div class="stat-card animate-fade-in-up" style="animation-delay: 0.1s;">
                    <div class="stat-header">
                        <div class="stat-icon-wrapper pink">
                            <svg class="stat-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <svg class="trending-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="stat-label">Upcoming Tasks</div>
                    <div class="stat-value"><?php echo $stats['upcoming_tasks']; ?></div>
                </div>
                
                <div class="stat-card animate-fade-in-up" style="animation-delay: 0.2s;">
                    <div class="stat-header">
                        <div class="stat-icon-wrapper blue">
                            <svg class="stat-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                        <svg class="trending-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="stat-label">Volunteer Hours</div>
                    <div class="stat-value"><?php echo $stats['volunteer_hours']; ?></div>
                </div>
        </div>

            <!-- My Registered Events -->
            <?php if (count($registeredEvents) > 0): ?>
            <div class="animate-fade-in-up" style="animation-delay: 0.3s; margin-bottom: 48px;">
                <div class="section-header">
                    <h2 class="section-title">My Registered Events</h2>
                    <a href="/simple-event-portal/user/events.php?my_events=1" class="btn-outline">View All</a>
        </div>

                <div class="events-grid">
                    <?php foreach ($registeredEvents as $event): ?>
                    <div class="event-card">
                        <div class="event-image">
                            <div class="event-category">Environment</div>
                        </div>
                        <div class="event-content">
                            <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                            <div class="event-details">
                                <div class="event-detail">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <?php 
                                    $date = new DateTime($event['event_date']);
                                    echo $date->format('M d, Y');
                                    ?>
                                </div>
                                <div class="event-detail">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <?php 
                                    if ($event['event_time']) {
                                        $time = new DateTime($event['event_time']);
                                        echo $time->format('g:i A');
                                    } else {
                                        echo 'TBD';
                                    }
                                    ?>
                                </div>
                                <div class="event-detail">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <?php echo htmlspecialchars($event['venue'] ?? 'TBD'); ?>
                                </div>
                            </div>
                            <div class="event-footer">
                                <div class="event-volunteers">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    <?php echo (int)$event['reg_count']; ?> volunteers
                                </div>
                                <a href="/simple-event-portal/user/dashboard.php?unregister=<?php echo $event['id']; ?>" 
                                   class="event-link" 
                                   style="color: #ef4444;"
                                   onclick="return confirm('Are you sure you want to unregister from this event?');">
                                    Unregister
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Available Events -->
            <div class="animate-fade-in-up" style="animation-delay: 0.5s;">
                <div class="section-header">
                    <h2 class="section-title">Available Events</h2>
                    <div class="filter-section">
                        <svg class="filter-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; color: var(--text-gray);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        <select class="filter-select" id="categoryFilter" onchange="window.location.href='/simple-event-portal/user/dashboard.php?category=' + this.value + '&search=<?php echo urlencode($searchQuery); ?>'">
                            <option value="All" <?php echo $categoryFilter === 'All' ? 'selected' : ''; ?>>All</option>
                            <option value="Environment" <?php echo $categoryFilter === 'Environment' ? 'selected' : ''; ?>>Environment</option>
                            <option value="Food & Hunger" <?php echo $categoryFilter === 'Food & Hunger' ? 'selected' : ''; ?>>Food & Hunger</option>
                            <option value="Community" <?php echo $categoryFilter === 'Community' ? 'selected' : ''; ?>>Community</option>
                            <option value="Education" <?php echo $categoryFilter === 'Education' ? 'selected' : ''; ?>>Education</option>
                        </select>
                    </div>
        </div>

                <div class="events-grid">
                    <?php if (count($availableEvents) > 0): ?>
                        <?php foreach ($availableEvents as $event): ?>
                        <div class="event-card">
                            <div class="event-image">
                                <div class="event-category">Environment</div>
                            </div>
                            <div class="event-content">
                                <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                                <div class="event-details">
                                    <div class="event-detail">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <?php 
                                        $date = new DateTime($event['event_date']);
                                        echo $date->format('M d, Y');
                                        ?>
                                    </div>
                                    <div class="event-detail">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <?php 
                                        if ($event['event_time']) {
                                            $time = new DateTime($event['event_time']);
                                            echo $time->format('g:i A');
                                        } else {
                                            echo 'TBD';
                                        }
                                        ?>
                                    </div>
                                    <div class="event-detail">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <?php echo htmlspecialchars($event['venue'] ?? 'TBD'); ?>
                                    </div>
                                </div>
                                <div class="event-footer">
                                    <div class="event-volunteers">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                        <?php echo (int)$event['reg_count']; ?> volunteers needed
                                    </div>
                                    <a href="/simple-event-portal/user/events.php?register=<?php echo $event['id']; ?>" class="event-link">
                                        Register
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="event-card">
                            <div class="event-content">
                                <p>No available events at the moment. Check back later!</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
    </div>
</div>

    <script>
        // Notification dropdown toggle
        document.getElementById('notificationBtn').addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('notificationDropdown');
            const btn = document.getElementById('notificationBtn');
            if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
    </script>
</body>
</html>
