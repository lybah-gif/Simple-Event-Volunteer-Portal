<?php  
// admin/dashboard.php
require_once __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /simple-event-portal/index.php');
}

include __DIR__ . '/../includes/header.php';
?>

<style>
/* Main Container */
.admin-dashboard-container {
    max-width: 1100px;
    margin: 40px auto;
    padding: 20px;
    background: #f5f7fb;
}

/* Header Section */
.admin-hero {
    background: white;
    padding: 40px;
    border-radius: 14px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.admin-hero h2 {
    font-size: 32px;
    color: #1a3d7c;
    margin-bottom: 10px;
}

.admin-hero p {
    font-size: 16px;
    color: #444;
}

.admin-hero .welcome {
    margin-top: 10px;
    font-weight: bold;
    color: #0a66c2;
}

/* Card Grid */
.admin-grid {
    margin-top: 30px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 25px;
}

.admin-card {
    background: white;
    padding: 30px 25px;
    border-radius: 12px;
    text-align: center;
    transition: 0.3s ease;
    box-shadow: 0 3px 12px rgba(0,0,0,0.07);
}

.admin-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.12);
}

.admin-card h3 {
    margin-bottom: 10px;
    color: #1a3d7c;
}

.admin-card p {
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
    margin: 5px;
}

.btn-custom:hover {
    background: #004b9a;
}

/* Icon style */
.icon {
    font-size: 42px;
    color: #0a66c2;
    margin-bottom: 12px;
}
</style>


<div class="admin-dashboard-container">

    <!-- Hero Header -->
    <div class="admin-hero">
        <h2>Admin Dashboard</h2>
        <p>Manage events, volunteers, and tasks across the community portal.</p>
        <div class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
    </div>

    <!-- Grid Cards -->
    <div class="admin-grid">

        <!-- Events -->
        <div class="admin-card">
            <div class="icon">📅</div>
            <h3>Events</h3>
            <p>Create, update, and manage all community events.</p>

            <a class="btn-custom" href="/simple-event-portal/admin/events_create.php">Create Event</a>
            <a class="btn-custom" href="/simple-event-portal/admin/events_list.php">View Events</a>
        </div>

        <!-- Volunteers -->
        <div class="admin-card">
            <div class="icon">🧑‍🤝‍🧑</div>
            <h3>Volunteers</h3>
            <p>Add new volunteers and manage volunteer records.</p>

            <a class="btn-custom" href="/simple-event-portal/admin/volunteers_add.php">Add Volunteer</a>
            <a class="btn-custom" href="/simple-event-portal/admin/volunteers_list.php">View Volunteers</a>
        </div>

        <!-- Tasks -->
        <div class="admin-card">
            <div class="icon">🗂️</div>
            <h3>Tasks</h3>
            <p>Assign tasks to volunteers and manage activity progress.</p>

            <a class="btn-custom" href="/simple-event-portal/admin/tasks_assign.php">Assign Task</a>
            <a class="btn-custom" href="/simple-event-portal/admin/tasks_list.php">View Tasks</a>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
