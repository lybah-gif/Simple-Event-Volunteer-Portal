<?php

require_once __DIR__ . '/../config/db.php';

// Only allow logged-in users with role 'user'
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header('Location: /simple-event-portal/index.php');
    exit;
}

include __DIR__ . '/../includes/header.php';
?>


<style>
/* Page Background */
.dashboard-container {
    max-width: 1100px;
    margin: 40px auto;
    padding: 20px;
    background: #f5f7fb;
}

/* Header Section */
.dashboard-hero {
    background: white;
    padding: 40px;
    border-radius: 14px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.dashboard-hero h2 {
    font-size: 32px;
    color: #1a3d7c;
    margin-bottom: 10px;
}

.dashboard-hero p {
    font-size: 16px;
    color: #444;
}

.dashboard-hero .welcome {
    margin-top: 10px;
    font-weight: bold;
    color: #0a66c2;
}

/* Card Grid */
.dashboard-grid {
    margin-top: 30px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 25px;
}

.dash-card {
    background: white;
    padding: 30px 25px;
    border-radius: 12px;
    text-align: center;
    transition: 0.3s ease;
    box-shadow: 0 3px 12px rgba(0,0,0,0.07);
}

.dash-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.12);
}

.dash-card h3 {
    margin-bottom: 10px;
    color: #1a3d7c;
}

.dash-card p {
    color: #555;
    margin-bottom: 20px;
}

/* Buttons */
.btn-custom {
    padding: 10px 18px;
    background: #0a66c2;
    color: white;
    border-radius: 6px;
    text-decoration: none;
    display: inline-block;
    transition: 0.3s;
}

.btn-custom:hover {
    background: #004b9a;
}

/* Icon style */
.icon {
    font-size: 40px;
    color: #0a66c2;
    margin-bottom: 10px;
}
</style>


<div class="dashboard-container">

    <!-- Hero Header -->
    <div class="dashboard-hero">
        <h2>User Dashboard</h2>
        <p>Manage your volunteer activities, update your profile, and stay connected with community events.</p>
        <div class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
    </div>

    <!-- Grid Cards -->
    <div class="dashboard-grid">

        <div class="dash-card">
            <div class="icon">📅</div>
            <h3>Events</h3>
            <p>Explore all upcoming community events you can join.</p>
            <a class="btn-custom" href="/simple-event-portal/user/events.php">View Events</a>
        </div>

        <div class="dash-card">
            <div class="icon">👤</div>
            <h3>Profile</h3>
            <p>Update your personal details and volunteering preferences.</p>
            <a class="btn-custom" href="/simple-event-portal/user/profile_edit.php">Edit Profile</a>
        </div>

        <div class="dash-card">
            <div class="icon">📝</div>
            <h3>Tasks</h3>
            <p>Review your assigned tasks for upcoming volunteer events.</p>
            <a class="btn-custom" href="/simple-event-portal/user/tasks.php">My Tasks</a>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
