<?php
include '../config/db.php';
include '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us — DaisyDream</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
</head>
<body>

<section class="page-header">
    <div class="container">
        <div class="section-label">About Us</div>
        <h1>About DaisyDream</h1>
        <p>Turning your special moments into unforgettable memories.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        
        <div class="about-section">
            <h2>Who We Are</h2>
            <p>DaisyDream is a comprehensive event management platform designed to connect people with beautifully curated events. From intimate celebrations to grand occasions, we help you discover, plan, and book the perfect event experience — all through a single, easy-to-use platform.</p>
            <p>Whether you're planning a wedding, organizing a corporate gathering, or celebrating a milestone, DaisyDream provides a seamless way to explore events, view detailed information, and book with confidence.</p>
        </div>
        
        <div class="about-img">
            <img src="../assets/images/about.jpg" alt="About DaisyDream" onerror="this.src='https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?q=80&w=1200&auto=format&fit=crop'">
        </div>
        
        <div class="about-section">
            <h2>Our Purpose</h2>
            <p>DaisyDream was created with a simple vision: to simplify event planning for everyone. We understand that organizing an event can be overwhelming — from selecting the right venue to coordinating every detail. Our platform brings everything together in one place, making the experience smooth, transparent, and enjoyable.</p>
            <p>We believe that every celebration deserves to be memorable, and our mission is to help you focus on the joy of the moment while we handle the rest.</p>
        </div>
        
        <div class="about-section">
            <h2>Our Services</h2>
            <p>DaisyDream offers a diverse range of event categories to suit every occasion:</p>
            <div class="services-about-grid">
                <div class="service-card"><div class="service-icon"><i class="fas fa-heart"></i></div><h3>Wedding Events</h3><p>Elegant and personalized wedding celebrations.</p></div>
                <div class="service-card"><div class="service-icon"><i class="fas fa-birthday-cake"></i></div><h3>Birthday Events</h3><p>Fun and memorable birthday celebrations.</p></div>
                <div class="service-card"><div class="service-icon"><i class="fas fa-briefcase"></i></div><h3>Corporate Events</h3><p>Professional events for your business needs.</p></div>
                <div class="service-card"><div class="service-icon"><i class="fas fa-users"></i></div><h3>Social Events</h3><p>Get-togethers and social celebrations.</p></div>
                <div class="service-card"><div class="service-icon"><i class="fas fa-gift"></i></div><h3>Anniversary Events</h3><p>Celebrating love and togetherness.</p></div>
                <div class="service-card"><div class="service-icon"><i class="fas fa-star"></i></div><h3>Special Events</h3><p>Custom events tailored to your needs.</p></div>
            </div>
        </div>
        
        <div class="about-section">
            <h2>Why DaisyDream?</h2>
            <ul class="intro-features">
                <li>Easy and intuitive event discovery</li>
                <li>Detailed event information and schedules</li>
                <li>Simple booking process with confirmation</li>
                <li>Personalized user dashboard</li>
                <li>Complete booking management</li>
                <li>Professional event planning support</li>
            </ul>
        </div>
        
        <div class="about-section">
            <h2>Our Event Management Process</h2>
            <div class="steps-grid">
                <div class="step-card"><div class="step-num">01</div><h3>Event Discovery</h3><p>Browse our curated event collection.</p></div>
                <div class="step-card"><div class="step-num">02</div><h3>Event Selection</h3><p>Choose the event that suits you best.</p></div>
                <div class="step-card"><div class="step-num">03</div><h3>Booking</h3><p>Fill in your details and confirm.</p></div>
                <div class="step-card"><div class="step-num">04</div><h3>Confirmation</h3><p>Receive your booking confirmation.</p></div>
            </div>
        </div>
        
        <div class="about-section">
            <h2>Our Mission</h2>
            <p>To make event planning accessible, enjoyable, and stress-free for everyone. We aim to provide a reliable platform that connects people with exceptional event experiences, ensuring every celebration is as unique as the people behind it.</p>
        </div>
        
        <div class="about-section">
            <h2>Our Vision</h2>
            <p>To become the most trusted event management platform — a place where unforgettable memories begin. We envision a world where planning any event, big or small, is simple, joyful, and within everyone's reach.</p>
        </div>
        
        <div class="cta-section">
            <h2>Let's Make Your Next Event Memorable</h2>
            <p>Start exploring events today and create memories that last a lifetime.</p>
            <div>
                <a href="events.php" class="btn-primary">Explore Events</a>
                <a href="contact.php" class="btn-secondary">Contact Us</a>
            </div>
        </div>
        
    </div>
</section>

<?php include '../includes/footer.php'; ?>
</body>
</html>