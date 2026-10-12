<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<header class="site-header">
    <div class="header-inner">

        <!-- LOGO — MOTU TEXT -->
        <a href="home.php" class="brand">
            <span class="brand-main">DaisyDream</span>
            <span class="brand-sub">EVENT MANAGEMENT</span>
        </a>

        <!-- DESKTOP NAV — MOTU TEXT -->
        <nav class="nav-links">
            <a href="home.php" class="<?php echo $currentPage === 'home.php' ? 'active' : ''; ?>">Home</a>
            <a href="about.php" class="<?php echo $currentPage === 'about.php' ? 'active' : ''; ?>">About Us</a>
            <a href="events.php" class="<?php echo $currentPage === 'events.php' ? 'active' : ''; ?>">Events</a>
            <a href="schedule.php" class="<?php echo $currentPage === 'schedule.php' ? 'active' : ''; ?>">Schedule</a>
            <a href="contact.php" class="<?php echo $currentPage === 'contact.php' ? 'active' : ''; ?>">Contact</a>

            <?php if ($isLoggedIn): ?>
                <a href="dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a>
                <a href="profile.php" class="<?php echo $currentPage === 'profile.php' ? 'active' : ''; ?>">Profile</a>
                <a href="../auth/logout.php" class="nav-logout">Logout</a>
            <?php else: ?>
                <a href="../auth/login.php" class="nav-login">Login</a>
                <a href="../auth/register.php" class="nav-register">Register</a>
            <?php endif; ?>
        </nav>

        <!-- MOBILE MENU BUTTON -->
        <button type="button" class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Open menu">
            <span></span><span></span><span></span>
        </button>
    </div>

    <!-- MOBILE NAVIGATION -->
    <nav class="mobile-nav" id="mobileNav">
        <a href="home.php">Home</a>
        <a href="about.php">About Us</a>
        <a href="events.php">Events</a>
        <a href="schedule.php">Schedule</a>
        <a href="contact.php">Contact</a>
        <?php if ($isLoggedIn): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="profile.php">Profile</a>
            <a href="../auth/logout.php" class="mobile-logout">Logout</a>
        <?php else: ?>
            <a href="../auth/login.php">Login</a>
            <a href="../auth/register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>

<style>
/* =========================================================
   FULL SCREEN HEADER — MOTU TEXT
========================================================= */

.site-header {
    width: 100%;
    background: #FFFFFF;
    border-bottom: 1px solid #E7E2DE;
    position: sticky;
    top: 0;
    z-index: 1000;
}

.header-inner {
    width: 100%;
    max-width: 100%;
    margin: 0 auto;
    padding: 0 80px;
    box-sizing: border-box;
    min-height: 110px;        /* ⭐ MOTU HEIGHT */
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 40px;
}

/* ⭐ BRAND — MOTU TEXT */
.brand {
    display: flex;
    flex-direction: column;
    text-decoration: none;
    flex-shrink: 0;
}

.brand-main {
    color: #292929;
    font-size: 44px;          /* ⭐ MOTU TEXT */
    font-weight: 700;
    letter-spacing: -1px;
    line-height: 1;
    font-family: 'Playfair Display', Georgia, serif;
}

.brand-sub {
    margin-top: 10px;
    color: #A8785C;
    font-size: 11px;          /* ⭐ MOTU */
    font-weight: 700;
    letter-spacing: 3.5px;
    text-transform: uppercase;
}

/* ⭐ NAV — MOTU TEXT */
.nav-links {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
}

.nav-links a {
    position: relative;
    display: inline-flex;
    align-items: center;
    min-height: 48px;         /* ⭐ MOTU */
    padding: 0 20px;          /* ⭐ MOTU PADDING */
    color: #555555;
    text-decoration: none;
    font-size: 16px;          /* ⭐ MOTU TEXT */
    font-weight: 500;
    white-space: nowrap;
    transition: color 0.2s ease;
}

