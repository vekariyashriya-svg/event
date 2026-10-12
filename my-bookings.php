<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$stmt = $conn->prepare("SELECT b.*, s.event_title FROM bookings b JOIN schedules s ON b.schedule_id = s.schedule_id WHERE b.user_id = ? ORDER BY b.booking_date DESC");
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings | DaisyDream</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .mb-page { background: #FAF8F5; padding: 60px 20px 80px; min-height: 100vh; }
        .mb-container { max-width: 1180px; margin: 0 auto; }
        .mb-hero { text-align: center; padding: 0 0 40px; border-bottom: 1px solid #E5DED8; margin-bottom: 45px; }
        .mb-label { display: inline-block; margin-bottom: 12px; color: #A8785C; font-size: 12px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; }
        .mb-hero h1 { margin: 0 0 14px; color: #292929; font-size: 42px; font-weight: 700; }
        .mb-hero p { color: #666; font-size: 16px; }
        .mb-table-card { background: #fff; border: 1px solid #E5E0DC; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 18px rgba(40,35,30,0.05); }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #F4EFEA; }
        th { text-align: left; padding: 16px 20px; font-size: 12px; color: #777; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        td { padding: 16px 20px; font-size: 14px; color: #333; border-top: 1px solid #F1EBE6; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-pending { background: #FEF3E2; color: #B7791F; }
        .badge-confirmed { background: #EAF6EC; color: #2F7D3B; }
        .badge-cancelled { background: #FDEAEA; color: #C0392B; }
        .empty { background: #fff; border: 1px solid #E5E0DC; border-radius: 10px; padding: 60px 40px; text-align: center; }
        .empty i { font-size: 50px; color: #D4B5A3; margin-bottom: 20px; display: block; }
        .empty h3 { color: #292929; font-size: 22px; margin: 0 0 10px; }
        .empty p { color: #777; font-size: 14px; margin-bottom: 25px; }
        .empty-btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 28px; background: #A8785C; color: #fff; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 600; }
        .empty-btn:hover { background: #93694F; }
        @media (max-width: 700px) {
            .mb-hero h1 { font-size: 32px; }
            .mb-table-card { overflow-x: auto; }
            table { min-width: 600px; }
        }
    </style>
</head>
<body>

<?php require_once "../includes/header.php"; ?>

<section class="mb-page">
    <div class="mb-container">

        <div class="mb-hero">
            <span class="mb-label">Account</span>
            <h1>My Bookings</h1>
            <p>Track all your event bookings in one place.</p>
        </div>

        <?php if (count($bookings) > 0): ?>
            <div class="mb-table-card">
                <table>
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Event</th>
                            <th>Event Date</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Booked On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $b): ?>
                            <tr>
                                <td>#<?php echo $b['booking_id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($b['event_title']); ?></strong></td>
                                <td><?php echo date('d M Y', strtotime($b['event_date'])); ?></td>
                                <td><?php echo htmlspecialchars($b['payment_method']); ?></td>
                                <td>
                                    <?php
                                    $sc = 'badge-pending';
                                    if ($b['status'] == 'Confirmed') $sc = 'badge-confirmed';
                                    elseif ($b['status'] == 'Cancelled') $sc = 'badge-cancelled';
                                    ?>
                                    <span class="badge <?php echo $sc; ?>"><?php echo htmlspecialchars($b['status']); ?></span>
                                </td>
                                <td><?php echo date('d M Y', strtotime($b['booking_date'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty">
                <i class="fas fa-calendar-times"></i>
                <h3>No Bookings Yet</h3>
                <p>You haven't made any bookings. Start exploring events now!</p>
                <a href="events.php" class="empty-btn">Browse Events</a>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php require_once "../includes/footer.php"; ?>

</body>
</html>