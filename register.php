<?php
// register.php
require_once __DIR__.'/config/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: /simple-event-portal/user/dashboard.php'); 
    exit;
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = esc($_POST['name'] ?? '');
    $email = esc($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$name || !$email || !$password) $err = 'All fields required.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $err = 'Invalid email.';
    elseif ($password !== $confirm) $err = 'Passwords do not match.';
    elseif (strlen($password) < 6) $err = 'Password must be at least 6 chars.';
    else {
        // check existing
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param('s', $email); 
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->fetch_assoc()) $err = 'Email already registered.';
        else {
            $role = 'user';
            $stmt = $mysqli->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?,?,?,?,NOW())");
            $stmt->bind_param('ssss', $name, $email, $password, $role);
            if ($stmt->execute()) {
                header('Location: /simple-event-portal/index.php'); 
                exit;
            } else $err = 'Registration failed.';
        }
    }
}

include __DIR__.'/includes/header.php';
?>
<div class="card">
  <h2>Register</h2>
  <?php if($err): ?><div class="card" style="background:#ffecec;color:#900"><?php echo $err; ?></div><?php endif; ?>
  <form method="post" onsubmit="return validateRegister();">
    <div class="form-row"><label>Name</label><input type="text" id="name" name="name" required></div>
    <div class="form-row"><label>Email</label><input type="email" id="email" name="email" required></div>
    <div class="form-row"><label>Password</label><input type="password" id="password" name="password" required></div>
    <div class="form-row"><label>Confirm Password</label><input type="password" id="confirm_password" name="confirm_password" required></div>
    <div class="actions">
      <button class="btn btn-primary" type="submit">Register</button>
      <a class="btn btn-muted" href="/simple-event-portal/index.php">Back to Login</a>
    </div>
  </form>
</div>
<?php include __DIR__.'/includes/footer.php'; ?>
