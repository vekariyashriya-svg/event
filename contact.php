<?php
include '../config/db.php';
include '../includes/header.php';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $message = trim($_POST['message']);

    if (empty($full_name) || empty($email) || empty($message)) {
        $error = "Please fill in all required fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO contact_messages (full_name, email, address, message) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$full_name, $email, $address, $message])) {
            $success = "Thank you! Your message has been sent successfully.";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact — DaisyDream</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
</head>
<body>

<section class="page-header">
    <div class="container">
        <div class="section-label">Get in Touch</div>
        <h1>Contact Us</h1>
        <p>We'd love to hear from you. Reach out anytime.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="contact-grid">
            <div class="contact-info">
                <h2>Contact Information</h2>
                <p>Have a question or want to plan an event? We're here to help.</p>
                
                <div class="contact-item">
                    <div class="contact-item-icon"><i class="fas fa-envelope"></i></div>
                    <div><h4>Email</h4><p>info@daisydream.com</p></div>
                </div>
                <div class="contact-item">
                    <div class="contact-item-icon"><i class="fas fa-phone"></i></div>
                    <div><h4>Phone</h4><p>+91 98765 43210</p></div>
                </div>
                <div class="contact-item">
                    <div class="contact-item-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div><h4>Address</h4><p>Surat, Gujarat, India</p></div>
                </div>
                <div class="contact-item">
                    <div class="contact-item-icon"><i class="fas fa-clock"></i></div>
                    <div><h4>Working Hours</h4><p>Mon - Sat: 9:00 AM - 7:00 PM</p></div>
                </div>
            </div>
            
            <div class="contact-form-card">
                <h2>Send Us a Message</h2>
                
                <?php if (!empty($success)): ?>
                    <div class="auth-alert auth-success" style="margin-bottom:20px;"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
                <?php endif; ?>
                <?php if (!empty($error)): ?>
                    <div class="auth-alert auth-error" style="margin-bottom:20px;"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group"><label>Full Name</label><input type="text" name="full_name" placeholder="Your name" required></div>
                    <div class="form-group"><label>Email Address</label><input type="email" name="email" placeholder="you@example.com" required></div>
                    <div class="form-group"><label>Address</label><input type="text" name="address" placeholder="Your address"></div>
                    <div class="form-group"><label>Message</label><textarea name="message" rows="5" placeholder="Write your message..." required></textarea></div>
                    <button type="submit" class="btn-primary btn-full" style="padding:14px;">Send Message <i class="fas fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
</body>
</html>