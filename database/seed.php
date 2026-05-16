<?php
/**
 * Database seeder for the Movie Ticket Booking system.
 * Run this script once to populate the current database with dummy data.
 *
 * Usage:
 *   - In browser: http://localhost/MovieBooking/movie_ticket_booking_system_php/database/seed.php
 *   - CLI: php database/seed.php
 */

require_once __DIR__ . '/../connection.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $con->begin_transaction();
    $con->query('SET FOREIGN_KEY_CHECKS = 0');
    $tables = ['ticket', 'payment', 'booking', 'movie_show', 'seat', 'screen', 'movie', 'cinema', 'customer', 'users'];
    foreach ($tables as $table) {
        $con->query("TRUNCATE TABLE $table");
    }
    $con->query('SET FOREIGN_KEY_CHECKS = 1');

    $customers = [
        ['John Doe', 'john@example.com', '03001234567', password_hash('password123', PASSWORD_DEFAULT)],
        ['Jane Smith', 'jane@example.com', '03007654321', password_hash('securepass', PASSWORD_DEFAULT)],
        ['Ayesha Khan', 'ayesha@example.com', '03009876543', password_hash('mypassword', PASSWORD_DEFAULT)],
        ['Customer User', 'customer@gmail.com', '03001001001', password_hash('customer123', PASSWORD_DEFAULT)],
    ];

    $adminUsers = [
        ['admin@booking.com', 'Admin User', 'admin@booking.com', 'admin123'],
    ];

    $cinemas = [
        ['Cineplex Lahore', 'Lahore', '123 Mall Road, Lahore', '042-1234567'],
        ['Cineplex Islamabad', 'Islamabad', '456 F-10 Markaz, Islamabad', '051-7654321'],
    ];

    $movies = [
        ['Captain Marvel', 125, 'English', 'PG-13', 'Action, Adventure, Sci-Fi', 'img/movie-poster-1.jpg', '2019-03-08', 'Carol Danvers becomes one of the universe\'s most powerful heroes.'],
        ['The Lego Movie', 100, 'English', 'U', 'Animation, Action, Adventure', 'img/movie-poster-3.jpg', '2014-02-07', 'An ordinary Lego construction worker discovers a hidden talent.'],
        ['Inception', 148, 'English', 'PG-13', 'Action, Thriller, Sci-Fi', 'img/movie-poster-5.jpg', '2010-07-16', 'A thief who steals corporate secrets through dream-sharing technology.'],
        ['Fast & Furious 9', 145, 'English', 'PG-13', 'Action, Adventure', 'img/movie-poster-6.jpg', '2021-06-25', 'Dom and the family must stop a world-shattering plot led by his brother.'],
    ];

    $screens = [
        ['Screen 1', 24, 1],
        ['Screen 2', 24, 1],
        ['Screen 1', 24, 2],
        ['Screen 2', 24, 2],
    ];

    $seat_map = [];
    foreach ([1, 2, 3, 4] as $screenId) {
        for ($row = 'A'; $row <= 'D'; $row++) {
            for ($col = 1; $col <= 6; $col++) {
                $seat_number = $row . $col;
                $seat_type = in_array($row, ['A', 'B']) && $col > 4 ? 'VIP' : 'Standard';
                $seat_map[] = [$seat_number, $seat_type, $screenId];
            }
        }
    }

    $shows = [
        ['2025-05-15 14:00:00', 1200.00, 1, 1],
        ['2025-05-15 18:30:00', 1100.00, 2, 1],
        ['2025-05-16 16:00:00', 1500.00, 3, 2],
        ['2025-05-16 20:00:00', 1300.00, 4, 3],
    ];

    $customerStmt = $con->prepare("INSERT INTO customer (name, email, phone, password_hash) VALUES (?, ?, ?, ?)");
    foreach ($customers as $customer) {
        $customerStmt->bind_param('ssss', ...$customer);
        $customerStmt->execute();
    }
    $customerStmt->close();

    $adminStmt = $con->prepare("INSERT INTO users (username, name, email, password) VALUES (?, ?, ?, ?)");
    foreach ($adminUsers as $adminUser) {
        $adminStmt->bind_param('ssss', ...$adminUser);
        $adminStmt->execute();
    }
    $adminStmt->close();

    $cinemaStmt = $con->prepare("INSERT INTO cinema (cinema_name, city, address, phone) VALUES (?, ?, ?, ?)");
    foreach ($cinemas as $cinema) {
        $cinemaStmt->bind_param('ssss', ...$cinema);
        $cinemaStmt->execute();
    }
    $cinemaStmt->close();

    $movieStmt = $con->prepare("INSERT INTO movie (title, duration_min, language, rating, genres, poster_url, release_date, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($movies as $movie) {
        $movieStmt->bind_param('sissssss', $movie[0], $movie[1], $movie[2], $movie[3], $movie[4], $movie[5], $movie[6], $movie[7]);
        $movieStmt->execute();
    }
    $movieStmt->close();

    $screenStmt = $con->prepare("INSERT INTO screen (screen_name, total_seats, cinema_id) VALUES (?, ?, ?)");
    foreach ($screens as $screen) {
        $screenStmt->bind_param('sii', $screen[0], $screen[1], $screen[2]);
        $screenStmt->execute();
    }
    $screenStmt->close();

    $seatStmt = $con->prepare("INSERT INTO seat (seat_number, seat_type, screen_id) VALUES (?, ?, ?)");
    foreach ($seat_map as $seat) {
        $seatStmt->bind_param('ssi', $seat[0], $seat[1], $seat[2]);
        $seatStmt->execute();
    }
    $seatStmt->close();

    $showStmt = $con->prepare("INSERT INTO movie_show (show_time, price, movie_id, screen_id) VALUES (?, ?, ?, ?)");
    foreach ($shows as $show) {
        $showStmt->bind_param('sdii', $show[0], $show[1], $show[2], $show[3]);
        $showStmt->execute();
    }
    $showStmt->close();

    $bookingStmt = $con->prepare("INSERT INTO booking (status, total_amount, customer_id, show_id) VALUES (?, ?, ?, ?)");
    $status = 'Confirmed';
    $customerId = 1;
    $showId = 1;
    $totalAmount = 2400.00;
    $bookingStmt->bind_param('sdii', $status, $totalAmount, $customerId, $showId);
    $bookingStmt->execute();
    $bookingId = $con->insert_id;
    $bookingStmt->close();

    $paymentStmt = $con->prepare("INSERT INTO payment (amount, method, status, paid_at, booking_id) VALUES (?, ?, ?, NOW(), ?)");
    $method = 'Card';
    $paymentStatus = 'Success';
    $paymentStmt->bind_param('dssi', $totalAmount, $method, $paymentStatus, $bookingId);
    $paymentStmt->execute();
    $paymentStmt->close();

    $ticketStmt = $con->prepare("INSERT INTO ticket (qr_code, booking_id, seat_id) VALUES (?, ?, ?)");
    $qr1 = 'QR-' . uniqid();
    $seat1 = 1;
    $ticketStmt->bind_param('sii', $qr1, $bookingId, $seat1);
    $ticketStmt->execute();

    $qr2 = 'QR-' . uniqid();
    $seat2 = 2;
    $ticketStmt->bind_param('sii', $qr2, $bookingId, $seat2);
    $ticketStmt->execute();
    $ticketStmt->close();

    $con->commit();
    echo '<h2>Database seeded successfully.</h2>';
    echo '<p>Inserted sample customers, cinemas, movies, screens, seats, shows, one booking, payment, and tickets.</p>';
    echo '<p>Use <strong>admin@booking.com / admin123</strong> for admin access.</p>';
    echo '<p>Use <strong>customer@gmail.com / customer123</strong> to log in as a customer.</p>';
} catch (Exception $e) {
    $con->rollback();
    echo '<h2>Seeder failed:</h2>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    exit;
}
