<?php
// user/tasks.php
require_once __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user_id'])) header('Location: /simple-event-portal/index.php');

// show tasks assigned to volunteers that match the logged-in user by email (best-effort) or to volunteers table if linked to users (simpler here)
$user_email = '';
$stmt = $mysqli->prepare("SELECT email FROM users WHERE id=?");
$stmt->bind_param('i', $_SESSION['user_id']); $stmt->execute(); $res = $stmt->get_result(); if ($u = $res->fetch_assoc()) $user_email = $u['email'];

// Attempt to find volunteer id by email
$vol_id = 0;
if ($user_email) {
    $stmt = $mysqli->prepare("SELECT id FROM volunteers WHERE email=?");
    $stmt->bind_param('s',$user_email); $stmt->execute(); $rv = $stmt->get_result(); if ($r = $rv->fetch_assoc()) $vol_id = $r['id'];
}

if (isset($_GET['update_status']) && $vol_id) {
    $tid = intval($_GET['update_status']);
    $status = esc($_GET['status'] ?? 'in_progress');
    $stmt = $mysqli->prepare("UPDATE tasks SET status=? WHERE id=? AND volunteer_id=?");
    $stmt->bind_param('sii', $status, $tid, $vol_id); $stmt->execute();
}

if ($vol_id) {
    $stmt = $mysqli->prepare("SELECT t.*, e.title as event_title FROM tasks t LEFT JOIN events e ON t.event_id=e.id WHERE t.volunteer_id=? ORDER BY t.created_at DESC");
    $stmt->bind_param('i',$vol_id); $stmt->execute(); $tasks = $stmt->get_result();
} else {
    // no volunteer profile linked - show none
    $tasks = false;
}

include __DIR__ . '/../includes/header.php';
?>
<div class="card">
  <h2>My Tasks</h2>
  <?php if (!$tasks || $tasks->num_rows === 0): ?>
    <p>No tasks assigned to your account.</p>
  <?php else: ?>
  <table class="table">
    <thead><tr><th>Title</th><th>Event</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
      <?php while($t = $tasks->fetch_assoc()): ?>
      <tr>
        <td><?php echo htmlspecialchars($t['title']); ?></td>
        <td><?php echo htmlspecialchars($t['event_title']); ?></td>
        <td><?php echo htmlspecialchars($t['status']); ?></td>
        <td class="actions">
          <?php if($t['status'] !== 'completed'): ?>
          <a class="btn btn-primary" href="/simple-event-portal/user/tasks.php?update_status=<?php echo $t['id']; ?>&status=in_progress">In Progress</a>
          <a class="btn btn-muted" href="/simple-event-portal/user/tasks.php?update_status=<?php echo $t['id']; ?>&status=completed">Complete</a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
