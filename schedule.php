<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/db.php";

$events = [];

/*
|--------------------------------------------------------------------------
| Fetch Event Schedule
|--------------------------------------------------------------------------
*/

try {

    $stmt = $conn->prepare("
        SELECT *
        FROM schedules
        ORDER BY event_date ASC, start_time ASC
    ");

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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Event Schedule | DaisyDream</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

    <style>

        /* =====================================================
           SCHEDULE PAGE
        ===================================================== */

        .schedule-page {
            min-height: 100vh;
            background: #FAF8F5;
        }


        /* =====================================================
           HERO
        ===================================================== */

        .schedule-hero {
            padding: 70px 20px 55px;
            text-align: center;
            background: #F4EFEA;
            border-bottom: 1px solid #E5DED8;
        }

        .schedule-label {
            display: inline-block;
            margin-bottom: 12px;
            color: #A8785C;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .schedule-hero h1 {
            margin: 0 0 13px;
            color: #292929;
            font-size: 42px;
            line-height: 1.15;
        }

        .schedule-hero p {
            max-width: 650px;
            margin: 0 auto;
            color: #6D6D6D;
            font-size: 15px;
            line-height: 1.7;
        }


        /* =====================================================
           SCHEDULE SECTION
        ===================================================== */

        .schedule-section {
            padding: 65px 20px 80px;
        }

        .schedule-container {
            max-width: 1150px;
            margin: 0 auto;
        }


        /* =====================================================
           SECTION TITLE
        ===================================================== */

        .schedule-heading {
            margin-bottom: 30px;
        }

        .schedule-heading h2 {
            margin: 0 0 8px;
            color: #292929;
            font-size: 28px;
        }

        .schedule-heading p {
            margin: 0;
            color: #777777;
            font-size: 14px;
        }


        /* =====================================================
           SCHEDULE LIST
        ===================================================== */

        .schedule-list {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }


        /* =====================================================
           SCHEDULE CARD
        ===================================================== */

        .schedule-card {
            display: grid;
            grid-template-columns: 120px 1fr auto;
            align-items: center;
            gap: 25px;

            padding: 23px;

            background: #FFFFFF;
            border: 1px solid #E4DED9;
            border-radius: 9px;

            box-shadow:
                0 5px 18px rgba(40, 35, 30, 0.04);

            transition:
                transform 0.2s ease,
                border-color 0.2s ease,
                box-shadow 0.2s ease;
        }

        .schedule-card:hover {
            transform: translateY(-2px);
            border-color: #D5C2B5;
            box-shadow:
                0 9px 24px rgba(40, 35, 30, 0.07);
        }


        /* =====================================================
           DATE BOX
        ===================================================== */

        .schedule-date {
            width: 100px;
            min-height: 92px;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            background: #F4EFEA;
            border: 1px solid #E2D9D2;
            border-radius: 8px;
        }

        .schedule-day {
            color: #292929;
            font-size: 27px;
            font-weight: 700;
            line-height: 1;
        }

        .schedule-month {
            margin-top: 7px;
            color: #A8785C;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .schedule-weekday {
            margin-top: 5px;
            color: #777777;
            font-size: 10px;
            text-transform: uppercase;
        }


        /* =====================================================
           EVENT CONTENT
        ===================================================== */

        .schedule-content {
            min-width: 0;
        }

        .schedule-content h3 {
            margin: 0 0 8px;
            color: #292929;
            font-size: 21px;
        }

        .schedule-description {
            margin: 0 0 12px;
            color: #6D6D6D;
            font-size: 13px;
            line-height: 1.65;
        }


        /* =====================================================
           EVENT META
        ===================================================== */

        .schedule-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .schedule-meta-item {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;

            background: #FAF8F5;
            border: 1px solid #E7E1DC;
            border-radius: 5px;

            color: #555555;
            font-size: 11px;
        }

        .schedule-price {
            color: #A8785C;
            font-weight: 700;
        }


        /* =====================================================
           BOOK BUTTON
        ===================================================== */

        .schedule-action {
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .schedule-book-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;

            min-width: 115px;
            padding: 11px 17px;

            border: 1px solid #A8785C;
            border-radius: 6px;

            background: #A8785C;
            color: #FFFFFF;

            text-decoration: none;

            font-size: 12px;
            font-weight: 600;

            transition: 0.2s ease;
        }

        .schedule-book-btn:hover {
            background: #93694F;
            border-color: #93694F;
        }


        /* =====================================================
           EMPTY STATE
        ===================================================== */

        .schedule-empty {
            padding: 60px 25px;

            text-align: center;

            background: #FFFFFF;
            border: 1px solid #E4DED9;
            border-radius: 9px;
        }

        .schedule-empty h3 {
            margin: 0 0 10px;
            color: #292929;
            font-size: 22px;
        }

        .schedule-empty p {
            margin: 0;
            color: #777777;
            font-size: 14px;
            line-height: 1.6;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 800px) {

            .schedule-card {
                grid-template-columns: 95px 1fr;
                gap: 20px;
            }

            .schedule-date {
                width: 90px;
            }

            .schedule-action {
                grid-column: 2;
                justify-content: flex-start;
            }

        }


        @media (max-width: 560px) {

            .schedule-hero {
                padding: 55px 17px 40px;
            }

            .schedule-hero h1 {
                font-size: 32px;
            }

            .schedule-section {
                padding: 45px 17px 60px;
            }

            .schedule-card {
                grid-template-columns: 1fr;
                gap: 18px;
                padding: 20px;
            }

            .schedule-date {
                width: 90px;
            }

            .schedule-action {
                grid-column: auto;
                width: 100%;
            }

            .schedule-book-btn {
                width: 100%;
            }

        }

    </style>

</head>


<body class="schedule-page">


<?php
require_once "../includes/header.php";
?>


<!-- =====================================================
     HERO
===================================================== -->

<section class="schedule-hero">

    <span class="schedule-label">
        Event Timeline
    </span>

    <h1>
        Event Schedule
    </h1>

    <p>
        Browse our complete event calendar and plan ahead
        for your next memorable occasion.
    </p>

</section>


<!-- =====================================================
     SCHEDULE
===================================================== -->

<section class="schedule-section">

    <div class="schedule-container">

        <div class="schedule-heading">

            <h2>
                Upcoming Events
            </h2>

            <p>
                View event dates, timings, pricing and booking options.
            </p>

        </div>


        <?php if (count($events) > 0): ?>


            <div class="schedule-list">


                <?php foreach ($events as $event): ?>

                    <?php

                    $eventTitle =
                        $event['event_title']
                        ?? $event['title']
                        ?? 'Event';

                    $description =
                        $event['event_description']
                        ?? $event['description']
                        ?? 'DaisyDream event experience.';

                    $eventDate =
                        $event['event_date']
                        ?? '';

                    $startTime =
                        $event['start_time']
                        ?? '';

                    $endTime =
                        $event['end_time']
                        ?? '';

                    $price =
                        $event['price']
                        ?? '';

                    $scheduleId =
                        $event['schedule_id']
                        ?? $event['id']
                        ?? '';

                    ?>


                    <article class="schedule-card">


                        <!-- DATE -->

                        <div class="schedule-date">

                            <?php if (!empty($eventDate)): ?>

                                <div class="schedule-day">

                                    <?php
                                    echo date(
                                        "d",
                                        strtotime($eventDate)
                                    );
                                    ?>

                                </div>


                                <div class="schedule-month">

                                    <?php
                                    echo date(
                                        "M",
                                        strtotime($eventDate)
                                    );
                                    ?>

                                </div>


                                <div class="schedule-weekday">

                                    <?php
                                    echo date(
                                        "D",
                                        strtotime($eventDate)
                                    );
                                    ?>

                                </div>

                            <?php else: ?>

                                <div class="schedule-day">
                                    --
                                </div>

                                <div class="schedule-month">
                                    Date
                                </div>

                            <?php endif; ?>

                        </div>


                        <!-- EVENT INFORMATION -->

                        <div class="schedule-content">

                            <h3>
                                <?php
                                echo htmlspecialchars($eventTitle);
                                ?>
                            </h3>


                            <p class="schedule-description">

                                <?php
                                echo htmlspecialchars($description);
                                ?>

                            </p>


                            <div class="schedule-meta">


                                <?php if (!empty($startTime)): ?>

                                    <span class="schedule-meta-item">

                                        Time:

                                        <?php
                                        echo htmlspecialchars(
                                            date(
                                                "h:i A",
                                                strtotime($startTime)
                                            )
                                        );
                                        ?>

                                        <?php if (!empty($endTime)): ?>

                                            -
                                            
                                            <?php
                                            echo htmlspecialchars(
                                                date(
                                                    "h:i A",
                                                    strtotime($endTime)
                                                )
                                            );
                                            ?>

                                        <?php endif; ?>

                                    </span>

                                <?php endif; ?>


                                <?php if ($price !== '' && $price !== null): ?>

                                    <span
                                        class="schedule-meta-item schedule-price"
                                    >

                                        Price:

                                        ₹<?php
                                        echo htmlspecialchars(
                                            number_format(
                                                (float)$price,
                                                2
                                            )
                                        );
                                        ?>

                                    </span>

                                <?php endif; ?>


                            </div>

                        </div>


                        <!-- BOOK -->

                        <div class="schedule-action">

                            <a
                                href="book-event.php?schedule_id=<?php echo urlencode($scheduleId); ?>"
                                class="schedule-book-btn"
                            >
                                Book Event
                            </a>

                        </div>


                    </article>


                <?php endforeach; ?>


            </div>


        <?php else: ?>


            <div class="schedule-empty">

                <h3>
                    No Events Scheduled
                </h3>

                <p>
                    There are currently no events available in the
                    schedule. New events will appear here once they
                    are added by the event management team.
                </p>

            </div>


        <?php endif; ?>


    </div>

</section>


<?php
require_once "../includes/footer.php";
?>


</body>

</html>