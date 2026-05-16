<?php
session_start();
include('connection.php');

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['booking_id']) || !is_numeric($_GET['booking_id'])) {
    header("Location: my-bookings.php");
    exit();
}

$booking_id = (int)$_GET['booking_id'];
$customer_id = $_SESSION['customer_id'];

// Get booking details
$stmt = $con->prepare("SELECT b.booking_id AS BookingID, b.total_amount AS TotalAmount, b.status AS Status, m.title AS Title, s.show_time AS ShowTime, c.cinema_name AS CinemaName, c.city AS City, sc.screen_name AS ScreenName
                       FROM booking b
                       JOIN movie_show s ON b.show_id = s.show_id
                       JOIN movie m ON s.movie_id = m.movie_id
                       JOIN screen sc ON s.screen_id = sc.screen_id
                       JOIN cinema c ON sc.cinema_id = c.cinema_id
                       WHERE b.booking_id = ? AND b.customer_id = ? AND b.status = 'Confirmed'");
$stmt->bind_param("ii", $booking_id, $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

if (!$booking) {
    header("Location: my-bookings.php");
    exit();
}

// Get tickets
$stmt = $con->prepare("SELECT t.qr_code AS QRCode, s.seat_number AS SeatNumber, s.seat_type AS SeatType
                       FROM ticket t
                       JOIN seat s ON t.seat_id = s.seat_id
                       WHERE t.booking_id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$tickets_result = $stmt->get_result();
$tickets = [];
while ($row = $tickets_result->fetch_assoc()) {
    $tickets[] = $row;
}
$stmt->close();

// Get payment details
$stmt = $con->prepare("SELECT method, status, paid_at FROM payment WHERE booking_id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$payment_result = $stmt->get_result();
$payment = $payment_result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - Online Movie Ticket Booking</title>
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
    <?php include('includes/header.php'); ?>

    <div class="container mt-5">
        <div class="alert alert-success">
            <h4>Booking Confirmed!</h4>
            <p>Booking ID: <?php echo $booking['BookingID']; ?></p>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Booking Details</h5>
            </div>
            <div class="card-body">
                <p><strong>Movie:</strong> <?php echo htmlspecialchars($booking['Title']); ?></p>
                <p><strong>Show Time:</strong> <?php echo date('d M Y, H:i', strtotime($booking['ShowTime'])); ?></p>
                <p><strong>Cinema:</strong> <?php echo htmlspecialchars($booking['CinemaName']); ?> (<?php echo htmlspecialchars($booking['City']); ?>)</p>
                <p><strong>Screen:</strong> <?php echo htmlspecialchars($booking['ScreenName']); ?></p>
                <p><strong>Total Amount:</strong> PKR <?php echo number_format($booking['TotalAmount'], 2); ?></p>
                <?php if (!empty($payment)): ?>
                    <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($payment['method']); ?></p>
                    <p><strong>Payment Status:</strong> <?php echo htmlspecialchars($payment['status']); ?><?php if (!empty($payment['paid_at'])): ?> (Paid at <?php echo date('d M Y, H:i', strtotime($payment['paid_at'])); ?>)<?php endif; ?></p>
                <?php endif; ?>
            </div>
        </div>

        <h3 class="mt-4">Your Tickets</h3>
        <div class="row">
            <?php foreach ($tickets as $ticket): ?>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5><?php echo htmlspecialchars($booking['Title']); ?></h5>
                                            <p>Seat: <?php echo htmlspecialchars($ticket['SeatNumber']); ?> (<?php echo htmlspecialchars($ticket['SeatType']); ?>)</p>
                            <p>Screen: <?php echo htmlspecialchars($booking['ScreenName']); ?></p>
                            <p>Time: <?php echo date('d M Y, H:i', strtotime($booking['ShowTime'])); ?></p>
                                            <?php if (!empty($ticket['QRCode']) && !empty($ticket['SeatNumber'])): ?>
                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo urlencode($ticket['QRCode']); ?>" alt="QR Code">
                                                <p><small>QR Code: <?php echo htmlspecialchars($ticket['QRCode']); ?></small></p>
                                            <?php else: ?>
                                                <p><em>Ticket pending finalization.</em></p>
                                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-4">
            <a href="my-bookings.php" class="btn btn-primary">View My Bookings</a>
            <button onclick="window.print()" class="btn btn-secondary">Print Tickets</button>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>
</body>
</html>