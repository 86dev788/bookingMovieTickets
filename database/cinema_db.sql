-- =====================================================
-- ONLINE MOVIE TICKET BOOKING SYSTEM
-- Updated Schema for COMSATS Lahore CSC 270
-- MySQL Version | 9 Tables | 3NF Compliant
-- =====================================================

-- Drop existing tables if they exist
DROP TABLE IF EXISTS ticket;
DROP TABLE IF EXISTS payment;
DROP TABLE IF EXISTS booking;
DROP TABLE IF EXISTS movie_show;
DROP TABLE IF EXISTS seat;
DROP TABLE IF EXISTS screen;
DROP TABLE IF EXISTS movie;
DROP TABLE IF EXISTS cinema;
DROP TABLE IF EXISTS customer;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS feedbacktable;
DROP TABLE IF EXISTS bookingtable;
DROP TABLE IF EXISTS movietable;

-- =====================================================
-- PART 1: FOUNDATION TABLES (no foreign keys)
-- =====================================================

-- 1. Customer
CREATE TABLE customer (
    customer_id      INT             PRIMARY KEY AUTO_INCREMENT,
    name             VARCHAR(100)    NOT NULL,
    email            VARCHAR(150)    NOT NULL UNIQUE,
    phone            VARCHAR(20)     NOT NULL,
    password_hash    VARCHAR(255)    NOT NULL,
    created_at       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 2. Cinema
-- City is absorbed as a VARCHAR column (removes City table, stays 3NF)
CREATE TABLE cinema (
    cinema_id        INT             PRIMARY KEY AUTO_INCREMENT,
    cinema_name      VARCHAR(150)    NOT NULL,
    city             VARCHAR(100)    NOT NULL,
    address          VARCHAR(300)    NOT NULL,
    phone            VARCHAR(20)     NULL
);

-- 3. Movie
-- Genres stored as comma-separated VARCHAR (removes Genre+MovieGenre tables)
-- e.g. 'Action, Sci-Fi' | Still 3NF as Genres is a single multi-value attribute
CREATE TABLE movie (
    movie_id         INT             PRIMARY KEY AUTO_INCREMENT,
    title            VARCHAR(200)    NOT NULL,
    duration_min     INT             NOT NULL,
    language         VARCHAR(50)     NOT NULL,
    rating           VARCHAR(10)     NOT NULL,
    genres           VARCHAR(300)    NOT NULL,
    poster_url       VARCHAR(500)    NULL,
    release_date     DATE            NOT NULL,
    description      VARCHAR(1000)   NULL
);

-- =====================================================
-- PART 2: SCREEN AND SEAT (depend on Cinema)
-- =====================================================

-- 4. Screen
CREATE TABLE screen (
    screen_id        INT             PRIMARY KEY AUTO_INCREMENT,
    screen_name      VARCHAR(100)    NOT NULL,
    total_seats      INT             NOT NULL,
    cinema_id        INT             NOT NULL,
    FOREIGN KEY (cinema_id) REFERENCES cinema(cinema_id) ON DELETE CASCADE
);

-- 5. Seat
-- SeatType: 'Standard', 'VIP', 'Recliner'
-- SeatNumber: 'A1', 'B2', etc. — unique per screen
CREATE TABLE seat (
    seat_id          INT             PRIMARY KEY AUTO_INCREMENT,
    seat_number      VARCHAR(10)     NOT NULL,
    seat_type        VARCHAR(30)     NOT NULL DEFAULT 'Standard',
    screen_id        INT             NOT NULL,
    FOREIGN KEY (screen_id) REFERENCES screen(screen_id) ON DELETE CASCADE,
    UNIQUE (screen_id, seat_number)   -- no duplicate seat numbers in same screen
);

-- =====================================================
-- PART 3: SHOW (depends on Movie + Screen)
-- =====================================================

-- 6. Show
-- One movie on one screen at a specific time
-- UNIQUE on (screen_id, show_time) prevents double-booking a screen
CREATE TABLE movie_show (
    show_id          INT             PRIMARY KEY AUTO_INCREMENT,
    show_time        DATETIME        NOT NULL,
    price            DECIMAL(8,2)    NOT NULL,
    movie_id         INT             NOT NULL,
    screen_id        INT             NOT NULL,
    FOREIGN KEY (movie_id)  REFERENCES movie(movie_id)   ON DELETE CASCADE,
    FOREIGN KEY (screen_id) REFERENCES screen(screen_id) ON DELETE NO ACTION,
    UNIQUE (screen_id, show_time)     -- prevents two shows on same screen at same time
);

-- =====================================================
-- PART 4: BOOKING (depends on Customer + Show)
-- =====================================================

-- 7. Booking
-- Status: 'Pending', 'Confirmed', 'Cancelled'
CREATE TABLE booking (
    booking_id       INT             PRIMARY KEY AUTO_INCREMENT,
    booking_date     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status           VARCHAR(20)     NOT NULL DEFAULT 'Pending',
    total_amount     DECIMAL(8,2)    NOT NULL,
    customer_id      INT             NOT NULL,
    show_id          INT             NOT NULL,
    FOREIGN KEY (customer_id) REFERENCES customer(customer_id) ON DELETE CASCADE,
    FOREIGN KEY (show_id)     REFERENCES movie_show(show_id) ON DELETE NO ACTION
);

-- =====================================================
-- PART 5: PAYMENT AND TICKET (depend on Booking + Seat)
-- =====================================================

-- 8. Payment
-- UNIQUE on booking_id enforces the 1:1 relationship with booking
-- Method: 'Cash', 'Card', 'EasyPaisa', 'JazzCash'
-- Status: 'Pending', 'Success', 'Failed', 'Refunded'
CREATE TABLE payment (
    payment_id       INT             PRIMARY KEY AUTO_INCREMENT,
    amount           DECIMAL(8,2)    NOT NULL,
    method           VARCHAR(50)     NOT NULL,
    status           VARCHAR(20)     NOT NULL DEFAULT 'Pending',
    paid_at          DATETIME        NULL,
    booking_id       INT             NOT NULL UNIQUE,
    FOREIGN KEY (booking_id) REFERENCES booking(booking_id) ON DELETE CASCADE
);

-- 9. Ticket
-- One ticket per seat per booking
-- QRCode is unique system-wide for gate scanning
-- seat_id + booking_id together replace the old BookingSeat junction table
-- UNIQUE (booking_id, seat_id) prevents same seat being ticketed twice under same booking
CREATE TABLE ticket (
    ticket_id        INT             PRIMARY KEY AUTO_INCREMENT,
    qr_code          VARCHAR(255)    NOT NULL UNIQUE,
    is_used          TINYINT(1)      NOT NULL DEFAULT 0,
    generated_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    booking_id       INT             NOT NULL,
    seat_id          INT             NOT NULL,
    FOREIGN KEY (booking_id) REFERENCES booking(booking_id) ON DELETE CASCADE,
    FOREIGN KEY (seat_id)    REFERENCES seat(seat_id)       ON DELETE NO ACTION,
    UNIQUE (booking_id, seat_id)      -- one ticket per seat per booking
);

-- =====================================================
-- LEGACY ADMIN TABLES
-- =====================================================

CREATE TABLE bookingtable (
    bookingID        INT(11)        NOT NULL AUTO_INCREMENT,
    movieID          INT(11)        DEFAULT NULL,
    bookingTheatre   VARCHAR(255)   DEFAULT NULL,
    bookingType      VARCHAR(100)   DEFAULT NULL,
    bookingDate      DATE           DEFAULT NULL,
    bookingTime      VARCHAR(50)    DEFAULT NULL,
    bookingFName     VARCHAR(100)   DEFAULT NULL,
    bookingLName     VARCHAR(100)   DEFAULT NULL,
    bookingPNumber   VARCHAR(50)    DEFAULT NULL,
    bookingEmail     VARCHAR(150)   DEFAULT NULL,
    amount           VARCHAR(100)   DEFAULT NULL,
    `ORDERID`        VARCHAR(100)   DEFAULT NULL,
    PRIMARY KEY (bookingID),
    KEY foreign_key_movieID (movieID)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE feedbacktable (
    msgID            INT(12)        NOT NULL AUTO_INCREMENT,
    senderfName      VARCHAR(100)   DEFAULT NULL,
    senderlName      VARCHAR(100)   DEFAULT NULL,
    sendereMail      VARCHAR(150)   DEFAULT NULL,
    senderfeedback   TEXT           DEFAULT NULL,
    PRIMARY KEY (msgID)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE movietable (
    movieID          INT(11)        NOT NULL AUTO_INCREMENT,
    movieTitle       VARCHAR(255)   DEFAULT NULL,
    movieGenre       VARCHAR(255)   DEFAULT NULL,
    DurationMin      INT(11)        DEFAULT NULL,
    Language         VARCHAR(50)    DEFAULT NULL,
    Rating           VARCHAR(20)    DEFAULT NULL,
    movieRelDate     DATE           DEFAULT NULL,
    movieDirector    VARCHAR(255)   DEFAULT NULL,
    PosterURL        VARCHAR(500)   DEFAULT NULL,
    movieDescription TEXT           DEFAULT NULL,
    PRIMARY KEY (movieID)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- =====================================================
-- SAMPLE DATA INSERTION
-- =====================================================

-- Insert sample customers
INSERT INTO customer (name, email, phone, password_hash) VALUES
('John Doe', 'john@example.com', '1234567890', '$2y$10$hO69iqeleADiK5sJ1/frWuBnMCrvCMJx9Dh.enEc8KcsAHthz7oqK'),
('Jane Smith', 'jane@example.com', '0987654321', '$2y$10$awSMpulO94wGiGHHK/ywNeQo0dsELqoMHUCg6M0KmzVFLaLGuwK6O'),
('Customer User', 'customer@gmail.com', '03001001001', '$2y$10$aFDB9B0CjROVm70TqbBG0OqJZ5zueD3HzGwQy.dYBr4UojoCO6AKG');

-- Insert sample cinemas
INSERT INTO cinema (cinema_name, city, address, phone) VALUES
('Cineplex Lahore', 'Lahore', '123 Mall Road, Lahore', '042-1234567'),
('Cineplex Islamabad', 'Islamabad', '456 F-10, Islamabad', '051-7654321');

-- Insert sample movies
INSERT INTO movie (title, duration_min, language, rating, genres, poster_url, release_date, description) VALUES
('Captain Marvel', 220, 'English', 'PG-13', 'Action, Adventure, Sci-Fi', 'img/movie-poster-1.jpg', '2019-03-08', 'Carol Danvers becomes one of the universe\'s most powerful heroes.'),
('The Lego Movie', 110, 'English', 'U', 'Animation, Action, Adventure', 'img/movie-poster-3.jpg', '2014-02-07', 'An ordinary Lego construction worker discovers a hidden talent.');

-- Insert sample screens
INSERT INTO screen (screen_name, total_seats, cinema_id) VALUES
('Screen 1', 100, 1),
('Screen 2', 80, 1),
('Screen 1', 120, 2);

-- Insert sample seats for Screen 1 (Cinema 1)
INSERT INTO seat (seat_number, seat_type, screen_id) VALUES
('A1', 'Standard', 1), ('A2', 'Standard', 1), ('A3', 'VIP', 1), ('A4', 'VIP', 1),
('B1', 'Standard', 1), ('B2', 'Standard', 1), ('B3', 'VIP', 1), ('B4', 'VIP', 1);

-- Insert sample shows
INSERT INTO movie_show (show_time, price, movie_id, screen_id) VALUES
('2025-05-15 14:00:00', 1500.00, 1, 1),
('2025-05-15 18:00:00', 1200.00, 2, 1);

-- Insert sample booking
INSERT INTO booking (status, total_amount, customer_id, show_id) VALUES
('Confirmed', 3000.00, 1, 1);

-- Insert sample payment
INSERT INTO payment (amount, method, status, paid_at, booking_id) VALUES
(3000.00, 'Card', 'Success', '2025-05-14 10:00:00', 1);

-- Insert sample tickets
INSERT INTO ticket (qr_code, booking_id, seat_id) VALUES
('QR123456789', 1, 1),
('QR987654321', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(80) NOT NULL,
  `name` varchar(80) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password` varchar(80) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `email`, `password`) VALUES
(1, 'admin@booking.com', 'Admin User', 'admin@booking.com', 'admin123'),
(2, 'customer@gmail.com', 'Customer User', 'customer@gmail.com', 'customer123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookingtable`
--
ALTER TABLE `bookingtable`
  ADD PRIMARY KEY (`bookingID`),
  ADD UNIQUE KEY `bookingID` (`bookingID`),
  ADD KEY `foreign_key_movieID` (`movieID`),
  ADD KEY `foreign_key_ORDERID` (`ORDERID`);

--
-- Indexes for table `feedbacktable`
--
ALTER TABLE `feedbacktable`
  ADD PRIMARY KEY (`msgID`),
  ADD UNIQUE KEY `msgID` (`msgID`);

--
-- Indexes for table `movietable`
--
ALTER TABLE `movietable`
  ADD PRIMARY KEY (`movieID`),
  ADD UNIQUE KEY `movieID` (`movieID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookingtable`
--
ALTER TABLE `bookingtable`
  MODIFY `bookingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `feedbacktable`
--
ALTER TABLE `feedbacktable`
  MODIFY `msgID` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `movietable`
--
ALTER TABLE `movietable`
  MODIFY `movieID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookingtable`
--
ALTER TABLE `bookingtable`
  ADD CONSTRAINT `foreign_key_movieID` FOREIGN KEY (`movieID`) REFERENCES `movietable` (`movieID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
