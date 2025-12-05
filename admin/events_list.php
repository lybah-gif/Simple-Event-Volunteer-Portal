<?php
// admin/events_list.php
require_once __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') header('Location: /simple-event-portal/index.php');

include __DIR__ . '/../includes/header.php';

$res = $mysqli->query("SELECT id,title,event_date,event_time,venue FROM events ORDER BY event_date DESC");
?>
<div class="card">
  <h2>Events</h2>
  <table class="table">
    <thead><tr><th>Title</th><th>Date</th><th>Time</th><th>Venue</th><th>Actions</th></tr></thead>
    <tbody>
      <?php while($row = $res->fetch_assoc()): ?>
      <tr>
        <td><?php echo htmlspecialchars($row['title']); ?></td>
        <td><?php echo htmlspecialchars($row['event_date']); ?></td>
        <td><?php echo htmlspecialchars($row['event_time']); ?></td>
        <td><?php echo htmlspecialchars($row['venue']); ?></td>
        <td class="actions">
          <a class="btn btn-muted" href="/simple-event-portal/admin/events_edit.php?id=<?php echo $row['id']; ?>">Edit</a>
          <a class="btn btn-primary confirm-delete" href="/simple-event-portal/admin/events_edit.php?delete=<?php echo $row['id']; ?>">Delete</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
