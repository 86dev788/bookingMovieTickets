-- =====================================================
-- ONLINE MOVIE TICKET BOOKING SYSTEM
-- MySQL/XAMPP Compatible Schema
-- =====================================================

CREATE DATABASE cinema_db;

USE cinema_db;

-- =====================================================
-- 1. customer
-- =====================================================

CREATE TABLE customer (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- 2. cinema
-- =====================================================

CREATE TABLE cinema (
    cinema_id INT AUTO_INCREMENT PRIMARY KEY,
    cinema_name VARCHAR(150) NOT NULL,
    city VARCHAR(100) NOT NULL,
    address VARCHAR(300) NOT NULL,
    phone VARCHAR(20)
);

-- =====================================================
-- 3. movie
-- =====================================================

CREATE TABLE movie (
    movie_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    duration_min INT NOT NULL,
    language VARCHAR(50) NOT NULL,
    rating ENUM('U', 'PG', 'PG-13', 'R') NOT NULL,
    genres VARCHAR(300) NOT NULL,
    poster_url VARCHAR(500),
    release_date DATE NOT NULL,
    description VARCHAR(1000),

    CHECK (duration_min > 0)
);

-- =====================================================
-- 4. screen
-- =====================================================

CREATE TABLE screen (
    screen_id INT AUTO_INCREMENT PRIMARY KEY,
    screen_name VARCHAR(100) NOT NULL,
    total_seats INT NOT NULL,
    cinema_id INT NOT NULL,

    CHECK (total_seats > 0),

    FOREIGN KEY (cinema_id)
    REFERENCES cinema(cinema_id)
    ON DELETE CASCADE
);

-- =====================================================
-- 5. seat
-- =====================================================

CREATE TABLE seat (
    seat_id INT AUTO_INCREMENT PRIMARY KEY,
    seat_number VARCHAR(10) NOT NULL,
    seat_type ENUM('Standard', 'VIP', 'Recliner')
    DEFAULT 'Standard',

    screen_id INT NOT NULL,

    FOREIGN KEY (screen_id)
    REFERENCES screen(screen_id)
    ON DELETE CASCADE,

    UNIQUE(screen_id, seat_number)
);

-- =====================================================
-- 6. movie_show
-- "show" is reserved keyword in MySQL
-- =====================================================

CREATE TABLE movie_show (
    show_id INT AUTO_INCREMENT PRIMARY KEY,
    show_time DATETIME NOT NULL,
    price DECIMAL(8,2) NOT NULL,

    movie_id INT NOT NULL,
    screen_id INT NOT NULL,

    CHECK (price > 0),

    FOREIGN KEY (movie_id)
    REFERENCES movie(movie_id)
    ON DELETE CASCADE,

    FOREIGN KEY (screen_id)
    REFERENCES screen(screen_id)
    ON DELETE NO ACTION,

    UNIQUE(screen_id, show_time)
);

-- =====================================================
-- 7. booking
-- =====================================================

CREATE TABLE booking (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,

    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    status ENUM('Pending', 'Confirmed', 'Cancelled')
    DEFAULT 'Pending',

    total_amount DECIMAL(8,2) NOT NULL,

    customer_id INT NOT NULL,
    show_id INT NOT NULL,

    CHECK (total_amount >= 0),

    FOREIGN KEY (customer_id)
    REFERENCES customer(customer_id)
    ON DELETE CASCADE,

    FOREIGN KEY (show_id)
    REFERENCES movie_show(show_id)
    ON DELETE NO ACTION
);

-- =====================================================
-- 8. payment
-- =====================================================

CREATE TABLE payment (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,

    amount DECIMAL(8,2) NOT NULL,

    method ENUM('Cash', 'Card', 'EasyPaisa', 'JazzCash')
    NOT NULL,

    status ENUM('Pending', 'Success', 'Failed', 'Refunded')
    DEFAULT 'Pending',

    paid_at DATETIME,

    booking_id INT NOT NULL UNIQUE,

    CHECK (amount > 0),

    FOREIGN KEY (booking_id)
    REFERENCES booking(booking_id)
    ON DELETE CASCADE
);

-- =====================================================
-- 9. ticket
-- =====================================================

CREATE TABLE ticket (
    ticket_id INT AUTO_INCREMENT PRIMARY KEY,

    qr_code VARCHAR(255) NOT NULL UNIQUE,

    is_used BOOLEAN DEFAULT FALSE,

    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    booking_id INT NOT NULL,
    seat_id INT NOT NULL,

    FOREIGN KEY (booking_id)
    REFERENCES booking(booking_id)
    ON DELETE CASCADE,

    FOREIGN KEY (seat_id)
    REFERENCES seat(seat_id)
    ON DELETE NO ACTION,

    UNIQUE(booking_id, seat_id)
);