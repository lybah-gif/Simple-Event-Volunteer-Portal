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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign In - VolunteerHub</title>
    <link rel="stylesheet" href="/simple-event-portal/assets/css/main.css">
</head>
<body>
    <div class="auth-container">
        <!-- Left Side -->
        <div class="auth-left">
            <div class="auth-brand">
                <div class="logo-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <span style="color: white; font-weight: 700; font-size: 20px;">VolunteerHub</span>
            </div>
            
            <h1 class="auth-title">Welcome Back!</h1>
            <p class="auth-description">
                Continue your journey of making a difference. Log in to access your volunteer dashboard, manage your events, and connect with your community.
            </p>
            
            <ul class="auth-features">
                <li>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Track your volunteer hours</span>
                </li>
                <li>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Manage registered events</span>
                </li>
                <li>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Discover new opportunities</span>
                </li>
                <li>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Connect with organizers</span>
                </li>
            </ul>
        </div>
        
        <!-- Right Side -->
        <div class="auth-right">
            <div class="auth-form-container">
                <h2 class="auth-form-title">Sign In</h2>
                <p class="auth-form-subtitle">Enter your credentials to access your account</p>
                
                <?php if ($err): ?>
                    <div class="error-message"><?php echo htmlspecialchars($err); ?></div>
                <?php endif; ?>
                
                <form method="post" action="/simple-event-portal/login.php">
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <div class="form-input-wrapper">
                            <svg class="form-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <input type="email" name="email" class="form-input" placeholder="your@email.com" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="form-input-wrapper">
                            <svg class="form-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <input type="password" name="password" class="form-input" placeholder="••••••••" required>
                        </div>
                    </div>
                    
                    <div class="form-options">
                        <div class="checkbox-wrapper">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Remember me</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="form-submit">
                        Sign In
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </form>
                
                <div class="auth-link">
                    Don't have an account? <a href="/simple-event-portal/register.php">Sign up</a>
                </div>
                
                <a href="/simple-event-portal/" class="back-link">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>