.nav-links a:hover { color: #A8785C; }
.nav-links a.active { color: #A8785C; font-weight: 600; }

.nav-links a.active::after {
    content: "";
    position: absolute;
    left: 20px;
    right: 20px;
    bottom: 0;
    height: 3px;
    background: #A8785C;
    border-radius: 3px;
}

/* ⭐ LOGIN / REGISTER — MOTU TEXT */
.nav-login {
    margin-left: 14px;
    border: 1px solid #D8D1CC;
    border-radius: 8px;
    color: #4A4A4A !important;
    padding: 0 26px !important;   /* ⭐ MOTU */
    min-height: 48px;
    font-size: 15px !important;   /* ⭐ MOTU */
    font-weight: 600;
}

.nav-login:hover { border-color: #A8785C; color: #A8785C !important; }

.nav-register {
    border: 1px solid #A8785C;
    border-radius: 8px;
    background: #A8785C;
    color: #FFFFFF !important;
    padding: 0 30px !important;   /* ⭐ MOTU */
    min-height: 48px;
    font-size: 15px !important;   /* ⭐ MOTU */
    font-weight: 600;
}

.nav-register:hover { background: #93694F; border-color: #93694F; }

.nav-logout {
    margin-left: 10px;
    border: 1px solid #D8D1CC;
    border-radius: 8px;
    color: #555555 !important;
    padding: 0 26px !important;   /* ⭐ MOTU */
    min-height: 48px;
    font-size: 15px !important;   /* ⭐ MOTU */
    font-weight: 600;
}

.nav-logout:hover { border-color: #A8785C; color: #A8785C !important; }

/* MOBILE MENU BUTTON */
.mobile-menu-btn {
    display: none;
    width: 52px;              /* ⭐ MOTU */
    height: 48px;             /* ⭐ MOTU */
    padding: 12px;
    border: 1px solid #DCD6D1;
    border-radius: 8px;
    background: #FFFFFF;
    cursor: pointer;
}

.mobile-menu-btn span {
    display: block;
    width: 24px;
    height: 2px;
    margin: 5px auto;
    background: #444444;
    border-radius: 2px;
}

/* MOBILE NAV */
.mobile-nav {
    display: none;
    padding: 14px 25px 22px;
    border-top: 1px solid #EEEEEE;
}

.mobile-nav a {
    display: block;
    padding: 15px 4px;        /* ⭐ MOTU */
    color: #4A4A4A;
    text-decoration: none;
    font-size: 16px;          /* ⭐ MOTU */
    border-bottom: 1px solid #F0F0F0;
}

.mobile-nav a:hover { color: #A8785C; }
.mobile-nav .mobile-logout { color: #93694F; font-weight: 600; }

/* ========== RESPONSIVE ========== */

@media (max-width: 1400px) {
    .header-inner { padding: 0 60px; }
}

@media (max-width: 1200px) {
    .header-inner { padding: 0 40px; gap: 25px; }
    .brand-main { font-size: 38px; }
    .brand-sub { font-size: 10px; letter-spacing: 3px; }
    .nav-links a { padding: 0 14px; font-size: 14.5px; }
    .nav-links a.active::after { left: 14px; right: 14px; }
}

@media (max-width: 1050px) {
    .nav-links a { padding: 0 12px; font-size: 14px; }
    .nav-links a.active::after { left: 12px; right: 12px; }
}

@media (max-width: 900px) {
    .header-inner { min-height: 85px; padding: 0 25px; }
    .brand-main { font-size: 32px; }
    .brand-sub { font-size: 9px; letter-spacing: 2.5px; }
    .nav-links { display: none; }
    .mobile-menu-btn { display: block; }
    .mobile-nav.show { display: block; }
}

@media (max-width: 480px) {
    .header-inner { padding: 0 18px; min-height: 75px; }
    .brand-main { font-size: 26px; }
    .brand-sub { font-size: 8px; letter-spacing: 2px; }
    .mobile-menu-btn { width: 46px; height: 44px; }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const menuButton = document.getElementById("mobileMenuBtn");
    const mobileNav = document.getElementById("mobileNav");
    if (menuButton && mobileNav) {
        menuButton.addEventListener("click", function () {
            mobileNav.classList.toggle("show");
        });
    }
});
</script>