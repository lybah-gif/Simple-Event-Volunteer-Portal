<?php
// user/events.php
require_once __DIR__ . '/../config/db.php';

// Allow viewing events without login
$loggedIn = isset($_SESSION['user_id']);

// Handle event registration — only if logged in
if ($loggedIn && isset($_GET['register'])) {

    $event_id = intval($_GET['register']);

    // Check if user already registered
    $stmt = $mysqli->prepare("SELECT id FROM event_registrations WHERE user_id=? AND event_id=?");
    $stmt->bind_param('ii', $_SESSION['user_id'], $event_id);
    $stmt->execute(); 
    $res = $stmt->get_result();

    if (!$res->fetch_assoc()) {
        $stmt = $mysqli->prepare("INSERT INTO event_registrations (user_id,event_id,created_at) VALUES (?,?,NOW())");
        $stmt->bind_param('ii', $_SESSION['user_id'], $event_id);
        $stmt->execute();
    }

    header("Location: /simple-event-portal/user/events.php");
    exit();
}

// Fetch events
$res = $mysqli->query("
    SELECT e.*, 
        (SELECT COUNT(*) FROM event_registrations r WHERE r.event_id = e.id) AS reg_count 
    FROM events e 
    ORDER BY event_date ASC
");

include __DIR__ . '/../includes/header.php';
?>

<style>
.events-wrapper {
    max-width: 1100px;
    margin: 40px auto;
    padding: 20px;
}
.events-header {
    text-align: center;
    margin-bottom: 30px;
}
.events-header h2 {
    font-size: 32px;
    color: #1a3d7c;
}
.events-header p {
    font-size: 16px;
    color: #444;
}
.events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
}
.event-card {
    background: white;
    padding: 22px 20px;
    border-radius: 14px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: 0.25s ease;
}
.event-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 22px rgba(0,0,0,0.12);
}
.event-title {
    font-size: 20px;
    font-weight: bold;
    color: #1a3d7c;
    margin-bottom: 8px;
}
.event-info {
    color: #555;
    margin-bottom: 6px;
}
.btn-register {
    padding: 10px 16px;
    background: #0a66c2;
    color: white;
    border-radius: 6px;
    text-decoration: none;
    display: inline-block;
    margin-top: 10px;
    transition: 0.3s;
}
.btn-register:hover {
    background: #004b9a;
}
</style>

<div class="events-wrapper">

    <div class="events-header">
        <h2>Upcoming Events</h2>
        <p>Find community events and volunteer opportunities you can join.</p>
    </div>

    <div class="events-grid">
        <?php while ($e = $res->fetch_assoc()): ?>
            <div class="event-card">

                <div class="event-title">
                    <?php echo htmlspecialchars($e['title']); ?>
                </div>

                <div class="event-info">
                    📅 <strong>Date:</strong> <?php echo htmlspecialchars($e['event_date']); ?>
                </div>

                <div class="event-info">
                    📍 <strong>Venue:</strong> <?php echo htmlspecialchars($e['venue']); ?>
                </div>

                <div class="event-info">
                    👥 <strong>Registered:</strong> <?php echo (int)$e['reg_count']; ?>
                </div>

                <?php if ($loggedIn): ?>
                    <!-- Logged-in users can register -->
                    <a class="btn-register" href="/simple-event-portal/user/events.php?register=<?php echo $e['id']; ?>">
                        Register
                    </a>
                <?php else: ?>
                    <!-- Guests: redirect to login -->
                    <a class="btn-register" href="/simple-event-portal/login.php">
                        Login to Register
                    </a>
                <?php endif; ?>

            </div>
        <?php endwhile; ?>
    </div>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
