<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/db.php";


/*
|--------------------------------------------------------------------------
| Fetch Upcoming Events
|--------------------------------------------------------------------------
*/

$events = [];

try {

    /*
     * First try schedules table because your project
     * already uses schedules for upcoming events.
     */

    $stmt = $conn->prepare("
        SELECT *
        FROM schedules
        WHERE event_date >= CURDATE()
        ORDER BY event_date ASC, start_time ASC
        LIMIT 6
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

    <title>DaisyDream | Event Management System</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

    <style>

        /* =====================================================
           HOME PAGE
        ===================================================== */

        .home-page {
            background: #FAF8F5;
            color: #292929;
        }


        /* =====================================================
           HERO
        ===================================================== */

        .home-hero {
            min-height: 560px;
            display: flex;
            align-items: center;
            background: #F4EFEA;
            border-bottom: 1px solid #E5DED8;
        }

        .home-hero-container {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 75px 22px;
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            align-items: center;
            gap: 55px;
        }


        .hero-content {
            max-width: 620px;
        }

        .hero-label {
            display: inline-block;
            margin-bottom: 18px;
            color: #A8785C;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 2.2px;
            text-transform: uppercase;
        }

        .hero-content h1 {
            margin: 0 0 20px;
            color: #292929;
            font-size: 52px;
            line-height: 1.12;
            font-weight: 700;
            letter-spacing: -1px;
        }

        .hero-content h1 span {
            color: #A8785C;
            font-style: italic;
        }

        .hero-content p {
            max-width: 590px;
            margin: 0 0 30px;
            color: #666666;
            font-size: 16px;
            line-height: 1.8;
        }


        /* =====================================================
           HERO BUTTONS
        ===================================================== */

        .hero-buttons {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .hero-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 22px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: 0.2s ease;
        }

        .hero-btn-primary {
            background: #A8785C;
            border: 1px solid #A8785C;
            color: #FFFFFF;
        }

        .hero-btn-primary:hover {
            background: #93694F;
            border-color: #93694F;
        }

        .hero-btn-secondary {
            background: #FFFFFF;
            border: 1px solid #D5CDC7;
            color: #4A4A4A;
        }

        .hero-btn-secondary:hover {
            border-color: #A8785C;
            color: #A8785C;
        }


        /* =====================================================
           HERO IMAGE
        ===================================================== */

        .hero-image {
            height: 410px;
            overflow: hidden;
            border-radius: 10px;
            background: #E9E1DB;
            border: 1px solid #DED5CF;
            box-shadow: 0 12px 30px rgba(40, 35, 30, 0.08);
        }

        .hero-image img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
        }


        /* =====================================================
           SERVICES
        ===================================================== */

        .services-section {
            padding: 75px 20px;
            background: #FFFFFF;
        }

        .home-container {
            max-width: 1160px;
            margin: 0 auto;
        }

        .section-heading {
            max-width: 650px;
            margin: 0 auto 40px;
            text-align: center;
        }

        .section-label {
            display: inline-block;
            margin-bottom: 10px;
            color: #A8785C;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .section-heading h2 {
            margin: 0 0 12px;
            color: #292929;
            font-size: 34px;
        }

        .section-heading p {
            margin: 0;
            color: #707070;
            font-size: 15px;
            line-height: 1.7;
        }


        .services-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 22px;
        }

        .service-card {
            padding: 28px 22px;
            background: #FFFFFF;
            border: 1px solid #E5E0DC;
            border-radius: 9px;
            box-shadow: 0 5px 18px rgba(40, 35, 30, 0.04);
        }

        .service-number {
            margin-bottom: 18px;
            color: #A8785C;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .service-card h3 {
            margin: 0 0 10px;
            color: #292929;
            font-size: 19px;
        }

        .service-card p {
            margin: 0;
            color: #6D6D6D;
            font-size: 14px;
            line-height: 1.7;
        }


        /* =====================================================
           UPCOMING EVENTS
        ===================================================== */

        .upcoming-section {
            padding: 75px 20px;
            background: #FAF8F5;
        }

        .upcoming-heading {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 35px;
        }

        .upcoming-heading h2 {
            margin: 0 0 8px;
            color: #292929;
            font-size: 32px;
        }

        .upcoming-heading p {
            margin: 0;
            color: #727272;
            font-size: 14px;
        }

        .view-all {
            color: #A8785C;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }

        .view-all:hover {
            color: #93694F;
        }


        /* =====================================================
           EVENT GRID
        ===================================================== */

        .home-events-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .home-event-card {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: #FFFFFF;
            border: 1px solid #E4DED9;
            border-radius: 9px;
            box-shadow: 0 5px 18px rgba(40, 35, 30, 0.05);
        }


        .home-event-image {
            width: 100%;
            height: 185px;
            background: #EEE8E3;
        }

        .home-event-image img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
        }

        .home-event-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #A8785C;
            background: #F0EAE5;
            font-size: 13px;
            font-weight: 600;
        }


        .home-event-content {
            padding: 21px;
        }

        .home-event-date {
            margin-bottom: 9px;
            color: #A8785C;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.7px;
            text-transform: uppercase;
        }

        .home-event-content h3 {
            margin: 0 0 10px;
            color: #292929;
            font-size: 19px;
        }

        .home-event-content p {
            margin: 0 0 18px;
            color: #6E6E6E;
            font-size: 13px;
            line-height: 1.65;
        }

        .home-event-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            border: 1px solid #A8785C;
            border-radius: 6px;
            background: #A8785C;
            color: #FFFFFF;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
        }

        .home-event-btn:hover {
            background: #93694F;
            border-color: #93694F;
        }


        /* =====================================================
           EMPTY EVENTS
        ===================================================== */

        .home-events-empty {
            padding: 55px 25px;
            text-align: center;
            background: #FFFFFF;
            border: 1px solid #E4DED9;
            border-radius: 9px;
        }

        .home-events-empty h3 {
            margin: 0 0 10px;
            color: #292929;
            font-size: 21px;
        }

        .home-events-empty p {
            margin: 0;
            color: #777777;
            font-size: 14px;
        }


        /* =====================================================
           CTA
        ===================================================== */

        .home-cta {
            padding: 70px 20px;
            background: #FFFFFF;
            border-top: 1px solid #E8E3DF;
        }

        .home-cta-box {
            max-width: 1050px;
            margin: 0 auto;
            padding: 48px 45px;
            text-align: center;
            background: #F4EFEA;
            border: 1px solid #E2D9D2;
            border-radius: 10px;
        }

        .home-cta-box h2 {
            margin: 0 0 12px;
            color: #292929;
            font-size: 30px;
        }

        .home-cta-box p {
            max-width: 650px;
            margin: 0 auto 25px;
            color: #6D6D6D;
            font-size: 14px;
            line-height: 1.7;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 950px) {

            .home-hero-container {
                grid-template-columns: 1fr;
            }

            .hero-content {
                max-width: 750px;
            }

            .hero-image {
                height: 340px;
            }

            .services-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .home-events-grid {
                grid-template-columns: repeat(2, 1fr);
            }

        }


        @media (max-width: 620px) {

            .home-hero-container {
                padding: 55px 17px;
            }

            .hero-content h1 {
                font-size: 37px;
            }

            .hero-content p {
                font-size: 14px;
            }

            .hero-image {
                height: 280px;
            }

            .services-section,
            .upcoming-section {
                padding: 55px 17px;
            }

            .section-heading h2,
            .upcoming-heading h2 {
                font-size: 28px;
            }

            .services-grid,
            .home-events-grid {
                grid-template-columns: 1fr;
            }

            .upcoming-heading {
                display: block;
            }

            .view-all {
                display: inline-block;
                margin-top: 15px;
            }

            .home-cta {
                padding: 50px 17px;
            }

            .home-cta-box {
                padding: 35px 20px;
            }

        }

    </style>

