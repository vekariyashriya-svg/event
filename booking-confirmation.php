<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id = intval($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT b.*, s.event_title FROM bookings b JOIN schedules s ON b.schedule_id = s.schedule_id WHERE b.booking_id = ? AND b.user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    header("Location: events.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmed | DaisyDream</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .confirm-page { background: #FAF8F5; padding: 70px 20px 90px; min-height: 100vh; }
        .confirm-container { max-width: 700px; margin: 0 auto; }
        .confirm-card { background: #fff; border: 1px solid #E5E0DC; border-radius: 12px; padding: 55px 45px; text-align: center; box-shadow: 0 5px 25px rgba(40,35,30,0.06); }
        .confirm-icon { width: 80px; height: 80px; background: #EAF6EC; color: #2F7D3B; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 38px; margin: 0 auto 25px; }
        .confirm-card h1 { color: #292929; font-size: 34px; margin: 0 0 12px; }
        .confirm-card > p { color: #666; font-size: 15px; margin-bottom: 35px; }
        .confirm-details { text-align: left; background: #FAF8F5; border-radius: 10px; padding: 25px; margin-bottom: 30px; border: 1px solid #F1EBE6; }
        .confirm-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #F1EBE6; font-size: 14px; }
        .confirm-row:last-child { border-bottom: none; }
        .confirm-row span { color: #777; }
        .confirm-row strong { color: #292929; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-pending { background: #FEF3E2; color: #B7791F; }
        .badge-confirmed { background: #EAF6EC; color: #2F7D3B; }
        .badge-cancelled { background: #FDEAEA; color: #C0392B; }
        .confirm-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .confirm-btn { display: inline-flex; align-items: center; gap: 8px; padding: 13px 28px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 600; transition: 0.2s; }
        .btn-olive { background: #A8785C; color: #fff; border: 1px solid #A8785C; }
        .btn-olive:hover { background: #93694F; border-color: #93694F; }
        .btn-outline { background: transparent; color: #444; border: 1px solid #CCC; }
        .btn-outline:hover { border-color: #A8785C; color: #A8785C; }
        @media (max-width: 600px) {
            .confirm-card { padding: 35px 22px; }
            .confirm-card h1 { font-size: 26px; }
            .confirm-actions { flex-direction: column; }
            .confirm-btn { justify-content: center; }
        }
    </style>
</head>
<body>

<?php require_once "../includes/header.php"; ?>

<section class="confirm-page">
    <div class="confirm-container">
        <div class="confirm-card">
            <div class="confirm-icon"><i class="fas fa-check"></i></div>
            <h1>Booking Submitted!</h1>
            <p>Thank you, <?php echo htmlspecialchars($booking['customer_name']); ?>. Your booking has been received.</p>

            <div class="confirm-details">
                <div class="confirm-row"><span>Booking ID</span><strong>#<?php echo $booking['booking_id']; ?></strong></div>
                <div class="confirm-row"><span>Event</span><strong><?php echo htmlspecialchars($booking['event_title']); ?></strong></div>
                <div class="confirm-row"><span>Event Date</span><strong><?php echo date('d M Y', strtotime($booking['event_date'])); ?></strong></div>
                <div class="confirm-row"><span>Payment Method</span><strong><?php echo htmlspecialchars($booking['payment_method']); ?></strong></div>
                <div class="confirm-row"><span>Status</span><strong><span class="badge badge-pending"><?php echo htmlspecialchars($booking['status']); ?></span></strong></div>
                <div class="confirm-row"><span>Booking Date</span><strong><?php echo date('d M Y', strtotime($booking['booking_date'])); ?></strong></div>
            </div>

            <div class="confirm-actions">
                <a href="my-bookings.php" class="confirm-btn btn-olive">View My Bookings</a>
                <a href="events.php" class="confirm-btn btn-outline">Browse More Events</a>
            </div>
        </div>
    </div>
</section>

<?php require_once "../includes/footer.php"; ?>

</body>
</html>