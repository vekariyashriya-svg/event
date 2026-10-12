<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id = intval($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM schedules WHERE schedule_id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    header("Location: events.php");
    exit();
}

$userStmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$userStmt->execute([$_SESSION['user_id']]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = trim($_POST['customer_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $event_date = $_POST['event_date'];
    $payment_method = $_POST['payment_method'];
    $status = "Pending";

    if (empty($customer_name) || empty($email) || empty($phone) || empty($address) || empty($event_date) || empty($payment_method)) {
        $error = "Please fill in all fields.";
    } else {
        $insert = $conn->prepare("INSERT INTO bookings (user_id, schedule_id, customer_name, email, phone, address, event_date, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert->execute([$_SESSION['user_id'], $id, $customer_name, $email, $phone, $address, $event_date, $payment_method, $status]);
        $booking_id = $conn->lastInsertId();
        header("Location: booking-confirmation.php?id=" . $booking_id);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Event | DaisyDream</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ============= SAME DESIGN AS EVENTS PAGE ============= */
        .booking-page { background: #FAF8F5; padding: 60px 20px 80px; min-height: 100vh; }
        .booking-container { max-width: 1180px; margin: 0 auto; }
        .booking-hero { text-align: center; padding: 0 0 40px; border-bottom: 1px solid #E5DED8; margin-bottom: 45px; }
        .booking-label { display: inline-block; margin-bottom: 12px; color: #A8785C; font-size: 12px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; }
        .booking-hero h1 { margin: 0 0 14px; color: #292929; font-size: 42px; line-height: 1.15; font-weight: 700; }
        .booking-hero p { max-width: 650px; margin: 0 auto; color: #666; font-size: 16px; line-height: 1.7; }

        .booking-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 35px; align-items: start; }

        /* Form Card */
        .booking-form-card { background: #fff; border: 1px solid #E5E0DC; border-radius: 10px; padding: 40px; box-shadow: 0 5px 18px rgba(40,35,30,0.05); }
        .booking-form-card h2 { margin: 0 0 8px; color: #292929; font-size: 26px; }
        .booking-form-card > p { color: #777; font-size: 14px; margin-bottom: 30px; }

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; color: #444; font-size: 12px; font-weight: 700; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 13px 15px; border: 1px solid #E5DFD9;
            border-radius: 8px; background: #FAF8F5; color: #292929;
            font-size: 14px; outline: none; transition: 0.25s;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            border-color: #A8785C; background: #fff;
            box-shadow: 0 0 0 3px rgba(168,120,92,0.08);
        }
        .form-group textarea { resize: vertical; min-height: 90px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }

        .booking-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 14px 20px; border: 1px solid #A8785C; border-radius: 6px; background: #A8785C; color: #fff; text-decoration: none; font-size: 14px; font-weight: 600; cursor: pointer; transition: 0.2s; margin-top: 10px; }
        .booking-btn:hover { background: #93694F; border-color: #93694F; }

        /* Summary Card */
        .booking-summary { background: #fff; border: 1px solid #E5E0DC; border-radius: 10px; padding: 30px; box-shadow: 0 5px 18px rgba(40,35,30,0.05); position: sticky; top: 100px; }
        .booking-summary h3 { margin: 0 0 20px; color: #292929; font-size: 22px; }
        .summary-image { width: 100%; height: 200px; border-radius: 8px; overflow: hidden; margin-bottom: 20px; background: #F1EBE6; }
        .summary-image img { width: 100%; height: 100%; object-fit: cover; }
        .summary-image .event-placeholder { height: 100%; display: flex; align-items: center; justify-content: center; color: #A8785C; font-weight: 600; }
        .summary-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #F1EBE6; font-size: 14px; }
        .summary-row:last-of-type { border-bottom: none; }
        .summary-row span { color: #777; }
        .summary-row strong { color: #292929; }
        .summary-total { display: flex; justify-content: space-between; align-items: center; padding-top: 18px; margin-top: 10px; border-top: 2px solid #F1EBE6; }
        .summary-total span { color: #777; }
        .summary-total strong { font-size: 26px; color: #A8785C; font-family: Georgia, serif; }

        .form-error { padding: 12px 15px; border-radius: 8px; background: #FEF2F2; border: 1px solid #FAD4D4; color: #C0392B; font-size: 13.5px; margin-bottom: 20px; }

        @media (max-width: 900px) {
            .booking-grid { grid-template-columns: 1fr; }
            .booking-summary { position: static; }
            .booking-hero h1 { font-size: 32px; }
        }
        @media (max-width: 600px) {
            .booking-form-card { padding: 25px 20px; }
            .form-row { grid-template-columns: 1fr; gap: 0; }
        }
    </style>
</head>
<body>

<?php require_once "../includes/header.php"; ?>

<section class="booking-page">
    <div class="booking-container">

        <a href="event-details.php?id=<?php echo $event['schedule_id']; ?>" class="detail-back" style="display:inline-flex;align-items:center;gap:8px;color:#A8785C;font-size:14px;margin-bottom:25px;text-decoration:none;">
            <i class="fas fa-arrow-left"></i> Back to Event
        </a>

        <div class="booking-hero">
            <span class="booking-label">DaisyDream Booking</span>
            <h1>Book Your Event</h1>
            <p>Complete the form below to submit your event booking request.</p>
        </div>

        <div class="booking-grid">

            <!-- FORM -->
            <div class="booking-form-card">
                <h2>Your Details</h2>
                <p>Please verify your information before submitting the booking.</p>

                <?php if (!empty($error)): ?>
                    <div class="form-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Customer Name</label>
                        <input type="text" name="customer_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Event Date</label>
                            <input type="date" name="event_date" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Payment Method</label>
                            <select name="payment_method" required>
                                <option value="">Select Payment Method</option>
                                <option value="Cash">Cash on Event</option>
                                <option value="UPI">UPI</option>
                                <option value="Card">Card</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="booking-btn">
                        Submit Booking <i class="fas fa-arrow-right"></i>
                    </button>
                </form>
            </div>

            <!-- SUMMARY -->
            <div class="booking-summary">
                <h3>Booking Summary</h3>

                <div class="summary-image">
                    <?php if (!empty($event['image'])): ?>
                        <img src="../assets/images/<?php echo htmlspecialchars($event['image']); ?>" alt="<?php echo htmlspecialchars($event['event_title']); ?>">
                    <?php else: ?>
                        <div class="event-placeholder">DaisyDream Event</div>
                    <?php endif; ?>
                </div>

                <div class="summary-row">
                    <span>Event</span>
                    <strong><?php echo htmlspecialchars($event['event_title']); ?></strong>
                </div>
                <div class="summary-row">
                    <span>Date</span>
                    <strong><?php echo date('d M Y', strtotime($event['event_date'])); ?></strong>
                </div>
                <div class="summary-row">
                    <span>Time</span>
                    <strong><?php echo htmlspecialchars($event['start_time']); ?> - <?php echo htmlspecialchars($event['end_time']); ?></strong>
                </div>
                <div class="summary-total">
                    <span>Total</span>
                    <strong>₹<?php echo number_format($event['price']); ?></strong>
                </div>
            </div>

        </div>
    </div>
</section>

<?php require_once "../includes/footer.php"; ?>

</body>
</html>