-- ============================================
-- DAISYDREAM EVENT MANAGEMENT SYSTEM
-- Database: daisydream
-- ============================================

CREATE DATABASE IF NOT EXISTS daisydream;

USE daisydream;


-- ============================================
-- 1. ADMINS TABLE
-- ============================================

CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- ============================================
-- 2. USERS TABLE
-- ============================================

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    address VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- ============================================
-- 3. CATEGORIES TABLE
-- ============================================

CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- ============================================
-- 4. SCHEDULES TABLE
-- ============================================

CREATE TABLE schedules (
    schedule_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    event_title VARCHAR(150) NOT NULL,
    event_description TEXT,
    event_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (category_id)
        REFERENCES categories(category_id)
        ON DELETE CASCADE
);


-- ============================================
-- 5. BOOKINGS TABLE
-- ============================================

CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,
    schedule_id INT NOT NULL,

    customer_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    address VARCHAR(255),

    event_date DATE NOT NULL,
    payment_method VARCHAR(50),

    status ENUM('Pending', 'Accepted', 'Rejected')
        DEFAULT 'Pending',

    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE,

    FOREIGN KEY (schedule_id)
        REFERENCES schedules(schedule_id)
        ON DELETE CASCADE
);


-- ============================================
-- 6. CONTACT MESSAGES TABLE
-- ============================================

CREATE TABLE contact_messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    address VARCHAR(255),
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- ============================================
-- 7. SITE SETTINGS TABLE
-- ============================================

CREATE TABLE site_settings (
    setting_id INT AUTO_INCREMENT PRIMARY KEY,
    website_name VARCHAR(150),
    contact_number VARCHAR(20),
    email VARCHAR(100),
    office_address VARCHAR(255),
    logo VARCHAR(255)
);


-- ============================================
-- SAMPLE ADMIN
-- ============================================

INSERT INTO admins
(name, email, password)
VALUES
(
    'DaisyDream Admin',
    'admin@daisydream.com',
    'admin123'
);


-- ============================================
-- SAMPLE CATEGORIES
-- ============================================

INSERT INTO categories
(category_name, description, image)
VALUES
(
    'Wedding',
    'Wedding and marriage events',
    'wedding.jpg'
),
(
    'Birthday',
    'Birthday celebration events',
    'birthday.jpg'
),
(
    'Corporate',
    'Corporate and business events',
    'corporate.jpg'
),
(
    'Engagement',
    'Engagement ceremonies',
    'engagement.jpg'
),
(
    'Anniversary',
    'Anniversary celebrations',
    'anniversary.jpg'
);


-- ============================================
-- SAMPLE EVENT SCHEDULES
-- ============================================

INSERT INTO schedules
(
    category_id,
    event_title,
    event_description,
    event_date,
    start_time,
    end_time,
    price,
    image
)
VALUES
(
    1,
    'Royal Wedding',
    'Complete wedding event management',
    '2026-10-15',
    '10:00:00',
    '22:00:00',
    50000.00,
    'royal-wedding.jpg'
),
(
    2,
    'Birthday Celebration',
    'Professional birthday event arrangement',
    '2026-10-20',
    '18:00:00',
    '22:00:00',
    15000.00,
    'birthday-party.jpg'
),
(
    3,
    'Corporate Meetup',
    'Corporate meeting and event management',
    '2026-11-05',
    '09:00:00',
    '17:00:00',
    30000.00,
    'corporate-event.jpg'
);


-- ============================================
-- SAMPLE SITE SETTINGS
-- ============================================

INSERT INTO site_settings
(
    website_name,
    contact_number,
    email,
    office_address,
    logo
)
VALUES
(
    'DaisyDream Event Management',
    '9876543210',
    'info@daisydream.com',
    'Rajkot, Gujarat, India',
    'logo.png'
);


-- ============================================
-- DATABASE COMPLETED
-- ============================================