</head>


<body class="home-page">


<?php
require_once "../includes/header.php";
?>


<!-- =====================================================
     HERO
===================================================== -->

<section class="home-hero">

    <div class="home-hero-container">

        <div class="hero-content">

            <span class="hero-label">
                Event Management System
            </span>

            <h1>
                Creating Beautiful
                <span>Moments</span>,
                One Event at a Time
            </h1>

            <p>
                From intimate celebrations to grand occasions,
                DaisyDream helps you plan, organize and experience
                memorable events with ease.
            </p>

            <div class="hero-buttons">

                <a
                    href="events.php"
                    class="hero-btn hero-btn-primary"
                >
                    Explore Events
                </a>

                <a
                    href="contact.php"
                    class="hero-btn hero-btn-secondary"
                >
                    Plan an Event
                </a>

            </div>

        </div>


        <div class="hero-image">

            <img
                src="https://images.unsplash.com/photo-1519167758481-83f550bb49b3?q=80&w=800&auto=format&fit=crop"
                alt="DaisyDream Event"
            >

        </div>

    </div>

</section>


<!-- =====================================================
     SERVICES
===================================================== -->

<section class="services-section">

    <div class="home-container">

        <div class="section-heading">

            <span class="section-label">
                Our Services
            </span>

            <h2>
                What We Offer
            </h2>

            <p>
                Professional event services designed to make
                every moment memorable.
            </p>

        </div>


        <div class="services-grid">

            <div class="service-card">

                <div class="service-number">
                    01
                </div>

                <h3>
                    Event Planning
                </h3>

                <p>
                    Comprehensive planning services to bring
                    your vision to life.
                </p>

            </div>


            <div class="service-card">

                <div class="service-number">
                    02
                </div>

                <h3>
                    Venue Management
                </h3>

                <p>
                    Find and book the perfect venue for
                    your special event.
                </p>

            </div>


            <div class="service-card">

                <div class="service-number">
                    03
                </div>

                <h3>
                    Weddings
                </h3>

                <p>
                    Make your special day truly
                    unforgettable.
                </p>

            </div>


            <div class="service-card">

                <div class="service-number">
                    04
                </div>

                <h3>
                    Corporate Events
                </h3>

                <p>
                    Professional events that leave a
                    lasting impression.
                </p>

            </div>

        </div>

    </div>

