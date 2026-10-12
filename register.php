<?php
include '../config/db.php';
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $address = "Not Provided";

    if (empty($full_name) || empty($email) || empty($phone) || empty($password)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->rowCount() > 0) {
            $delete = $conn->prepare("DELETE FROM users WHERE email = ?");
            $delete->execute([$email]);
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)");

        if ($stmt->execute([$full_name, $email, $hashed, $phone, $address])) {
            header("Location: login.php?msg=registered");
            exit();
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — DaisyDream</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
</head>
<body>

<section class="auth-section">
    <a href="login.php" class="auth-back"><i class="fas fa-chevron-left"></i></a>

    <div class="auth-wrapper">
        <div class="auth-info">
            <div class="auth-label">Begin Your Journey</div>
            <h1>Create Your DaisyDream Account</h1>
            <p>Join us and start curating your perfect event experience. Book events, manage bookings, and make your moments unforgettable.</p>

            <div class="auth-features">
                <div class="auth-feature">
                    <div class="auth-feature-icon"><i class="fas fa-gem"></i></div>
                    <div><strong>Curated Events</strong><span>Events for every special occasion</span></div>
                </div>
                <div class="auth-feature">
                    <div class="auth-feature-icon"><i class="fas fa-bolt"></i></div>
                    <div><strong>Easy Booking</strong><span>Book events in a few simple steps</span></div>
                </div>
                <div class="auth-feature">
                    <div class="auth-feature-icon"><i class="fas fa-shield-alt"></i></div>
                    <div><strong>Secure Account</strong><span>Your data is protected</span></div>
                </div>
            </div>
        </div>

        <div class="auth-card">
            <div class="auth-header-icon" style="background:#7a6a1f;color:#fff;"><i class="fas fa-gem"></i></div>
            <div class="auth-card-header">
                <h2>Create Account</h2>
                <p>Fill in your details to get started.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="auth-alert auth-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label>Full Name</label>
                    <div class="input-wrapper">
                        <i class="far fa-user input-icon"></i>
                        <input type="text" name="full_name" placeholder="e.g. Julianne Sterling" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-wrapper">
                        <i class="far fa-envelope input-icon"></i>
                        <input type="email" name="email" placeholder="julianne@example.com" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <div class="input-wrapper">
                        <i class="fas fa-phone-alt input-icon"></i>
                        <input type="text" name="phone" placeholder="+1 (555) 000-0000" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" id="regPass" placeholder="At least 6 characters" required>
                        <i class="far fa-eye toggle-pass" onclick="togglePass('regPass', this)"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="confirm_password" id="regPass2" placeholder="Re-enter password" required>
                    </div>
                </div>
                <button type="submit" class="auth-submit">Create Account <i class="fas fa-arrow-right"></i></button>
            </form>

            <p class="auth-bottom">Already have an account? <a href="login.php">Login Now →</a></p>

            <p class="auth-terms">By continuing, you agree to DaisyDream's <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.</p>
        </div>
    </div>
</section>

<script>
function togglePass(id, el) {
    var x = document.getElementById(id);
    if (x.type === "password") { x.type = "text"; el.classList.remove("fa-eye"); el.classList.add("fa-eye-slash"); }
    else { x.type = "password"; el.classList.remove("fa-eye-slash"); el.classList.add("fa-eye"); }
}
</script>
</body>
</html>