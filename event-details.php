<?php
session_start();
require_once "../config/db.php";

$id = intval($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM schedules WHERE schedule_id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    header("Location: events.php");
    exit();
}

$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($event['event_title']); ?> | DaisyDream</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .detail-page { background: #FAF8F5; padding: 60px 20px 80px; min-height: 100vh; }
        .detail-container { max-width: 1180px; margin: 0 auto; }
        .detail-back { display: inline-flex; align-items: center; gap: 8px; color: #A8785C; font-size: 14px; margin-bottom: 25px; text-decoration: none; }
        .detail-back:hover { text-decoration: underline; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 45px; align-items: start; background: #fff; border: 1px solid #E5E0DC; border-radius: 12px; overflow: hidden; padding: 0; }
        .detail-image { width: 100%; height: 100%; min-height: 450px; background: #F1EBE6; overflow: hidden; }
        .detail-image img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .detail-image .event-placeholder { height: 100%; display: flex; align-items: center; justify-content: center; color: #A8785C; font-weight: 600; }
        .detail-content { padding: 45px 40px; }
        .detail-category { color: #A8785C; font-size: 12px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 12px; }
        .detail-content h1 { color: #292929; font-size: 36px; margin: 0 0 20px; }
        .detail-content p { color: #666; font-size: 15px; line-height: 1.8; margin-bottom: 25px; }
        .detail-info { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; padding: 22px 0; border-top: 1px solid #F1EBE6; border-bottom: 1px solid #F1EBE6; margin-bottom: 25px; }
        .detail-info-item p { font-size: 11px; color: #999; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
        .detail-info-item strong { font-size: 15px; color: #292929; }
        .detail-price { font-size: 36px; color: #A8785C; font-family: Georgia, serif; font-weight: 700; margin-bottom: 25px; }
        .detail-price span { font-size: 13px; color: #777; font-weight: 400; font-family: Arial, sans-serif; }
        .detail-btn { display: inline-flex; align-items: center; gap: 8px; padding: 14px 32px; background: #A8785C; color: #fff; border: 1px solid #A8785C; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 600; transition: 0.2s; }
        .detail-btn:hover { background: #93694F; border-color: #93694F; }
        .detail-sections { margin-top: 45px; display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .detail-section-card { background: #fff; border: 1px solid #E5E0DC; border-radius: 10px; padding: 30px; }
        .detail-section-card h3 { color: #292929; font-size: 20px; margin: 0 0 15px; }
        .detail-section-card p, .detail-section-card li { color: #666; font-size: 14.5px; line-height: 1.8; }
        .detail-section-card ul { list-style: none; padding: 0; }
        .detail-section-card ul li { padding: 6px 0; }
        .detail-section-card ul li::before { content: "✓ "; color: #A8785C; font-weight: 700; margin-right: 8px; }
        @media (max-width: 800px) {
            .detail-grid, .detail-sections { grid-template-columns: 1fr; }
            .detail-image { min-height: 280px; }
            .detail-content { padding: 30px 25px; }
            .detail-content h1 { font-size: 28px; }
        }
    </style>
</head>
<body>

<?php require_once "../includes/header.php"; ?>

<section class="detail-page">
    <div class="detail-container">

        <a href="events.php" class="detail-back"><i class="fas fa-arrow-left"></i> Back to Events</a>

        <div class="detail-grid">
            <div class="detail-image">
                <?php if (!empty($event['image'])): ?>
                    <img src="../assets/images/<?php echo htmlspecialchars($event['image']); ?>" alt="<?php echo htmlspecialchars($event['event_title']); ?>">
                <?php else: ?>
                    <div class="event-placeholder">DaisyDream Event</div>
                <?php endif; ?>
            </div>

            <div class="detail-content">
                <div class="detail-category">Featured Event</div>
                <h1><?php echo htmlspecialchars($event['event_title']); ?></h1>
                <p><?php echo nl2br(htmlspecialchars($event['event_description'])); ?></p>

                <div class="detail-info">
                    <div class="detail-info-item">
                        <p>Event Date</p>
                        <strong><?php echo date('d M Y', strtotime($event['event_date'])); ?></strong>
                    </div>
                    <div class="detail-info-item">
                        <p>Time</p>
                        <strong><?php echo htmlspecialchars($event['start_time']); ?> - <?php echo htmlspecialchars($event['end_time']); ?></strong>
                    </div>
                    <div class="detail-info-item">
                        <p>Category</p>
                        <strong>Premium</strong>
                    </div>
                    <div class="detail-info-item">
                        <p>Availability</p>
                        <strong style="color:#2f7d3b;">Available</strong>
                    </div>
                </div>

                <div class="detail-price">₹<?php echo number_format($event['price']); ?> <span>starting price</span></div>

                <?php if ($isLoggedIn): ?>
                    <a href="book-event.php?id=<?php echo $event['schedule_id']; ?>" class="detail-btn">
                        Book This Event <i class="fas fa-arrow-right"></i>
                    </a>
                <?php else: ?>
                    <a href="../auth/login.php" class="detail-btn">
                        <i class="fas fa-lock"></i> Login to Book
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="detail-sections">
            <div class="detail-section-card">
                <h3>About This Event</h3>
                <p><?php echo nl2br(htmlspecialchars($event['event_description'])); ?></p>
            </div>

            <div class="detail-section-card">
                <h3>What You Can Expect</h3>
                <ul>
                    <li>Professional event coordination</li>
                    <li>Curated venue and decoration</li>
                    <li>Complete event management support</li>
                    <li>Dedicated team for your event</li>
                    <li>High-quality service and hospitality</li>
                </ul>
            </div>
        </div>

    </div>
</section>

<?php require_once "../includes/footer.php"; ?>

</body>
</html>