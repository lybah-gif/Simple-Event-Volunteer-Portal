<?php
// index.php - Landing Page
require_once __DIR__ . '/config/db.php';

$loggedIn = isset($_SESSION['user_id']);

// If logged in, redirect to appropriate dashboard
if ($loggedIn) {
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: /simple-event-portal/admin/dashboard.php');
    } else {
        header('Location: /simple-event-portal/user/dashboard.php');
    }
    exit;
}

// Fetch upcoming events for landing page
$events = [];
$res = $mysqli->query("
    SELECT e.*, 
        (SELECT COUNT(*) FROM event_registrations r WHERE r.event_id = e.id) AS reg_count 
    FROM events e 
    WHERE e.event_date >= CURDATE()
    ORDER BY e.event_date ASC
    LIMIT 6
");
while ($row = $res->fetch_assoc()) {
    $events[] = $row;
}

// Get stats for display
$stats = [
    'volunteers' => $mysqli->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'")->fetch_assoc()['count'] ?? 0,
    'events' => $mysqli->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'] ?? 0,
    'organizations' => 50 // Placeholder
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>VolunteerHub - Make a Difference Today</title>
    <link rel="stylesheet" href="/simple-event-portal/assets/css/main.css">
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="header-content">
            <a href="/simple-event-portal/" class="logo">
                <div class="logo-icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <span>VolunteerHub</span>
            </a>
            
            <ul class="nav-links">
                <li><a href="/simple-event-portal/" class="active">Home</a></li>
                <li><a href="/simple-event-portal/user/events.php">Events</a></li>
                <li><a href="/simple-event-portal/#about-us">About</a></li>
            </ul>
            
            <div class="auth-buttons">
                <a href="/simple-event-portal/login.php" class="btn btn-outline">Login</a>
                <a href="/simple-event-portal/register.php" class="btn btn-gradient">Register</a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-container">
            <div class="hero-content animate-slide-in-left">
                <div class="hero-tagline">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                    <span>Make a Difference Today</span>
                </div>
                <h1>Connect. Volunteer. Transform Lives.</h1>
                <p class="hero-description">
                    Join thousands of passionate volunteers making a real impact in their communities. 
                    Find meaningful opportunities, track your contributions, and be part of something bigger.
                </p>
                <div class="hero-buttons">
                    <a href="/simple-event-portal/register.php" class="btn btn-gradient">
                        Get Started
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                    <a href="/simple-event-portal/login.php" class="btn btn-outline">Sign In</a>
                </div>
                <div class="hero-stats">
                    <div class="stat-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span><?php echo number_format($stats['volunteers']); ?>+ Volunteers</span>
                    </div>
                    <div class="stat-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span><?php echo number_format($stats['events']); ?>+ Events</span>
                    </div>
                    <div class="stat-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <span><?php echo $stats['organizations']; ?>+ Organizations</span>
                    </div>
                </div>
            </div>
            <div class="hero-image animate-slide-in-right">
                <div style="text-align: center; padding: 40px;">
                    <svg width="200" height="200" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity: 0.3;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p style="margin-top: 20px; font-size: 16px;">Volunteer Activities</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Upcoming Events Section -->
    <section class="section">
        <div class="section-header animate-fade-in-up">
            <h2 class="section-title">Upcoming Events</h2>
            <p class="section-description">
                Discover opportunities to make a difference. Browse our latest volunteer events and find the perfect match for your passion.
            </p>
        </div>
        
        <div class="events-grid">
            <?php if (count($events) > 0): ?>
                <?php foreach ($events as $event): ?>
                <div class="event-card animate-fade-in-up">
                    <div class="event-image">
                        <div class="event-category">Environment</div>
                    </div>
                    <div class="event-content">
                        <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                        <div class="event-details">
                            <div class="event-detail">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <?php 
                                $date = new DateTime($event['event_date']);
                                echo $date->format('M d, Y');
                                ?>
                            </div>
                            <div class="event-detail">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <?php 
                                if ($event['event_time']) {
                                    $time = new DateTime($event['event_time']);
                                    echo $time->format('g:i A');
                                } else {
                                    echo 'TBD';
                                }
                                ?>
                            </div>
                            <div class="event-detail">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <?php echo htmlspecialchars($event['venue'] ?? 'TBD'); ?>
                            </div>
                            <div class="event-detail">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <?php echo (int)$event['reg_count']; ?> volunteers needed
                            </div>
                        </div>
                        <div class="event-footer">
                            <div class="event-volunteers"></div>
                            <a href="/simple-event-portal/user/events.php" class="event-link">
                                Learn More
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="event-card">
                    <div class="event-content">
                        <p>No upcoming events at the moment. Check back soon!</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 40px;">
            <a href="/simple-event-portal/user/events.php" class="btn btn-gradient">
                View All Events
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </a>
        </div>
    </section>

    <!-- About Us Section -->
    <section class="section" id="about-us" style="background: linear-gradient(to bottom, #f9fafb, white);">
        <div class="section-header animate-fade-in-up">
            <h2 class="section-title">About VolunteerHub</h2>
            <p class="section-description">
                We're on a mission to connect passionate volunteers with meaningful opportunities to create positive change in communities worldwide.
            </p>
        </div>
        
        <div style="max-width: 1200px; margin: 0 auto;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px; margin-top: 60px;">
                <div class="animate-fade-in-up">
                    <div style="background: white; padding: 40px; border-radius: 20px; box-shadow: var(--shadow-md); height: 100%;">
                        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #3b82f6, #10b981); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 24px;">
                            <svg width="30" height="30" fill="none" stroke="white" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <h3 style="font-size: 24px; font-weight: 700; margin-bottom: 16px; color: var(--text-dark);">Our Mission</h3>
                        <p style="color: var(--text-gray); line-height: 1.8;">
                            To empower individuals and organizations to make a meaningful impact through volunteerism, fostering stronger, more connected communities.
                        </p>
                    </div>
                </div>
                
                <div class="animate-fade-in-up" style="animation-delay: 0.2s;">
                    <div style="background: white; padding: 40px; border-radius: 20px; box-shadow: var(--shadow-md); height: 100%;">
                        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #9333ea, #ec4899); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 24px;">
                            <svg width="30" height="30" fill="none" stroke="white" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 style="font-size: 24px; font-weight: 700; margin-bottom: 16px; color: var(--text-dark);">Our Vision</h3>
                        <p style="color: var(--text-gray); line-height: 1.8;">
                            A world where every person has easy access to volunteer opportunities that align with their passions and create lasting positive change.
                        </p>
                    </div>
                </div>
                
                <div class="animate-fade-in-up" style="animation-delay: 0.4s;">
                    <div style="background: white; padding: 40px; border-radius: 20px; box-shadow: var(--shadow-md); height: 100%;">
                        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #10b981, #3b82f6); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 24px;">
                            <svg width="30" height="30" fill="none" stroke="white" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h3 style="font-size: 24px; font-weight: 700; margin-bottom: 16px; color: var(--text-dark);">Our Values</h3>
                        <p style="color: var(--text-gray); line-height: 1.8;">
                            We believe in transparency, inclusivity, and the power of community. Every volunteer matters, and every contribution makes a difference.
                        </p>
                    </div>
                </div>
            </div>
            
            <div style="margin-top: 60px; text-align: center; background: white; padding: 60px 40px; border-radius: 20px; box-shadow: var(--shadow-md);">
                <h3 style="font-size: 32px; font-weight: 700; margin-bottom: 24px; color: var(--text-dark);">Why Choose VolunteerHub?</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 32px; margin-top: 40px;">
                    <div>
                        <div style="font-size: 48px; margin-bottom: 16px;">🎯</div>
                        <h4 style="font-size: 20px; font-weight: 600; margin-bottom: 8px; color: var(--text-dark);">Easy to Use</h4>
                        <p style="color: var(--text-gray);">Intuitive platform designed for volunteers of all tech levels.</p>
                    </div>
                    <div>
                        <div style="font-size: 48px; margin-bottom: 16px;">🌍</div>
                        <h4 style="font-size: 20px; font-weight: 600; margin-bottom: 8px; color: var(--text-dark);">Wide Reach</h4>
                        <p style="color: var(--text-gray);">Connect with organizations and events in your local community and beyond.</p>
                    </div>
                    <div>
                        <div style="font-size: 48px; margin-bottom: 16px;">📊</div>
                        <h4 style="font-size: 20px; font-weight: 600; margin-bottom: 8px; color: var(--text-dark);">Track Impact</h4>
                        <p style="color: var(--text-gray);">Monitor your volunteer hours and see the difference you're making.</p>
                    </div>
                    <div>
                        <div style="font-size: 48px; margin-bottom: 16px;">🤝</div>
                        <h4 style="font-size: 20px; font-weight: 600; margin-bottom: 8px; color: var(--text-dark);">Build Community</h4>
                        <p style="color: var(--text-gray);">Meet like-minded people and grow your network while giving back.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works" id="how-it-works">
        <div class="section-header animate-fade-in-up">
            <h2 class="section-title">How It Works</h2>
            <p class="section-description">
                Getting started is easy. Follow these three simple steps to begin your volunteering journey.
            </p>
        </div>
        
        <div class="steps-grid">
            <div class="step-card animate-fade-in-up">
                <div class="step-number">01</div>
                <div class="step-icon blue">
                    <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #3b82f6;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h3 class="step-title">Create Your Profile</h3>
                <p class="step-description">Sign up and tell us about your interests, skills, and availability.</p>
            </div>
            
            <div class="step-card animate-fade-in-up" style="animation-delay: 0.2s;">
                <div class="step-number">02</div>
                <div class="step-icon green">
                    <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #10b981;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h3 class="step-title">Find Events</h3>
                <p class="step-description">Browse through curated volunteer opportunities that match your passion.</p>
            </div>
            
            <div class="step-card animate-fade-in-up" style="animation-delay: 0.4s;">
                <div class="step-number">03</div>
                <div class="step-icon pink">
                    <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #ec4899;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="step-title">Make Impact</h3>
                <p class="step-description">Register for events, show up, and make a real difference in your community.</p>
            </div>
        </div>
</section>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <div class="section-header animate-fade-in-up">
            <h2 class="section-title">What Our Community Says</h2>
            <p class="section-description">
                Hear from volunteers and organizers who are making a difference.
            </p>
</div>

        <div class="testimonial-card animate-fade-in-up">
            <div class="testimonial-stars">
                <?php for ($i = 0; $i < 5; $i++): ?>
                <svg width="24" height="24" fill="#fbbf24" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                </svg>
                <?php endfor; ?>
            </div>
            <p class="testimonial-quote">
                "The interface is beautiful and intuitive. Our volunteers love how simple it is to sign up and track their hours."
            </p>
            <div class="testimonial-author">Emily Rodriguez</div>
            <div class="testimonial-role">Volunteer Coordinator</div>
            <div class="testimonial-dots">
                <div class="dot active"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <h2 class="cta-title animate-fade-in-up">Ready to Make a Difference?</h2>
        <p class="cta-description animate-fade-in-up">
            Join our community of passionate volunteers today and start creating positive change.
        </p>
        <a href="/simple-event-portal/register.php" class="cta-button animate-fade-in-up">
            Join VolunteerHub Now
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
            </svg>
        </a>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-brand">
                <div class="footer-logo">
                    <div class="logo-icon">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <span>VolunteerHub</span>
                </div>
                <p class="footer-description">
                    Connecting passionate volunteers with meaningful opportunities to make a difference.
                </p>
            </div>
            
            <div class="footer-column">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="/simple-event-portal/#about-us">About Us</a></li>
                    <li><a href="/simple-event-portal/user/events.php">Browse Events</a></li>
                    <li><a href="/simple-event-portal/#how-it-works">How It Works</a></li>
                </ul>
    </div>

            <div class="footer-column">
                <h3>For Organizers</h3>
                <ul class="footer-links">
                    <li><a href="/simple-event-portal/login.php">Create Event</a></li>
                    <li><a href="/simple-event-portal/login.php">Manage Events</a></li>
                </ul>
    </div>

            <div class="footer-column">
                <h3>Contact Us</h3>
                <div class="contact-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span>hello@volunteerhub.org</span>
                </div>
                <div class="contact-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <span>(555) 123-4567</span>
                </div>
                <div class="contact-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>123 Community St, City</span>
                </div>
    </div>
</div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> VolunteerHub. All rights reserved.</p>
</div>
    </footer>
</body>
</html>
