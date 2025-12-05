<?php
// admin/tasks_list.php
require_once __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') header('Location: /simple-event-portal/index.php');

if (isset($_GET['complete'])) {
    $tid = intval($_GET['complete']);
    $stmt = $mysqli->prepare("UPDATE tasks SET status='completed' WHERE id=?");
    $stmt->bind_param('i',$tid); $stmt->execute();
}

$res = $mysqli->query("SELECT t.*, v.name as volunteer_name, e.title as event_title FROM tasks t LEFT JOIN volunteers v ON t.volunteer_id = v.id LEFT JOIN events e ON t.event_id = e.id ORDER BY t.created_at DESC");
include __DIR__ . '/../includes/header.php';
?>
<div class="card">
  <h2>Tasks</h2>
  <table class="table">
    <thead><tr><th>Title</th><th>Volunteer</th><th>Event</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
      <?php while($t = $res->fetch_assoc()): ?>
      <tr>
        <td><?php echo htmlspecialchars($t['title']); ?></td>
        <td><?php echo htmlspecialchars($t['volunteer_name']); ?></td>
        <td><?php echo htmlspecialchars($t['event_title']); ?></td>
        <td><?php echo htmlspecialchars($t['status']); ?></td>
        <td class="actions">
          <?php if($t['status'] !== 'completed'): ?>
            <a class="btn btn-primary" href="/simple-event-portal/admin/tasks_list.php?complete=<?php echo $t['id']; ?>">Mark Completed</a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
