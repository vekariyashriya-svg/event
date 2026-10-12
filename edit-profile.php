<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/db.php";

/*
|--------------------------------------------------------------------------
| Login Check
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['user_id'];

$error = "";
$success = "";
$user = null;

/*
|--------------------------------------------------------------------------
| Get User
|--------------------------------------------------------------------------
*/

try {

    $stmt = $conn->prepare("
        SELECT
            user_id,
            full_name,
            email,
            phone,
            address,
            created_at
        FROM users
        WHERE user_id = ?
        LIMIT 1
    ");

    $stmt->execute([$userId]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $error = "Unable to load your profile. Please try again.";
}

if (!$user) {

    session_destroy();

    header("Location: ../auth/login.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| Update Profile
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullName = trim($_POST['full_name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $address  = trim($_POST['address'] ?? '');

    if ($fullName === "") {
        $error = "Please enter your full name.";
    } elseif ($email === "") {
        $error = "Please enter your email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {

        try {

            $stmt = $conn->prepare("
                SELECT user_id
                FROM users
                WHERE email = ?
                AND user_id != ?
                LIMIT 1
            ");

            $stmt->execute([$email, $userId]);

            $emailExists = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($emailExists) {

                $error = "This email address is already registered with another account.";

            } else {

                $stmt = $conn->prepare("
                    UPDATE users
                    SET
                        full_name = ?,
                        email = ?,
                        phone = ?,
                        address = ?
                    WHERE user_id = ?
                ");

                $stmt->execute([
                    $fullName,
                    $email,
                    $phone,
                    $address,
                    $userId
                ]);

                $_SESSION['full_name'] = $fullName;
                $_SESSION['email'] = $email;

                $user['full_name'] = $fullName;
                $user['email'] = $email;
                $user['phone'] = $phone;
                $user['address'] = $address;

                $success = "Your profile has been updated successfully.";
            }

        } catch (PDOException $e) {

            $error = "Something went wrong while updating your profile.";
        }
    }
}

require_once "../includes/header.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile | DaisyDream</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>

        /* ============= EDIT PROFILE PAGE ============= */
        .edit-profile-page { padding: 70px 0 90px; background: #FAF8F5; min-height: calc(100vh - 150px); }
        .edit-profile-heading { max-width: 760px; margin-bottom: 38px; }
        .edit-profile-heading .section-tag { display: inline-block; margin-bottom: 12px; }
        .edit-profile-heading h1 { margin: 0 0 12px; font-size: 42px; line-height: 1.15; color: #292929; }
        .edit-profile-heading p { margin: 0; color: #666666; font-size: 16px; line-height: 1.7; }

        /* ============= MAIN LAYOUT ============= */
        .edit-profile-layout { display: grid; grid-template-columns: 330px minmax(0, 1fr); gap: 28px; align-items: start; }

        /* ============= LEFT PROFILE CARD ============= */
        .profile-summary-card { background: #FFFFFF; border: 1px solid #E5E5E5; border-radius: 14px; padding: 30px; box-shadow: 0 8px 30px rgba(41,41,41,0.05); }
        .profile-avatar { width: 82px; height: 82px; border-radius: 50%; background: #A8785C; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 600; margin-bottom: 20px; }
        .profile-summary-card h2 { margin: 0 0 7px; font-size: 23px; color: #292929; }
        .profile-email { margin: 0 0 26px; color: #777777; font-size: 14px; word-break: break-word; }
        .profile-summary-divider { height: 1px; background: #E5E5E5; margin: 24px 0; }
        .profile-summary-item { margin-bottom: 20px; }
        .profile-summary-item:last-child { margin-bottom: 0; }
        .profile-summary-item span { display: block; font-size: 12px; text-transform: uppercase; letter-spacing: 0.8px; color: #999999; margin-bottom: 6px; }
        .profile-summary-item strong { display: block; color: #3A3A3A; font-size: 14px; line-height: 1.5; word-break: break-word; }

        /* ============= EDIT FORM CARD ============= */
        .edit-profile-card { background: #FFFFFF; border: 1px solid #E5E5E5; border-radius: 14px; padding: 38px; box-shadow: 0 8px 30px rgba(41,41,41,0.05); }
        .edit-card-header { padding-bottom: 25px; margin-bottom: 28px; border-bottom: 1px solid #EAEAEA; }
        .edit-card-header h2 { margin: 0 0 7px; font-size: 25px; color: #292929; }
        .edit-card-header p { margin: 0; color: #777777; font-size: 14px; line-height: 1.6; }

        /* ============= ALERTS ============= */
        .profile-alert { padding: 14px 16px; border-radius: 8px; margin-bottom: 25px; font-size: 14px; line-height: 1.5; }
        .profile-alert.error { background: #FDF1F0; border: 1px solid #E8C9C5; color: #9B4A43; }
        .profile-alert.success { background: #F1F7F1; border: 1px solid #C9DDC9; color: #4E7650; }

        /* ============= FORM ============= */
        .edit-form { width: 100%; }
        .edit-form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 24px 22px; }
        .edit-form-group { display: flex; flex-direction: column; }
        .edit-form-group.full-width { grid-column: 1 / -1; }
        .edit-form-group label { margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #3A3A3A; }
        .edit-form-group label span { color: #A8785C; }
        .edit-form-group input,
        .edit-form-group textarea { width: 100%; box-sizing: border-box; border: 1px solid #DCDCDC; background: #FFFFFF; color: #292929; border-radius: 8px; padding: 13px 14px; font-size: 14px; font-family: inherit; outline: none; transition: border-color 0.2s ease, box-shadow 0.2s ease; }
        .edit-form-group input { height: 48px; }
        .edit-form-group textarea { min-height: 115px; resize: vertical; line-height: 1.6; }
        .edit-form-group input:focus,
        .edit-form-group textarea:focus { border-color: #A8785C; box-shadow: 0 0 0 3px rgba(168,120,92,0.10); }
        .edit-form-group input::placeholder,
        .edit-form-group textarea::placeholder { color: #A0A0A0; }
        .field-note { margin-top: 7px; font-size: 12px; color: #999999; }

        /* ============= FORM ACTIONS ============= */
        .edit-form-actions { display: flex; justify-content: flex-end; align-items: center; gap: 12px; margin-top: 30px; padding-top: 25px; border-top: 1px solid #EAEAEA; }
        .edit-cancel-btn, .edit-save-btn { display: inline-flex; align-items: center; justify-content: center; min-height: 46px; padding: 0 22px; border-radius: 7px; text-decoration: none; font-size: 14px; font-weight: 600; cursor: pointer; font-family: inherit; transition: 0.2s ease; }
        .edit-cancel-btn { border: 1px solid #D8D8D8; background: #FFFFFF; color: #555555; }
        .edit-cancel-btn:hover { border-color: #A8785C; color: #A8785C; }
        .edit-save-btn { border: 1px solid #A8785C; background: #A8785C; color: #FFFFFF; }
        .edit-save-btn:hover { background: #93694F; border-color: #93694F; }

        /* ============= SECURITY BOX ============= */
        .security-box { margin-top: 28px; padding: 20px; background: #FAF8F5; border: 1px solid #E8E2DD; border-radius: 9px; }
        .security-box h3 { margin: 0 0 6px; color: #292929; font-size: 16px; }
        .security-box p { margin: 0 0 14px; color: #777777; font-size: 13px; line-height: 1.6; }
        .security-box a { color: #93694F; font-size: 13px; font-weight: 600; text-decoration: none; }
        .security-box a:hover { text-decoration: underline; }

        /* ============= RESPONSIVE ============= */
        @media (max-width: 900px) {
            .edit-profile-layout { grid-template-columns: 1fr; }
            .profile-summary-card { display: grid; grid-template-columns: auto 1fr; column-gap: 20px; align-items: center; }
            .profile-avatar { grid-row: span 2; margin-bottom: 0; }
            .profile-summary-divider { grid-column: 1 / -1; }
            .profile-summary-details { min-width: 0; }
        }
        @media (max-width: 700px) {
            .edit-profile-page { padding: 50px 0 65px; }
            .edit-profile-heading h1 { font-size: 34px; }
            .edit-profile-card { padding: 25px 20px; }
            .profile-summary-card { display: block; padding: 25px 20px; }
            .profile-avatar { margin-bottom: 18px; }
            .profile-summary-divider { margin: 22px 0; }
            .edit-form-grid { grid-template-columns: 1fr; gap: 20px; }
            .edit-form-group.full-width { grid-column: auto; }
            .edit-form-actions { flex-direction: column-reverse; align-items: stretch; }
            .edit-cancel-btn, .edit-save-btn { width: 100%; }
        }
    </style>
</head>
<body>

<section class="edit-profile-page">
    <div class="container">

        <!-- ============= PAGE HEADING ============= -->
        <div class="edit-profile-heading">
            <span class="section-tag">MY ACCOUNT</span>
            <h1>Edit Profile</h1>
            <p>Keep your personal information up to date. Changes made here will be reflected across your DaisyDream account.</p>
        </div>

        <!-- ============= PROFILE LAYOUT ============= -->
        <div class="edit-profile-layout">

            <!-- ============= PROFILE SUMMARY ============= -->
            <aside class="profile-summary-card">

                <div class="profile-avatar">
                    <?php
                    $nameParts = preg_split('/\s+/', trim($user['full_name']));
                    $initials = '';
                    if (!empty($nameParts[0])) $initials .= strtoupper(substr($nameParts[0], 0, 1));
                    if (count($nameParts) > 1 && !empty($nameParts[count($nameParts) - 1])) {
                        $initials .= strtoupper(substr($nameParts[count($nameParts) - 1], 0, 1));
                    }
                    echo htmlspecialchars($initials ?: 'U');
                    ?>
                </div>

                <div class="profile-summary-details">
                    <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
                    <p class="profile-email"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>

                <div class="profile-summary-divider"></div>

                <div class="profile-summary-item">
                    <span>User ID</span>
                    <strong><?php echo htmlspecialchars($user['user_id']); ?></strong>
                </div>
                <div class="profile-summary-item">
                    <span>Phone</span>
                    <strong><?php echo !empty($user['phone']) ? htmlspecialchars($user['phone']) : 'Not added'; ?></strong>
                </div>
                <div class="profile-summary-item">
                    <span>Member Since</span>
                    <strong><?php echo date("d M Y", strtotime($user['created_at'])); ?></strong>
                </div>

            </aside>

            <!-- ============= EDIT FORM ============= -->
            <div class="edit-profile-card">

                <div class="edit-card-header">
                    <h2>Personal Information</h2>
                    <p>Update the information below and click <strong>Save Changes</strong> to update your account.</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="profile-alert error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="profile-alert success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form method="POST" action="" class="edit-form">
                    <div class="edit-form-grid">

                        <div class="edit-form-group">
                            <label for="full_name">Full Name <span>*</span></label>
                            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" placeholder="Enter your full name" autocomplete="name" required>
                        </div>

                        <div class="edit-form-group">
                            <label for="email">Email Address <span>*</span></label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" placeholder="Enter your email address" autocomplete="email" required>
                        </div>

                        <div class="edit-form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="Enter your phone number" autocomplete="tel">
                            <span class="field-note">Enter a phone number where we can reach you regarding your booking.</span>
                        </div>

                        <div class="edit-form-group">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" placeholder="Enter your address" autocomplete="street-address">
                        </div>

                    </div>

                    <div class="edit-form-actions">
                        <a href="dashboard.php" class="edit-cancel-btn">Cancel</a>
                        <button type="submit" class="edit-save-btn">Save Changes</button>
                    </div>
                </form>

                <!-- ============= SECURITY ============= -->
                <div class="security-box">
                    <h3>Password & Account Security</h3>
                    <p>Want to change your password? You can securely update it anytime from here without changing your profile information.</p>
                    <a href="../auth/forgot_password.php?mode=change">Change Password →</a>
                </div>

            </div>

        </div>
    </div>
</section>

<?php require_once "../includes/footer.php"; ?>

</body>
</html>