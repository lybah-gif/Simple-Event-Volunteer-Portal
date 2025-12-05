<?php
// admin/volunteers_list.php
require_once __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') header('Location: /simple-event-portal/index.php');

if (isset($_GET['approve'])) {
    $vid = intval($_GET['approve']);
    $stmt = $mysqli->prepare("UPDATE volunteers SET status='approved' WHERE id=?");
    $stmt->bind_param('i',$vid); $stmt->execute();
}
if (isset($_GET['reject'])) {
    $vid = intval($_GET['reject']);
    $stmt = $mysqli->prepare("UPDATE volunteers SET status='rejected' WHERE id=?");
    $stmt->bind_param('i',$vid); $stmt->execute();
}

$res = $mysqli->query("SELECT * FROM volunteers ORDER BY created_at DESC");
include __DIR__ . '/../includes/header.php';
?>
<div class="card">
  <h2>Volunteers</h2>
  <table class="table">
    <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
      <?php while($v = $res->fetch_assoc()): ?>
      <tr>
        <td><?php echo htmlspecialchars($v['name']); ?></td>
        <td><?php echo htmlspecialchars($v['email']); ?></td>
        <td><?php echo htmlspecialchars($v['phone']); ?></td>
        <td><?php echo htmlspecialchars($v['status']); ?></td>
        <td class="actions">
          <a class="btn btn-primary" href="/simple-event-portal/admin/volunteers_list.php?approve=<?php echo $v['id']; ?>">Approve</a>
          <a class="btn btn-muted" href="/simple-event-portal/admin/volunteers_list.php?reject=<?php echo $v['id']; ?>">Reject</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
