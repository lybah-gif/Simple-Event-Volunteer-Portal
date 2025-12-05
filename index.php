<?php
// Start session to detect logged-in users
if (session_status() === PHP_SESSION_NONE) session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Event Volunteer Portal</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f6f9fc;
            color: #2d4059;
        }

        /* Header */
        header {
            background: #083d77;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header h1 {
            margin: 0;
            font-size: 24px;
        }
        header nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-weight: bold;
        }

        /* Hero Section */
        .hero {
            text-align: center;
            padding: 50px 20px;
            background: white;
        }
        .hero h2 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        .hero p {
            font-size: 17px;
            max-width: 700px;
            margin: 0 auto 20px;
        }
        .btn {
            display: inline-block;
            padding: 12px 20px;
            background: #1f8f45;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            margin: 5px;
            font-weight: bold;
        }

        /* Section Titles */
        .section-title {
            text-align: center;
            font-size: 24px;
            margin-top: 50px;
        }

        /* Opportunity Box */
        .opportunity-box {
            background: white;
            padding: 30px;
            width: 80%;
            margin: 20px auto;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            text-align: center;
        }

        /* Why Volunteer Cards */
        .why-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            padding: 20px;
            flex-wrap: wrap;
        }
        .why-card {
            background: white;
            width: 280px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            text-align: center;
        }
        .why-card h3 {
            margin-bottom: 10px;
        }

        /* Organizer Box */
        .organizer-box {
            background: white;
            padding: 30px;
            width: 80%;
            margin: 40px auto;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
    </style>
</head>

<body>

<header>
    <h1>Event Volunteer Portal</h1>
    <nav>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php else: ?>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <a href="admin/dashboard.php">Admin Dashboard</a>
            <?php else: ?>
                <a href="user/dashboard.php">Dashboard</a>
            <?php endif; ?>
            <a href="logout.php">Logout</a>
        <?php endif; ?>
    </nav>
</header>

<section class="hero">
    <h2>Welcome to the Community Volunteering Hub</h2>
    <p>
        Join hands to support upcoming community events, charity drives, awareness 
        campaigns, and social development activities. Make an impact by giving your time 
        and skills.
    </p>

    <!-- ALWAYS ACCESSIBLE: Find Events -->
    <a class="btn" href="user/events.php">Find Events</a>

    <?php if (!isset($_SESSION['user_id'])): ?>
        <a class="btn" href="register.php">Become a Volunteer</a>
    <?php endif; ?>
</section>

<h2 class="section-title">📅 Upcoming Volunteer Events</h2>
<div class="opportunity-box">
    <h3>No Events Available Right Now</h3>
    <p>Please check back later for new volunteer opportunities.</p>
</div>

<h2 class="section-title">⭐ Why Volunteer with Us?</h2>

<div class="why-container">
    <div class="why-card">
        <h3>Support Causes</h3>
        <p>Help create meaningful change by supporting community programs and social events.</p>
    </div>

    <div class="why-card">
        <h3>Develop Skills</h3>
        <p>Gain leadership, teamwork, and communication skills through active participation.</p>
    </div>

    <div class="why-card">
        <h3>Meet New People</h3>
        <p>Connect with passionate individuals and grow your community network.</p>
    </div>
</div>

<div class="organizer-box">
    <h3>Are You an Event Organizer?</h3>
    <p>Create events, manage volunteers, and coordinate activities with ease.</p>

    <?php if (!isset($_SESSION['user_id'])): ?>
        <a class="btn" href="register.php">Register as Organizer</a>
        <a class="btn" href="login.php" style="background:#0a66c2;">Login</a>
    <?php else: ?>
        <a class="btn" href="admin/dashboard.php">Go to Dashboard</a>
    <?php endif; ?>
</div>

</body>
</html>
