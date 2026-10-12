<?php
session_start();
include '../config/db.php';

$isChangeMode = isset($_GET['mode']) && $_GET['mode'] === 'change';
$isLoggedIn = isset($_SESSION['user_id']);

$error = "";
$success = "";

// Jo logged in user che ane change mode ma che, to email prefill karo
$prefillEmail = "";
if ($isLoggedIn && $isChangeMode) {
    $stmt = $conn->prepare("SELECT email FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $prefillEmail = $row['email'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $new_password = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if (empty($email) || empty($new_password)) {
        $error = "Please fill in all fields.";
    } elseif ($new_password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check->execute([$email]);
        
        if ($check->rowCount() == 0) {
            $error = "Email not found. Please register first.";
        } else {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            if ($update->execute([$hashed, $email])) {
                
                // Jo logged in user che to sidho dashboard par moklo
                if ($isLoggedIn && $isChangeMode) {
                    header("Location: ../User/dashboard.php?msg=password_changed");
                    exit();
                } else {
                    header("Location: login.php?msg=reset_success");
                    exit();
                }
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}

// Page Title based on mode
$pageTitle   = $isChangeMode ? "Change Password" : "Reset Password";
$pageLabel   = $isChangeMode ? "Account Security" : "Account Recovery";
$pageHeading = $isChangeMode ? "Update Your Password" : "Reset Your Password";
$pageSub     = $isChangeMode 
    ? "Enter your new password below to update your DaisyDream account password." 
    : "Enter your registered email and set a new password to regain access to your DaisyDream account.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $pageTitle; ?> — DaisyDream</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
</head>
<body>

<section class="auth-section">
    <a href="<?php echo $isChangeMode ? '../User/edit-profile.php' : 'login.php'; ?>" class="auth-back">
        <i class="fas fa-chevron-left"></i>
    </a>
    
    <div class="auth-wrapper">
        <div class="auth-info">
            <div class="auth-label"><?php echo $pageLabel; ?></div>
            <h1><?php echo $pageHeading; ?></h1>
            <p><?php echo $pageSub; ?></p>
        </div>
        
        <div class="auth-card">
            <div class="auth-header-icon" style="background:#7a6a1f;color:#fff;">
                <i class="fas fa-key"></i>
            </div>
            <div class="auth-card-header">
                <h2><?php echo $pageTitle; ?></h2>
                <p><?php echo $isChangeMode ? "Enter your new password below." : "Enter your email and new password."; ?></p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="auth-alert auth-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="auth-alert auth-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                
                <!-- EMAIL -->
                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-wrapper">
                        <i class="far fa-envelope input-icon"></i>
                        <input 
                            type="email" 
                            name="email" 
                            placeholder="you@example.com" 
                            value="<?php echo htmlspecialchars($prefillEmail); ?>"
                            <?php echo $isChangeMode ? 'readonly style="background:#eee;cursor:not-allowed;"' : ''; ?>
                            required
                        >
                    </div>
                </div>
                
                <!-- NEW PASSWORD -->
                <div class="form-group">
                    <label>New Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="new_password" id="np1" placeholder="At least 6 characters" required>
                        <i class="far fa-eye toggle-pass" onclick="togglePass('np1', this)"></i>
                    </div>
                </div>
                
                <!-- CONFIRM PASSWORD -->
                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="confirm_password" id="np2" placeholder="Re-enter password" required>
                    </div>
                </div>
                
                <!-- SUBMIT -->
                <button type="submit" class="auth-submit">
                    <?php echo $isChangeMode ? 'Update Password' : 'Reset Password'; ?> 
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
            
            <p class="auth-bottom">
                <?php if ($isChangeMode): ?>
                    <a href="../User/edit-profile.php">← Back to Edit Profile</a>
                <?php else: ?>
                    Remember password? <a href="login.php">Sign In →</a>
                <?php endif; ?>
            </p>
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