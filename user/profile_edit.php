<?php
// user/profile_edit.php
require_once __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user_id'])) header('Location: /simple-event-portal/index.php');

$err = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = esc($_POST['name'] ?? '');
    $email = esc($_POST['email'] ?? '');

    if (!$name || !$email) $err='Name & Email required.';
    else {
        $stmt = $mysqli->prepare("UPDATE users SET name=?, email=? WHERE id=?");
        $stmt->bind_param('ssi', $name, $email, $_SESSION['user_id']);
        if ($stmt->execute()) {
            $_SESSION['user_name'] = $name;
            $success='Profile updated.';
        } else $err='DB error.';
    }
}

$stmt = $mysqli->prepare("SELECT name,email FROM users WHERE id=?");
$stmt->bind_param('i', $_SESSION['user_id']); $stmt->execute(); $res = $stmt->get_result(); $user = $res->fetch_assoc();

include __DIR__ . '/../includes/header.php';
?>
<div class="card">
  <h2>Edit Profile</h2>
  <?php if($err): ?><div style="background:#ffecec;color:#900" class="card"><?php echo $err;?></div><?php endif; ?>
  <?php if($success): ?><div style="background:#e6ffed;color:#063" class="card"><?php echo $success;?></div><?php endif; ?>

  <form method="post">
    <div class="form-row"><label>Name</label><input name="name" type="text" value="<?php echo htmlspecialchars($user['name']); ?>" required></div>
    <div class="form-row"><label>Email</label><input name="email" type="email" value="<?php echo htmlspecialchars($user['email']); ?>" required></div>
    <div class="actions"><button class="btn btn-primary" type="submit">Save</button><a class="btn btn-muted" href="/simple-event-portal/user/dashboard.php">Back</a></div>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
