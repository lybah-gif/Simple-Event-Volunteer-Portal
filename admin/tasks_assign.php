<?php
// admin/tasks_assign.php
require_once __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') header('Location: /simple-event-portal/index.php');

$err=$success='';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = esc($_POST['task_title'] ?? '');
    $description = esc($_POST['task_description'] ?? '');
    $volunteer_id = intval($_POST['volunteer_id'] ?? 0);
    $event_id = intval($_POST['event_id'] ?? 0);

    if (!$title || !$volunteer_id) $err='Title and Volunteer required.';
    else {
        $stmt = $mysqli->prepare("INSERT INTO tasks (title,description,volunteer_id,event_id,status,created_at) VALUES (?,?,?,?, 'assigned', NOW())");
        $stmt->bind_param('ssii', $title, $description, $volunteer_id, $event_id);
        if ($stmt->execute()) $success='Task assigned.';
        else $err='DB error.';
    }
}

$vols = $mysqli->query("SELECT id,name FROM volunteers WHERE status='approved'");
$events = $mysqli->query("SELECT id,title FROM events ORDER BY event_date DESC");
include __DIR__ . '/../includes/header.php';
?>
<div class="card">
  <h2>Assign Task</h2>
  <?php if($err): ?><div style="background:#ffecec;color:#900" class="card"><?php echo $err;?></div><?php endif; ?>
  <?php if($success): ?><div style="background:#e6ffed;color:#063" class="card"><?php echo $success;?></div><?php endif; ?>

  <form method="post" onsubmit="return validateTaskForm();">
    <div class="form-row"><label>Task Title</label><input id="task_title" name="task_title" type="text" required></div>
    <div class="form-row"><label>Description</label><textarea name="task_description"></textarea></div>
    <div class="form-row"><label>Volunteer</label>
      <select id="volunteer_id" name="volunteer_id" required>
        <option value="">Select volunteer</option>
        <?php while($v = $vols->fetch_assoc()): ?>
          <option value="<?php echo $v['id']; ?>"><?php echo htmlspecialchars($v['name']); ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="form-row"><label>Event (optional)</label>
      <select name="event_id">
        <option value="0">-- none --</option>
        <?php while($e = $events->fetch_assoc()): ?>
          <option value="<?php echo $e['id']; ?>"><?php echo htmlspecialchars($e['title']); ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="actions"><button class="btn btn-primary" type="submit">Assign</button><a class="btn btn-muted" href="/simple-event-portal/admin/tasks_list.php">View Tasks</a></div>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
