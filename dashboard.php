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

    $user = null;
}

if (!$user) {

    session_destroy();

    header("Location: ../auth/login.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| Session Information
|--------------------------------------------------------------------------
*/

$_SESSION['full_name'] = $user['full_name'];
$_SESSION['email'] = $user['email'];

/*
|--------------------------------------------------------------------------
| Statistics
|--------------------------------------------------------------------------
*/

$totalBookings = 0;
$upcomingBookings = 0;
$completedBookings = 0;
$availableEvents = 0;

try {

    $stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ?");
    $stmt->execute([$userId]);
    $totalBookings = (int) $stmt->fetchColumn();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ? AND event_date >= CURDATE()");
    $stmt->execute([$userId]);
    $upcomingBookings = (int) $stmt->fetchColumn();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ? AND event_date < CURDATE()");
    $stmt->execute([$userId]);
    $completedBookings = (int) $stmt->fetchColumn();

    $stmt = $conn->query("SELECT COUNT(*) FROM schedules WHERE event_date >= CURDATE()");
    $availableEvents = (int) $stmt->fetchColumn();

} catch (PDOException $e) {

    $totalBookings = 0;
    $upcomingBookings = 0;
    $completedBookings = 0;
    $availableEvents = 0;
}

/*
|--------------------------------------------------------------------------
| Recent Bookings
|--------------------------------------------------------------------------
*/

$recentBookings = [];

try {

    $stmt = $conn->prepare("
        SELECT
            b.booking_id,
            b.customer_name,
            b.event_date,
            b.status,
            b.booking_date,
            s.event_title
        FROM bookings b
        LEFT JOIN schedules s ON b.schedule_id = s.schedule_id
        WHERE b.user_id = ?
        ORDER BY b.booking_date DESC
        LIMIT 5
    ");

    $stmt->execute([$userId]);
    $recentBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $recentBookings = [];
}

/*
|--------------------------------------------------------------------------
| Upcoming Events
|--------------------------------------------------------------------------
*/

$upcomingEvents = [];

try {

    $stmt = $conn->query("
        SELECT
            schedule_id,
            event_title,
            event_description,
            event_date,
            start_time,
            end_time,
            price,
            image
        FROM schedules
        WHERE event_date >= CURDATE()
        ORDER BY event_date ASC
        LIMIT 3
    ");

    $upcomingEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $upcomingEvents = [];
}

require_once "../includes/header.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | DaisyDream</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>

        /* ============= DASHBOARD PAGE ============= */
        .dashboard-page { background: #FAF8F5; min-height: calc(100vh - 150px); padding: 65px 0 90px; }
        .dashboard-welcome { display: flex; justify-content: space-between; align-items: flex-end; gap: 30px; margin-bottom: 38px; }
        .dashboard-welcome-text { max-width: 720px; }
        .dashboard-welcome-text .section-tag { display: inline-block; margin-bottom: 12px; }
        .dashboard-welcome-text h1 { margin: 0 0 10px; color: #292929; font-size: 40px; line-height: 1.2; }
        .dashboard-welcome-text h1 span { color: #A8785C; }
        .dashboard-welcome-text p { margin: 0; color: #6B6B6B; font-size: 15px; line-height: 1.7; }
        .dashboard-member { min-width: 190px; padding: 17px 20px; background: #FFFFFF; border: 1px solid #E5E5E5; border-radius: 10px; text-align: right; }
        .dashboard-member span { display: block; color: #999999; font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 5px; }
        .dashboard-member strong { color: #3A3A3A; font-size: 14px; }

        /* ============= SUCCESS ALERT ============= */
        .dashboard-success {
            padding: 14px 18px;
            background: #F1F7F1;
            border: 1px solid #C9DDC9;
            color: #4E7650;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 14px;
        }

        /* ============= STAT CARDS ============= */
        .dashboard-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 28px; }
        .dashboard-stat { background: #FFFFFF; border: 1px solid #E5E5E5; border-radius: 12px; padding: 25px; box-shadow: 0 5px 20px rgba(41,41,41,0.035); }
        .dashboard-stat-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; }
        .dashboard-stat-label { color: #888888; font-size: 11px; font-weight: 600; letter-spacing: 0.8px; text-transform: uppercase; }
        .dashboard-stat-icon { width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border: 1px solid #E8DED8; border-radius: 8px; color: #A8785C; font-size: 15px; font-weight: 700; }
        .dashboard-stat-number { display: block; color: #292929; font-size: 30px; font-weight: 700; line-height: 1; margin-bottom: 8px; }
        .dashboard-stat-description { color: #999999; font-size: 12px; margin: 0; }

        /* ============= MAIN GRID ============= */
        .dashboard-main-grid { display: grid; grid-template-columns: minmax(0, 1.35fr) minmax(300px, 0.65fr); gap: 24px; margin-bottom: 24px; }

        /* ============= COMMON CARD ============= */
        .dashboard-card { background: #FFFFFF; border: 1px solid #E5E5E5; border-radius: 12px; padding: 28px; box-shadow: 0 5px 20px rgba(41,41,41,0.035); }
        .dashboard-card-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; margin-bottom: 25px; }
        .dashboard-card-header .section-tag { display: block; margin-bottom: 8px; }
        .dashboard-card-header h2 { margin: 0; color: #292929; font-size: 21px; }
        .dashboard-card-header p { margin: 7px 0 0; color: #777777; font-size: 13px; line-height: 1.5; }

        /* ============= PROFILE ============= */
        .profile-overview { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .profile-item { padding-bottom: 17px; border-bottom: 1px solid #EEEEEE; }
        .profile-item span { display: block; color: #999999; font-size: 11px; text-transform: uppercase; letter-spacing: 0.7px; margin-bottom: 7px; }
        .profile-item strong { display: block; color: #3A3A3A; font-size: 14px; font-weight: 600; line-height: 1.5; word-break: break-word; }
        .profile-item.full { grid-column: 1 / -1; }

        /* ============= BUTTONS ============= */
        .dashboard-outline-btn { display: inline-flex; align-items: center; justify-content: center; min-height: 40px; padding: 0 17px; border: 1px solid #D8D8D8; border-radius: 7px; color: #555555; background: #FFFFFF; text-decoration: none; font-size: 13px; font-weight: 600; transition: 0.2s ease; }
        .dashboard-outline-btn:hover { color: #A8785C; border-color: #A8785C; }

        /* ============= QUICK ACTIONS ============= */
        .dashboard-actions { display: flex; flex-direction: column; gap: 10px; }
        .dashboard-action { display: flex; align-items: center; justify-content: space-between; gap: 15px; padding: 15px 16px; border: 1px solid #E8E8E8; border-radius: 8px; text-decoration: none; transition: 0.2s ease; }
        .dashboard-action:hover { border-color: #CBAF9D; background: #FCFAF8; }
        .dashboard-action-content strong { display: block; color: #363636; font-size: 14px; margin-bottom: 4px; }
        .dashboard-action-content span { display: block; color: #999999; font-size: 12px; }
        .dashboard-action-arrow { color: #A8785C; font-size: 18px; }

        /* ============= BOOKINGS TABLE ============= */
        .dashboard-full-card { margin-bottom: 24px; }
        .dashboard-table-wrapper { width: 100%; overflow-x: auto; }
        .dashboard-table { width: 100%; border-collapse: collapse; min-width: 650px; }
        .dashboard-table th { padding: 13px 12px; background: #FAF8F5; border-bottom: 1px solid #E5E5E5; color: #777777; font-size: 11px; text-transform: uppercase; letter-spacing: 0.6px; text-align: left; font-weight: 600; }
        .dashboard-table td { padding: 17px 12px; border-bottom: 1px solid #EEEEEE; color: #555555; font-size: 13px; vertical-align: middle; }
        .dashboard-table tbody tr:last-child td { border-bottom: none; }
        .dashboard-table-event { color: #292929 !important; font-weight: 600; }
        .booking-status { display: inline-flex; padding: 5px 10px; border-radius: 20px; background: #F6F0EC; color: #93694F; font-size: 11px; font-weight: 600; }

        /* ============= UPCOMING EVENTS ============= */
        .dashboard-events { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }
        .dashboard-event { border: 1px solid #E7E7E7; border-radius: 10px; overflow: hidden; background: #FFFFFF; }
        .dashboard-event-top { padding: 19px; border-bottom: 1px solid #EEEEEE; background: #FCFBFA; }
        .dashboard-event-date { display: inline-flex; flex-direction: column; align-items: center; justify-content: center; width: 52px; height: 58px; border: 1px solid #DCC8BA; border-radius: 7px; background: #FFFFFF; }
        .dashboard-event-date strong { color: #A8785C; font-size: 19px; line-height: 1; }
        .dashboard-event-date span { color: #777777; font-size: 10px; text-transform: uppercase; margin-top: 4px; }
        .dashboard-event-content { padding: 19px; }
        .dashboard-event-content h3 { margin: 0 0 8px; color: #292929; font-size: 16px; }
        .dashboard-event-content p { margin: 0 0 13px; color: #777777; font-size: 12px; line-height: 1.6; }
        .dashboard-event-time { color: #A8785C; font-size: 12px; font-weight: 600; }

        /* ============= EMPTY STATE ============= */
        .dashboard-empty { padding: 35px 20px; text-align: center; border: 1px dashed #DCDCDC; border-radius: 8px; }
        .dashboard-empty h3 { margin: 0 0 7px; color: #444444; font-size: 16px; }
        .dashboard-empty p { margin: 0 0 18px; color: #999999; font-size: 13px; }
        .dashboard-primary-btn { display: inline-flex; align-items: center; justify-content: center; min-height: 42px; padding: 0 19px; background: #A8785C; border: 1px solid #A8785C; color: #FFFFFF; border-radius: 7px; text-decoration: none; font-size: 13px; font-weight: 600; transition: 0.2s ease; }
        .dashboard-primary-btn:hover { background: #93694F; border-color: #93694F; }

        /* ============= RESPONSIVE ============= */
        @media (max-width: 1050px) {
            .dashboard-stats { grid-template-columns: repeat(2, 1fr); }
            .dashboard-main-grid { grid-template-columns: 1fr; }
            .dashboard-events { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 700px) {
            .dashboard-page { padding: 45px 0 65px; }
            .dashboard-welcome { flex-direction: column; align-items: stretch; }
            .dashboard-welcome-text h1 { font-size: 32px; }
            .dashboard-member { text-align: left; }
            .dashboard-stats { grid-template-columns: 1fr 1fr; gap: 12px; }
            .dashboard-stat { padding: 18px; }
            .dashboard-stat-number { font-size: 25px; }
            .dashboard-card { padding: 21px 18px; }
            .dashboard-card-header { flex-direction: column; gap: 13px; }
            .profile-overview { grid-template-columns: 1fr; }
            .profile-item.full { grid-column: auto; }
            .dashboard-events { grid-template-columns: 1fr; }
        }
        @media (max-width: 480px) {
            .dashboard-stats { grid-template-columns: 1fr; }
            .dashboard-welcome-text h1 { font-size: 29px; }
        }
    </style>
</head>
<body>

<section class="dashboard-page">
    <div class="container">

        <!-- ============= SUCCESS MESSAGE ============= -->
        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'password_changed'): ?>
            <div class="dashboard-success">
                <i class="fas fa-check-circle"></i> Password updated successfully!
            </div>
        <?php endif; ?>

        <!-- ============= WELCOME ============= -->
        <div class="dashboard-welcome">
            <div class="dashboard-welcome-text">
                <span class="section-tag">MY DASHBOARD</span>
                <h1>
                    Welcome back, <span><?php echo htmlspecialchars($user['full_name']); ?></span>
                </h1>
                <p>Manage your profile, view your bookings and explore upcoming events from your DaisyDream account.</p>
            </div>
            <div class="dashboard-member">
                <span>Member Since</span>
                <strong><?php echo date("d M Y", strtotime($user['created_at'])); ?></strong>
            </div>
        </div>

        <!-- ============= STATISTICS ============= -->
        <div class="dashboard-stats">
            <div class="dashboard-stat">
                <div class="dashboard-stat-top">
                    <span class="dashboard-stat-label">Total Bookings</span>
                    <div class="dashboard-stat-icon">B</div>
                </div>
                <strong class="dashboard-stat-number"><?php echo $totalBookings; ?></strong>
                <p class="dashboard-stat-description">All your event bookings</p>
            </div>
            <div class="dashboard-stat">
                <div class="dashboard-stat-top">
                    <span class="dashboard-stat-label">Upcoming</span>
                    <div class="dashboard-stat-icon">U</div>
                </div>
                <strong class="dashboard-stat-number"><?php echo $upcomingBookings; ?></strong>
                <p class="dashboard-stat-description">Upcoming bookings</p>
            </div>
            <div class="dashboard-stat">
                <div class="dashboard-stat-top">
                    <span class="dashboard-stat-label">Completed</span>
                    <div class="dashboard-stat-icon">C</div>
                </div>
                <strong class="dashboard-stat-number"><?php echo $completedBookings; ?></strong>
                <p class="dashboard-stat-description">Completed events</p>
            </div>
            <div class="dashboard-stat">
                <div class="dashboard-stat-top">
                    <span class="dashboard-stat-label">Available Events</span>
                    <div class="dashboard-stat-icon">E</div>
                </div>
                <strong class="dashboard-stat-number"><?php echo $availableEvents; ?></strong>
                <p class="dashboard-stat-description">Events available to explore</p>
            </div>
        </div>

        <!-- ============= PROFILE + QUICK ACTIONS ============= -->
        <div class="dashboard-main-grid">

            <!-- Profile -->
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <div>
                        <span class="section-tag">ACCOUNT</span>
                        <h2>Profile Overview</h2>
                    </div>
                    <a href="edit-profile.php" class="dashboard-outline-btn">Edit Profile</a>
                </div>
                <div class="profile-overview">
                    <div class="profile-item">
                        <span>Full Name</span>
                        <strong><?php echo htmlspecialchars($user['full_name']); ?></strong>
                    </div>
                    <div class="profile-item">
                        <span>Email Address</span>
                        <strong><?php echo htmlspecialchars($user['email']); ?></strong>
                    </div>
                    <div class="profile-item">
                        <span>Phone Number</span>
                        <strong><?php echo !empty($user['phone']) ? htmlspecialchars($user['phone']) : "Not added"; ?></strong>
                    </div>
                    <div class="profile-item">
                        <span>User ID</span>
                        <strong><?php echo htmlspecialchars($user['user_id']); ?></strong>
                    </div>
                    <div class="profile-item full">
                        <span>Address</span>
                        <strong><?php echo !empty($user['address']) ? htmlspecialchars($user['address']) : "No address added"; ?></strong>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <div>
                        <span class="section-tag">QUICK ACCESS</span>
                        <h2>Quick Actions</h2>
                    </div>
                </div>
                <div class="dashboard-actions">

                    <a href="edit-profile.php" class="dashboard-action">
                        <div class="dashboard-action-content">
                            <strong>Edit Profile</strong>
                            <span>Update your personal information</span>
                        </div>
                        <span class="dashboard-action-arrow">→</span>
                    </a>

                    <a href="my-bookings.php" class="dashboard-action">
                        <div class="dashboard-action-content">
                            <strong>My Bookings</strong>
                            <span>View and manage your bookings</span>
                        </div>
                        <span class="dashboard-action-arrow">→</span>
                    </a>

                    <a href="events.php" class="dashboard-action">
                        <div class="dashboard-action-content">
                            <strong>Explore Events</strong>
                            <span>Discover upcoming events</span>
                        </div>
                        <span class="dashboard-action-arrow">→</span>
                    </a>

                    <a href="schedule.php" class="dashboard-action">
                        <div class="dashboard-action-content">
                            <strong>Event Schedule</strong>
                            <span>Check dates and timings</span>
                        </div>
                        <span class="dashboard-action-arrow">→</span>
                    </a>

                    <!-- ✅ CHANGE PASSWORD -->
                    <a href="../auth/forgot-password.php?mode=change" class="dashboard-action">
                        <div class="dashboard-action-content">
                            <strong>Change Password</strong>
                            <span>Update your account password</span>
                        </div>
                        <span class="dashboard-action-arrow">→</span>
                    </a>

                </div>
            </div>

        </div>

        <!-- ============= RECENT BOOKINGS ============= -->
        <div class="dashboard-card dashboard-full-card">
            <div class="dashboard-card-header">
                <div>
                    <span class="section-tag">BOOKINGS</span>
                    <h2>Recent Bookings</h2>
                    <p>Your latest event booking activity.</p>
                </div>
                <a href="my-bookings.php" class="dashboard-outline-btn">View All</a>
            </div>

            <?php if (!empty($recentBookings)): ?>
                <div class="dashboard-table-wrapper">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Event Date</th>
                                <th>Booking Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($recentBookings as $booking): ?>
                            <tr>
                                <td class="dashboard-table-event"><?php echo htmlspecialchars($booking['event_title'] ?? 'Event'); ?></td>
                                <td><?php echo !empty($booking['event_date']) ? date("d M Y", strtotime($booking['event_date'])) : "-"; ?></td>
                                <td><?php echo !empty($booking['booking_date']) ? date("d M Y", strtotime($booking['booking_date'])) : "-"; ?></td>
                                <td><span class="booking-status"><?php echo htmlspecialchars($booking['status'] ?? 'Pending'); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="dashboard-empty">
                    <h3>No bookings yet</h3>
                    <p>You have not made any event bookings yet.</p>
                    <a href="events.php" class="dashboard-primary-btn">Explore Events</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- ============= UPCOMING EVENTS ============= -->
        <div class="dashboard-card dashboard-full-card">
            <div class="dashboard-card-header">
                <div>
                    <span class="section-tag">EXPLORE</span>
                    <h2>Upcoming Events</h2>
                    <p>Discover events available for booking.</p>
                </div>
                <a href="schedule.php" class="dashboard-outline-btn">Full Schedule</a>
            </div>

            <?php if (!empty($upcomingEvents)): ?>
                <div class="dashboard-events">
                    <?php foreach ($upcomingEvents as $event): ?>
                        <div class="dashboard-event">
                            <div class="dashboard-event-top">
                                <div class="dashboard-event-date">
                                    <strong><?php echo date("d", strtotime($event['event_date'])); ?></strong>
                                    <span><?php echo date("M", strtotime($event['event_date'])); ?></span>
                                </div>
                            </div>
                            <div class="dashboard-event-content">
                                <h3><?php echo htmlspecialchars($event['event_title']); ?></h3>
                                <p>
                                    <?php
                                    $description = $event['event_description'] ?? '';
                                    echo htmlspecialchars(mb_substr($description, 0, 105));
                                    if (mb_strlen($description) > 105) echo "...";
                                    ?>
                                </p>
                                <div class="dashboard-event-time">
                                    <?php
                                    if (!empty($event['start_time'])) echo date("h:i A", strtotime($event['start_time']));
                                    if (!empty($event['end_time'])) echo " - " . date("h:i A", strtotime($event['end_time']));
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="dashboard-empty">
                    <h3>No upcoming events</h3>
                    <p>There are currently no upcoming events.</p>
                    <a href="schedule.php" class="dashboard-primary-btn">View Schedule</a>
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>

<?php require_once "../includes/footer.php"; ?>

</body>
</html>