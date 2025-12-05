<?php
// user/events.php
require_once __DIR__ . '/../config/db.php';

// Allow viewing events without login
$loggedIn = isset($_SESSION['user_id']);
$userId = $loggedIn ? $_SESSION['user_id'] : 0;

// Check if this is "My Events" page (only registered events)
$myEventsOnly = isset($_GET['my_events']) && $loggedIn;

// Handle event registration — only if logged in
if ($loggedIn && isset($_GET['register'])) {
    $event_id = intval($_GET['register']);
    
    // Check if user already registered
    $stmt = $mysqli->prepare("SELECT id FROM event_registrations WHERE user_id=? AND event_id=?");
    $stmt->bind_param('ii', $userId, $event_id);
    $stmt->execute(); 
    $res = $stmt->get_result();
    
    if ($res->fetch_assoc()) {
        // Already registered - redirect to dashboard with error message
        header("Location: /simple-event-portal/user/dashboard.php?msg=" . urlencode('You are already registered for this event.') . "&type=error");
        exit();
    } else {
        $stmt = $mysqli->prepare("INSERT INTO event_registrations (user_id,event_id,created_at) VALUES (?,?,NOW())");
        $stmt->bind_param('ii', $userId, $event_id);
        if ($stmt->execute()) {
            // Success - redirect to dashboard with success message
            header("Location: /simple-event-portal/user/dashboard.php?msg=" . urlencode('Successfully registered for the event!') . "&type=success");
            exit();
        } else {
            header("Location: /simple-event-portal/user/dashboard.php?msg=" . urlencode('Registration failed. Please try again.') . "&type=error");
            exit();
        }
    }
}

// Handle event unregistration
if ($loggedIn && isset($_GET['unregister'])) {
    $event_id = intval($_GET['unregister']);
    $stmt = $mysqli->prepare("DELETE FROM event_registrations WHERE user_id=? AND event_id=?");
    $stmt->bind_param('ii', $userId, $event_id);
    if ($stmt->execute()) {
        // Redirect based on where user came from
        if (isset($_GET['my_events'])) {
            header("Location: /simple-event-portal/user/events.php?my_events=1&msg=" . urlencode('Successfully unregistered from the event.') . "&type=success");
        } else {
            header("Location: /simple-event-portal/user/dashboard.php?msg=" . urlencode('Successfully unregistered from the event.') . "&type=success");
        }
        exit();
    } else {
        if (isset($_GET['my_events'])) {
            header("Location: /simple-event-portal/user/events.php?my_events=1&msg=" . urlencode('Failed to unregister. Please try again.') . "&type=error");
        } else {
            header("Location: /simple-event-portal/user/dashboard.php?msg=" . urlencode('Failed to unregister. Please try again.') . "&type=error");
        }
        exit();
    }
}

$msg = '';
$msgType = '';
if (isset($_GET['msg']) && isset($_GET['type'])) {
    $msg = urldecode($_GET['msg']);
    $msgType = $_GET['type'];
}

