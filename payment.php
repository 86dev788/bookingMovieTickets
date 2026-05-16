<?php
session_start();
include('connection.php');

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['booking_id']) || !is_numeric($_GET['booking_id'])) {
    header("Location: movies.php");
    exit();
}

$booking_id = (int)$_GET['booking_id'];
$customer_id = $_SESSION['customer_id'];

// Verify booking belongs to customer
$stmt = $con->prepare("SELECT b.booking_id AS BookingID, b.total_amount AS TotalAmount, b.status AS Status, m.title AS Title, s.show_time AS ShowTime, c.cinema_name AS CinemaName, c.city AS City
                       FROM booking b
                       JOIN movie_show s ON b.show_id = s.show_id
                       JOIN movie m ON s.movie_id = m.movie_id
                       JOIN screen sc ON s.screen_id = sc.screen_id
                       JOIN cinema c ON sc.cinema_id = c.cinema_id
                       WHERE b.booking_id = ? AND b.customer_id = ?");
$stmt->bind_param("ii", $booking_id, $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

if (!$booking || $booking['Status'] !== 'Pending') {
    header("Location: my-bookings.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_POST['method'];

    // Update payment method and status to Success
    $stmt = $con->prepare("UPDATE payment SET method = ?, status = 'Success', paid_at = NOW() WHERE booking_id = ?");
    $stmt->bind_param("si", $method, $booking_id);
    $stmt->execute();
    $stmt->close();

    // Update booking status to Confirmed
    $stmt = $con->prepare("UPDATE booking SET status = 'Confirmed' WHERE booking_id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->close();

    header("Location: booking-confirmation.php?booking_id=$booking_id");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Online Movie Ticket Booking</title>
    <link rel="stylesheet" href="style/styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include('includes/header.php'); ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Payment</h3>
                    </div>
                    <div class="card-body">
                        <h5>Booking Summary</h5>
                        <p><strong>Movie:</strong> <?php echo htmlspecialchars($booking['Title']); ?></p>
                        <p><strong>Show Time:</strong> <?php echo date('d M Y, H:i', strtotime($booking['ShowTime'])); ?></p>
                        <p><strong>Cinema:</strong> <?php echo htmlspecialchars($booking['CinemaName']); ?> (<?php echo htmlspecialchars($booking['City']); ?>)</p>
                        <p><strong>Total Amount:</strong> PKR <?php echo number_format($booking['TotalAmount'], 2); ?></p>

                        <form method="POST">
                            <div class="form-group">
                                <label for="method">Payment Method</label>
                                <select class="form-control" id="method" name="method" required>
                                    <option value="Cash">Cash</option>
                                    <option value="Card">Card</option>
                                    <option value="EasyPaisa">EasyPaisa</option>
                                    <option value="JazzCash">JazzCash</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Pay Now</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>
</body>
</html>