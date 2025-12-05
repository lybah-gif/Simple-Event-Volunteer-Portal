<?php
// admin/events_edit.php
require_once __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') header('Location: /simple-event-portal/index.php');

$id = intval($_GET['id'] ?? 0);
if (isset($_GET['delete'])) {
    $delid = intval($_GET['delete']);
    $stmt = $mysqli->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param('i',$delid);
    $stmt->execute();
    header('Location: /simple-event-portal/admin/events_list.php'); exit;
}

$err = $success = '';
if ($_SERVER['REQUEST_METHOD']==='POST' && $id) {
    $title = esc($_POST['title'] ?? '');
    $description = esc($_POST['description'] ?? '');
    $date = esc($_POST['date'] ?? '');
    $time = esc($_POST['time'] ?? '');
    $venue = esc($_POST['venue'] ?? '');

    if (!$title || !$date) $err='Title & date required.';
    else {
        $stmt = $mysqli->prepare("UPDATE events SET title=?, description=?, event_date=?, event_time=?, venue=? WHERE id=?");
        $stmt->bind_param('sssssi', $title, $description, $date, $time, $venue, $id);
        if ($stmt->execute()) $success='Updated.';
        else $err='Update failed.';
    }
}

$event = null;
if ($id) {
    $stmt = $mysqli->prepare("SELECT * FROM events WHERE id=?");
    $stmt->bind_param('i',$id); $stmt->execute(); $res = $stmt->get_result(); $event = $res->fetch_assoc();
}
include __DIR__ . '/../includes/header.php';
?>
<div class="card">
  <h2><?php echo $event ? 'Edit Event' : 'Create Event'; ?></h2>
  <?php if($err): ?><div class="card" style="background:#ffecec;color:#900"><?php echo $err; ?></div><?php endif; ?>
  <?php if($success): ?><div class="card" style="background:#e6ffed;color:#063"><?php echo $success; ?></div><?php endif; ?>

  <?php if($event): ?>
  <form method="post" onsubmit="return validateEventForm();">
    <div class="form-row"><label>Title</label><input id="title" name="title" type="text" value="<?php echo htmlspecialchars($event['title']); ?>" required></div>
    <div class="form-row"><label>Description</label><textarea name="description"><?php echo htmlspecialchars($event['description']); ?></textarea></div>
    <div class="form-row"><label>Date</label><input id="date" name="date" type="date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required></div>
    <div class="form-row"><label>Time</label><input name="time" type="time" value="<?php echo htmlspecialchars($event['event_time']); ?>"></div>
    <div class="form-row"><label>Venue</label><input name="venue" type="text" value="<?php echo htmlspecialchars($event['venue']); ?>"></div>
    <div class="actions"><button class="btn btn-primary" type="submit">Update</button><a class="btn btn-muted" href="/simple-event-portal/admin/events_list.php">Back</a></div>
  </form>
  <?php else: ?>
    <p>Event not found.</p>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
