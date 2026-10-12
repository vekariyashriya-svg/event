<?php
include '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile — DaisyDream</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
</head>
<body>

<section class="page-header">
    <div class="container">
        <div class="section-label">Account</div>
        <h1>My Profile</h1>
        <p>View and manage your personal information.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
            <div class="auth-alert auth-success" style="max-width:600px;margin:0 auto 30px;"><i class="fas fa-check-circle"></i> Profile updated successfully!</div>
        <?php endif; ?>
        
        <div class="profile-grid">
            <div class="profile-sidebar">
                <div class="profile-avatar"><?php echo strtoupper(substr($user['full_name'], 0, 1)); ?></div>
                <h3><?php echo htmlspecialchars($user['full_name']); ?></h3>
                <p>Member since <?php echo date('M Y', strtotime($user['created_at'])); ?></p>
                <a href="edit-profile.php" class="btn-primary btn-full" style="margin-bottom:10px;">Edit Profile</a>
                <a href="my-bookings.php" class="btn-secondary btn-full" style="margin-bottom:10px;">My Bookings</a>
                <a href="../auth/logout.php" class="btn-secondary btn-full">Logout</a>
            </div>
            
            <div class="profile-main">
                <h2>Personal Information</h2>
                <div class="profile-field"><span>Full Name</span><strong><?php echo htmlspecialchars($user['full_name']); ?></strong></div>
                <div class="profile-field"><span>Email Address</span><strong><?php echo htmlspecialchars($user['email']); ?></strong></div>
                <div class="profile-field"><span>Phone Number</span><strong><?php echo htmlspecialchars($user['phone']); ?></strong></div>
                <div class="profile-field"><span>Address</span><strong><?php echo htmlspecialchars($user['address']); ?></strong></div>
                <div class="profile-field"><span>Account Created</span><strong><?php echo date('d M Y', strtotime($user['created_at'])); ?></strong></div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
</body>
</html>