// Fetch events based on mode
if ($myEventsOnly) {
    // Show only registered events for logged-in user
    $res = $mysqli->prepare("
        SELECT e.*, 
            (SELECT COUNT(*) FROM event_registrations r WHERE r.event_id = e.id) AS reg_count
        FROM events e
        INNER JOIN event_registrations er ON e.id = er.event_id
        WHERE er.user_id = ?
        ORDER BY e.event_date ASC
    ");
    $res->bind_param('i', $userId);
    $res->execute();
    $result = $res->get_result();
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
} else {
    // Show all upcoming events
    $res = $mysqli->query("
        SELECT e.*, 
            (SELECT COUNT(*) FROM event_registrations r WHERE r.event_id = e.id) AS reg_count 
        FROM events e 
        WHERE e.event_date >= CURDATE()
        ORDER BY e.event_date ASC
    ");
    $events = [];
    while ($row = $res->fetch_assoc()) {
        $events[] = $row;
    }
}

// Get registered event IDs for logged-in user
$registeredEventIds = [];
if ($loggedIn) {
    $stmt = $mysqli->prepare("SELECT event_id FROM event_registrations WHERE user_id = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $registeredEventIds[] = $row['event_id'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $myEventsOnly ? 'My Events' : 'Events'; ?> - VolunteerHub</title>
    <link rel="stylesheet" href="/simple-event-portal/assets/css/main.css">
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="header-content">
            <a href="/simple-event-portal/" class="logo">
                <div class="logo-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <span>VolunteerHub</span>
            </a>
            
            <ul class="nav-links">
                <li><a href="/simple-event-portal/">Home</a></li>
                <li><a href="/simple-event-portal/user/events.php" class="active">Events</a></li>
                <li><a href="/simple-event-portal/#about-us">About</a></li>
            </ul>
            
            <div class="auth-buttons">
                <?php if ($loggedIn): ?>
                    <a href="/simple-event-portal/user/dashboard.php" class="btn btn-outline">Dashboard</a>
                    <a href="/simple-event-portal/logout.php" class="btn btn-gradient">Logout</a>
                <?php else: ?>
                    <a href="/simple-event-portal/login.php" class="btn btn-outline">Login</a>
                    <a href="/simple-event-portal/register.php" class="btn btn-gradient">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Events Section -->
    <section class="section" style="padding-top: 60px;">
        <div class="section-header animate-fade-in-up">
            <h2 class="section-title"><?php echo $myEventsOnly ? 'My Registered Events' : 'Upcoming Events'; ?></h2>
            <p class="section-description">
                <?php if ($myEventsOnly): ?>
                    View and manage all events you've registered for.
                <?php else: ?>
                    Discover opportunities to make a difference. Browse our latest volunteer events and find the perfect match for your passion.
                <?php endif; ?>
            </p>
        </div>
        
        <?php if ($msg): ?>
            <div class="<?php echo $msgType === 'success' ? 'success-message' : 'error-message'; ?>" style="max-width: 1200px; margin: 0 auto 24px;">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!$myEventsOnly && $loggedIn): ?>
            <div style="text-align: center; margin-bottom: 24px;">
                <a href="/simple-event-portal/user/events.php?my_events=1" class="btn btn-outline">View My Events</a>
            </div>
        <?php endif; ?>
        
        <div class="events-grid">
            <?php if (count($events) > 0): ?>
                <?php foreach ($events as $e): ?>
                    <?php $isRegistered = in_array($e['id'], $registeredEventIds); ?>
                    <div class="event-card animate-fade-in-up">
                        <div class="event-image">
                            <div class="event-category">Environment</div>
                        </div>
                        <div class="event-content">
                            <h3 class="event-title"><?php echo htmlspecialchars($e['title']); ?></h3>
                            <div class="event-details">
                                <div class="event-detail">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <?php 
                                    $date = new DateTime($e['event_date']);
                                    echo $date->format('M d, Y');
                                    ?>
                                </div>
                                <div class="event-detail">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <?php 
                                    if ($e['event_time']) {
                                        $time = new DateTime($e['event_time']);
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
                                    <?php echo htmlspecialchars($e['venue'] ?? 'TBD'); ?>
                                </div>
                                <div class="event-detail">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    <?php echo (int)$e['reg_count']; ?> volunteers needed
                                </div>
                            </div>
                            <div class="event-footer">
                                <div class="event-volunteers"></div>
                                <?php if ($loggedIn): ?>
                                    <?php if ($isRegistered || $myEventsOnly): ?>
                                        <a href="/simple-event-portal/user/events.php?unregister=<?php echo $e['id']; ?><?php echo $myEventsOnly ? '&my_events=1' : ''; ?>" 
                                           class="event-link" 
                                           style="color: #ef4444;"
                                           onclick="return confirm('Are you sure you want to unregister from this event?');">
                                            Unregister
                                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </a>
                                    <?php else: ?>
                                        <a href="/simple-event-portal/user/events.php?register=<?php echo $e['id']; ?>" class="event-link">
                                            Register
                                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <a href="/simple-event-portal/login.php" class="event-link">
                                        Login to Register
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="event-card">
                    <div class="event-content">
                        <p><?php echo $myEventsOnly ? 'You have not registered for any events yet.' : 'No upcoming events at the moment. Check back soon!'; ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-brand">
                <div class="footer-logo">
                    <div class="logo-icon">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <span>VolunteerHub</span>
                </div>
                <p class="footer-description">
                    Connecting passionate volunteers with meaningful opportunities to make a difference.
                </p>
            </div>
            
            <div class="footer-column">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="/simple-event-portal/#about-us">About Us</a></li>
                    <li><a href="/simple-event-portal/user/events.php">Browse Events</a></li>
                    <li><a href="/simple-event-portal/#how-it-works">How It Works</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h3>For Organizers</h3>
                <ul class="footer-links">
                    <li><a href="/simple-event-portal/login.php">Create Event</a></li>
                    <li><a href="/simple-event-portal/login.php">Manage Events</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h3>Contact Us</h3>
                <div class="contact-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span>hello@volunteerhub.org</span>
                </div>
                <div class="contact-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <span>(555) 123-4567</span>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> VolunteerHub. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
