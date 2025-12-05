<?php
// admin/events_create.php
require_once __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') header('Location: /simple-event-portal/index.php');

$err = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = esc($_POST['title'] ?? '');
    $description = esc($_POST['description'] ?? '');
    $date = esc($_POST['date'] ?? '');
    $time = esc($_POST['time'] ?? '');
    $venue = esc($_POST['venue'] ?? '');

    if (!$title || !$date) $err = 'Title and date required.';
    else {
        $stmt = $mysqli->prepare("INSERT INTO events (title,description,event_date,event_time,venue,created_at) VALUES (?,?,?,?,?,NOW())");
        $stmt->bind_param('sssss', $title, $description, $date, $time, $venue);
        if ($stmt->execute()) {
            $success = 'Event created.';
        } else $err = 'DB error.';
    }
}
include __DIR__ . '/../includes/header.php';
?>
<div class="card">
  <h2>Create Event</h2>
  <?php if($err): ?><div class="card" style="background:#ffecec;color:#900"><?php echo $err; ?></div><?php endif; ?>
  <?php if($success): ?><div class="card" style="background:#e6ffed;color:#063"><?php echo $success; ?></div><?php endif; ?>
  <form method="post" onsubmit="return validateEventForm();">
    <div class="form-row"><label>Title</label><input id="title" name="title" type="text" required></div>
    <div class="form-row"><label>Description</label><textarea name="description"></textarea></div>
    <div class="form-row"><label>Date</label><input id="date" name="date" type="date" required></div>
    <div class="form-row"><label>Time</label><input name="time" type="time"></div>
    <div class="form-row"><label>Venue</label><input name="venue" type="text"></div>
    <div class="actions"><button class="btn btn-primary" type="submit">Create</button><a class="btn btn-muted" href="/simple-event-portal/admin/events_list.php">Back</a></div>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
