<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/db.php";

$events = [];

try {
    $stmt = $conn->prepare("SELECT * FROM schedules ORDER BY event_date ASC");
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $events = [];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Events | DaisyDream</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* ============= SAME EVENTS PAGE CSS ============= */
        .events-page { background: #FAF8F5; min-height: 100vh; }
        .events-hero { padding: 75px 20px 55px; text-align: center; background: #F4EFEA; border-bottom: 1px solid #E5DED8; }
        .events-label { display: inline-block; margin-bottom: 12px; color: #A8785C; font-size: 12px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; }
        .events-hero h1 { margin: 0 0 14px; color: #292929; font-size: 42px; line-height: 1.15; font-weight: 700; }
        .events-hero p { max-width: 650px; margin: 0 auto; color: #666666; font-size: 16px; line-height: 1.7; }
        .events-section { padding: 65px 20px 80px; }
        .events-container { max-width: 1180px; margin: 0 auto; }
        .events-heading { margin-bottom: 35px; }
        .events-heading h2 { margin: 0 0 8px; color: #292929; font-size: 28px; }
        .events-heading p { margin: 0; color: #777777; font-size: 14px; }
        .events-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; }
        .event-card { display: flex; flex-direction: column; min-height: 330px; background: #FFFFFF; border: 1px solid #E5E0DC; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 18px rgba(40,35,30,0.05); transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease; }
        .event-card:hover { transform: translateY(-4px); border-color: #D5C2B5; box-shadow: 0 10px 25px rgba(40,35,30,0.09); }
        .event-image { width: 100%; height: 190px; background: #EEE8E3; overflow: hidden; }
        .event-image img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .event-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #A8785C; font-size: 14px; font-weight: 600; background: #F1EBE6; }
        .event-content { display: flex; flex-direction: column; flex: 1; padding: 22px; }
        .event-content h3 { margin: 0 0 10px; color: #292929; font-size: 21px; }
        .event-content p { margin: 0 0 12px; color: #6C6C6C; font-size: 14px; line-height: 1.7; }
        .event-meta-info { display: flex; justify-content: space-between; font-size: 13px; color: #777; margin-bottom: 15px; padding-bottom: 12px; border-bottom: 1px solid #F1EBE6; }
        .event-meta-info strong { color: #A8785C; }
        .event-btn { display: inline-flex; align-items: center; justify-content: center; width: fit-content; margin-top: auto; padding: 11px 18px; border: 1px solid #A8785C; border-radius: 6px; background: #A8785C; color: #FFFFFF; text-decoration: none; font-size: 13px; font-weight: 600; transition: 0.2s ease; }
        .event-btn:hover { background: #93694F; border-color: #93694F; }
        .events-empty { padding: 55px 25px; text-align: center; background: #FFFFFF; border: 1px solid #E5E0DC; border-radius: 10px; }
        .events-empty h3 { margin: 0 0 10px; color: #292929; font-size: 22px; }
        .events-empty p { margin: 0; color: #777777; font-size: 14px; }
        @media (max-width: 900px) { .events-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px) {
            .events-hero { padding: 55px 18px 40px; }
            .events-hero h1 { font-size: 32px; }
            .events-hero p { font-size: 14px; }
            .events-section { padding: 45px 17px 60px; }
            .events-grid { grid-template-columns: 1fr; }
            .event-image { height: 200px; }
        }
    </style>
</head>
<body class="events-page">

<?php require_once "../includes/header.php"; ?>

<section class="events-hero">
    <span class="events-label">DaisyDream Events</span>
    <h1>Discover Events</h1>
    <p>Explore beautifully planned events and choose the perfect experience for your special occasion.</p>
</section>

<section class="events-section">
    <div class="events-container">

        <div class="events-heading">
            <h2>Our Events</h2>
            <p>Choose from our available events.</p>
        </div>

        <?php if (count($events) > 0): ?>

            <div class="events-grid">
                <?php foreach ($events as $event): ?>

                    <article class="event-card">
                        <div class="event-image">
                            <?php if (!empty($event['image'])): ?>
                                <img src="../assets/images/<?php echo htmlspecialchars($event['image']); ?>" alt="<?php echo htmlspecialchars($event['event_title']); ?>">
                            <?php else: ?>
                                <div class="event-placeholder">DaisyDream Events</div>
                            <?php endif; ?>
                        </div>

                        <div class="event-content">
                            <h3><?php echo htmlspecialchars($event['event_title']); ?></h3>
                            <p><?php echo htmlspecialchars(substr($event['event_description'], 0, 90)) . '...'; ?></p>

                            <div class="event-meta-info">
                                <span>📅 <?php echo date('d M Y', strtotime($event['event_date'])); ?></span>
                                <strong>₹<?php echo number_format($event['price']); ?></strong>
                            </div>

                            <a href="event-details.php?id=<?php echo $event['schedule_id']; ?>" class="event-btn">
                                View Details
                            </a>
                        </div>
                    </article>

                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <div class="events-empty">
                <h3>No Events Available</h3>
                <p>Events will appear here once they are added to the system.</p>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php require_once "../includes/footer.php"; ?>

</body>
</html>