<?php
// includes/navbar.php
if (session_status() === PHP_SESSION_NONE) session_start();
$role = $_SESSION['user_role'] ?? null;
?>
<nav class="navbar">
  <div class="brand"><a href="/simple-event-portal/">Event Volunteer Portal</a></div>
  <ul class="nav-links">
    <?php if(!isset($_SESSION['user_id'])): ?>
      <li><a href="/simple-event-portal/index.php">Login</a></li>
      <li><a href="/simple-event-portal/register.php">Register</a></li>
    <?php else: ?>
      <?php if($role === 'admin'): ?>
        <li><a href="/simple-event-portal/admin/dashboard.php">Admin Dashboard</a></li>
      <?php else: ?>
        <li><a href="/simple-event-portal/user/dashboard.php">Dashboard</a></li>
      <?php endif; ?>
      <li><a href="/simple-event-portal/logout.php">Logout</a></li>
    <?php endif; ?>
  </ul>
</nav>