</section>


<!-- =====================================================
     UPCOMING EVENTS
===================================================== -->

<section class="upcoming-section">

    <div class="home-container">

        <div class="upcoming-heading">

            <div>

                <span class="section-label">
                    Featured Events
                </span>

                <h2>
                    Upcoming Events
                </h2>

                <p>
                    Explore our curated selection of upcoming events.
                </p>

            </div>


            <a
                href="events.php"
                class="view-all"
            >
                View All Events →
            </a>

        </div>


        <?php if (count($events) > 0): ?>

            <div class="home-events-grid">

                <?php foreach ($events as $event): ?>

                    <?php

                    $eventTitle =
                        $event['event_title']
                        ?? $event['title']
                        ?? 'Upcoming Event';

                    $description =
                        $event['event_description']
                        ?? $event['description']
                        ?? 'Join us for a memorable DaisyDream event experience.';

                    $eventDate =
                        $event['event_date']
                        ?? '';

                    $startTime =
                        $event['start_time']
                        ?? '';

                    $price =
                        $event['price']
                        ?? '';

                    $image =
                        $event['image']
                        ?? '';

                    $scheduleId =
                        $event['schedule_id']
                        ?? $event['id']
                        ?? '';

                    ?>

                    <article class="home-event-card">


                        <!-- IMAGE -->

                        <div class="home-event-image">

                            <?php if (!empty($image)): ?>

                                <img
                                    src="../assets/images/<?php echo htmlspecialchars($image); ?>"
                                    alt="<?php echo htmlspecialchars($eventTitle); ?>"
                                >

                            <?php else: ?>

                                <div class="home-event-placeholder">
                                    DaisyDream Event
                                </div>

                            <?php endif; ?>

                        </div>


                        <!-- CONTENT -->

                        <div class="home-event-content">

                            <?php if (!empty($eventDate)): ?>

                                <div class="home-event-date">

                                    <?php
                                    echo htmlspecialchars(
                                        date("d M Y", strtotime($eventDate))
                                    );
                                    ?>

                                    <?php if (!empty($startTime)): ?>

                                        ·

                                        <?php
                                        echo htmlspecialchars(
                                            date("h:i A", strtotime($startTime))
                                        );
                                        ?>

                                    <?php endif; ?>

                                </div>

                            <?php endif; ?>


                            <h3>
                                <?php
                                echo htmlspecialchars($eventTitle);
                                ?>
                            </h3>


                            <p>
                                <?php
                                echo htmlspecialchars($description);
                                ?>
                            </p>


                            <a
                                href="event-details.php?schedule_id=<?php echo urlencode($scheduleId); ?>"
                                class="home-event-btn"
                            >
                                View Event
                            </a>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>


        <?php else: ?>

            <div class="home-events-empty">

                <h3>
                    No Upcoming Events
                </h3>

                <p>
                    New events will appear here once they are
                    added to the DaisyDream schedule.
                </p>

            </div>

        <?php endif; ?>

    </div>

</section>


<!-- =====================================================
     CALL TO ACTION
===================================================== -->

<section class="home-cta">

    <div class="home-cta-box">

        <h2>
            Ready to Plan Your Event?
        </h2>

        <p>
            Tell us about your occasion and our event
            management team will help you create a memorable
            experience.
        </p>

        <a
            href="contact.php"
            class="hero-btn hero-btn-primary"
        >
            Contact DaisyDream
        </a>

    </div>

</section>


<?php
require_once "../includes/footer.php";
?>


</body>

</html>