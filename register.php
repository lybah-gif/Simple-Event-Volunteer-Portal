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
    $phone = esc($_POST['phone'] ?? '');
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
                // Also add to volunteers table if phone provided
                if ($phone) {
                    $userId = $stmt->insert_id;
                    $volStmt = $mysqli->prepare("INSERT INTO volunteers (name, email, phone, status, created_at) VALUES (?,?,?,?,NOW())");
                    $status = 'pending';
                    $volStmt->bind_param('ssss', $name, $email, $phone, $status);
                    $volStmt->execute();
                }
                header('Location: /simple-event-portal/login.php'); 
                exit;
            } else $err = 'Registration failed.';
        }
    }
}

// Get stats for display
$stats = [
    'volunteers' => $mysqli->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'")->fetch_assoc()['count'] ?? 0,
    'events' => $mysqli->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'] ?? 0,
    'organizations' => 50,
    'hours' => 100000
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create Account - VolunteerHub</title>
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
            
            <h1 class="auth-title">Join Our Community!</h1>
            <p class="auth-description">
                Start your volunteering journey today. Create an account to discover meaningful opportunities, track your impact, and connect with like-minded people in your community.
            </p>
            
            <div class="auth-stats">
                <div class="auth-stat">
                    <div class="auth-stat-value"><?php echo number_format($stats['volunteers']); ?>+</div>
                    <div class="auth-stat-label">Active Volunteers</div>
                </div>
                <div class="auth-stat">
                    <div class="auth-stat-value"><?php echo number_format($stats['events']); ?>+</div>
                    <div class="auth-stat-label">Events Hosted</div>
                </div>
                <div class="auth-stat">
                    <div class="auth-stat-value"><?php echo $stats['organizations']; ?>+</div>
                    <div class="auth-stat-label">Organizations</div>
                </div>
                <div class="auth-stat">
                    <div class="auth-stat-value"><?php echo number_format($stats['hours']); ?>+</div>
                    <div class="auth-stat-label">Hours Contributed</div>
                </div>
            </div>
            
            <ul class="auth-features">
                <li>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Free to join, forever</span>
                </li>
                <li>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Easy event registration</span>
                </li>
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
                    <span>Earn recognition badges</span>
                </li>
            </ul>
        </div>
        
        <!-- Right Side -->
        <div class="auth-right">
            <div class="auth-form-container">
                <h2 class="auth-form-title">Create Account</h2>
                <p class="auth-form-subtitle">Fill in your details to get started</p>
                
                <?php if ($err): ?>
                    <div class="error-message"><?php echo htmlspecialchars($err); ?></div>
                <?php endif; ?>
                
                <form method="post" action="/simple-event-portal/register.php">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <div class="form-input-wrapper">
                            <svg class="form-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <input type="text" name="name" class="form-input" placeholder="John Doe" required>
                        </div>
                    </div>
                    
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
                        <label class="form-label">Phone Number</label>
                        <div class="form-input-wrapper">
                            <svg class="form-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <input type="tel" name="phone" class="form-input" placeholder="(555) 123-4567">
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
                    
                    <div class="form-group">
                        <label class="form-label">Confirm Password</label>
                        <div class="form-input-wrapper">
                            <svg class="form-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <input type="password" name="confirm_password" class="form-input" placeholder="••••••••" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="form-submit">
                        Create Account
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </form>
                
                <div class="auth-link">
                    Already have an account? <a href="/simple-event-portal/login.php">Sign in</a>
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
