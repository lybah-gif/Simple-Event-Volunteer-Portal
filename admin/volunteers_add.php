<?php
// admin/volunteers_add.php
require_once __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') header('Location: /simple-event-portal/index.php');

$err=$success='';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = esc($_POST['name'] ?? '');
    $email = esc($_POST['email'] ?? '');
    $phone = esc($_POST['phone'] ?? '');
    $status = 'pending'; // admin adds -> pending or approved?
    if (!$name || !$email) $err='Name & Email required.';
    else {
        $stmt = $mysqli->prepare("INSERT INTO volunteers (name,email,phone,status,created_at) VALUES (?,?,?,?,NOW())");
        $stmt->bind_param('ssss',$name,$email,$phone,$status);
        if ($stmt->execute()) $success='Volunteer added.';
        else $err='DB error.';
    }
}
include __DIR__ . '/../includes/header.php';
?>
<div class="card">
  <h2>Add Volunteer</h2>
  <?php if($err): ?><div style="background:#ffecec;color:#900" class="card"><?php echo $err;?></div><?php endif; ?>
  <?php if($success): ?><div style="background:#e6ffed;color:#063" class="card"><?php echo $success;?></div><?php endif; ?>
  <form method="post">
    <div class="form-row"><label>Name</label><input name="name" type="text" required></div>
    <div class="form-row"><label>Email</label><input name="email" type="email" required></div>
    <div class="form-row"><label>Phone</label><input name="phone" type="text"></div>
    <div class="actions"><button class="btn btn-primary" type="submit">Add</button><a class="btn btn-muted" href="/simple-event-portal/admin/volunteers_list.php">Back</a></div>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
