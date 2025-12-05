<?php
// login.php
require_once __DIR__ . '/config/db.php';

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') header('Location: /simple-event-portal/admin/dashboard.php');
    else header('Location: /simple-event-portal/user/dashboard.php');
    exit;
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = esc($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $err = 'Email and password required.';
    } else {
        $stmt = $mysqli->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            if ($password === $row['password']) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['user_role'] = $row['role'];
                if ($row['role'] === 'admin') header('Location: /simple-event-portal/admin/dashboard.php');
                else header('Location: /simple-event-portal/user/dashboard.php');
                exit;
            } else $err = 'Invalid credentials.';
        } else $err = 'Invalid credentials.';
    }
}

include __DIR__ . '/includes/header.php';
?>
<div class="container" style="max-width:640px;margin-top:28px">
  <div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center">
      <h2>Login</h2>
      <div class="muted">New? <a href="/simple-event-portal/register.php">Create an account</a></div>
    </div>
    <?php if ($err): ?><div class="card" style="background:#ffecec;color:#900"><?php echo $err; ?></div><?php endif; ?>

    <form method="post" onsubmit="return validateLogin()" style="margin-top:10px">
      <div class="form-row"><label>Email</label><input type="email" id="email" name="email" required></div>
      <div class="form-row"><label>Password</label><input type="password" id="password" name="password" required></div>
      <div class="actions" style="margin-top:12px">
        <button class="btn btn-primary" type="submit">Login</button>
        <a class="btn btn-outline" href="/simple-event-portal/">Back to Home</a>
      </div>
    </form>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
