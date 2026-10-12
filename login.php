<?php
session_start();
include '../config/db.php';

$error = "";
$success = "";

if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'logout_success') $success = "You have been logged out successfully.";
    if ($_GET['msg'] == 'registered') $success = "Registration successful! Please login.";
    if ($_GET['msg'] == 'reset_success') $success = "Password reset successfully!";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT user_id, full_name, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                header("Location: http://localhost/Daisy_Dream/USER/User/dashboard.php");
                exit();
            } else {
                $error = "Invalid password. Please try again.";
            }
        } else {
            $error = "Email not found. Please register first.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login — DaisyDream</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
</head>
<body>

<section class="auth-section">
    <a href="../User/home.php" class="auth-back"><i class="fas fa-chevron-left"></i></a>

    <div class="auth-wrapper">
        <div class="auth-info">
            <div class="auth-label">Welcome Back</div>
            <h1>Sign In to Continue Your Event Journey</h1>
            <p>Access your bookings, manage your profile, and discover new events curated just for you.</p>

            <div class="auth-features">
                <div class="auth-feature">
                    <div class="auth-feature-icon"><i class="fas fa-calendar-check"></i></div>
                    <div><strong>Manage Bookings</strong><span>Track all your event bookings</span></div>
                </div>
                <div class="auth-feature">
                    <div class="auth-feature-icon"><i class="fas fa-search"></i></div>
                    <div><strong>Discover Events</strong><span>Explore events for every occasion</span></div>
                </div>
                <div class="auth-feature">
                    <div class="auth-feature-icon"><i class="fas fa-user-circle"></i></div>
                    <div><strong>Your Profile</strong><span>Keep your details updated</span></div>
                </div>
            </div>
        </div>

        <div class="auth-card">
            <div class="auth-header-icon"><i class="far fa-heart"></i></div>
            <div class="auth-card-header">
                <h2>Welcome Back</h2>
                <p>Please enter your details to sign in.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="auth-alert auth-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="auth-alert auth-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-wrapper">
                        <i class="far fa-envelope input-icon"></i>
                        <input type="email" name="email" placeholder="alex@example.com" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" id="loginPass" placeholder="Enter your password" required>
                        <i class="far fa-eye toggle-pass" onclick="togglePass('loginPass', this)"></i>
                    </div>
                </div>
                <div class="auth-row">
                    <label class="remember-me"><input type="checkbox"> Remember me</label>
                    <a href="forgot-password.php" class="forgot-link">Forgot Password?</a>
                </div>
                <button type="submit" class="auth-submit">Sign In <i class="fas fa-arrow-right"></i></button>
            </form>

            <p class="auth-bottom">New to DaisyDream? <a href="register.php">Create Account →</a></p>
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