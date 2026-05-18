<?php
session_start();
include('connection.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

$stmt = $con->prepare("
SELECT 
    b.booking_id AS BookingID,
    b.booking_date AS BookingDate,
    b.status AS Status,
    b.total_amount AS TotalAmount,

    m.title AS Title,

    s.show_time AS ShowTime,

    c.cinema_name AS CinemaName,
    c.city AS City,

    p.method AS PaymentMethod,
    p.status AS PaymentStatus,
    p.paid_at AS PaidAt,

    t.qr_code AS QRCode

FROM booking b

JOIN movie_show s 
ON b.show_id = s.show_id

JOIN movie m 
ON s.movie_id = m.movie_id

JOIN screen sc 
ON s.screen_id = sc.screen_id

JOIN cinema c 
ON sc.cinema_id = c.cinema_id

LEFT JOIN payment p 
ON p.booking_id = b.booking_id

LEFT JOIN ticket t
ON t.booking_id = b.booking_id

WHERE b.customer_id = ?
AND b.status NOT IN ('Cancelled', 'Refunded')

ORDER BY b.booking_date DESC
");

$stmt->bind_param("i", $customer_id);
$stmt->execute();

$result = $stmt->get_result();

$bookings = [];

while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

$stmt->close();


// Split into Upcoming & Past
$upcoming = [];
$past = [];

foreach ($bookings as $b) {

    if (strtotime($b['ShowTime']) >= strtotime(date('Y-m-d 00:00:00'))) {
        $upcoming[] = $b;
    } else {
        $past[] = $b;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Bookings - Online Movie Ticket Booking</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="style/styles.css">

</head>

<body>

    <?php include('includes/header.php'); ?>

    <div class="container mt-5">

        <h1 class="mb-4">My Bookings</h1>

        <?php if (empty($bookings)): ?>

            <div class="alert alert-warning">
                You have no bookings yet.
            </div>

        <?php else: ?>

            <!-- Tabs -->
            <ul class="nav nav-tabs" id="bookingTabs" role="tablist">

                <li class="nav-item">
                    <a class="nav-link active" id="upcoming-tab" data-toggle="tab" href="#upcoming" role="tab">

                        Upcoming (<?php echo count($upcoming); ?>)

                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="past-tab" data-toggle="tab" href="#past" role="tab">

                        Past (<?php echo count($past); ?>)

                    </a>
                </li>

            </ul>

            <div class="tab-content mt-4">

                <!-- UPCOMING BOOKINGS -->
                <div class="tab-pane fade show active" id="upcoming" role="tabpanel">

                    <div class="row">

                        <?php foreach ($upcoming as $booking): ?>

                            <div class="col-md-6 mb-4">

                                <div class="card shadow">

                                    <div class="card-body">

                                        <h4 class="card-title">
                                            <?php echo htmlspecialchars($booking['Title']); ?>
                                        </h4>

                                        <p class="card-text">

                                            <strong>Booking ID:</strong>
                                            <?php echo $booking['BookingID']; ?>
                                            <br>

                                            <strong>Date:</strong>
                                            <?php echo date('d M Y', strtotime($booking['BookingDate'])); ?>
                                            <br>

                                            <strong>Show Time:</strong>
                                            <?php echo date('d M Y, H:i', strtotime($booking['ShowTime'])); ?>
                                            <br>

                                            <strong>Cinema:</strong>
                                            <?php echo htmlspecialchars($booking['CinemaName']); ?>
                                            (<?php echo htmlspecialchars($booking['City']); ?>)
                                            <br>

                                            <strong>Amount:</strong>
                                            PKR <?php echo number_format($booking['TotalAmount'], 2); ?>
                                            <br>

                                            <strong>Booking Status:</strong>

                                            <span class="badge badge-<?php
                                            echo $booking['Status'] == 'Confirmed'
                                                ? 'success'
                                                : ($booking['Status'] == 'Pending'
                                                    ? 'warning'
                                                    : 'danger');
                                            ?>">

                                                <?php echo $booking['Status']; ?>

                                            </span>

                                            <br>

                                            <strong>Payment:</strong>

                                            <?php echo htmlspecialchars($booking['PaymentMethod'] ?? 'N/A'); ?>

                                            —

                                            <span class="badge badge-<?php
                                            echo ($booking['PaymentStatus'] == 'Success'
                                                ? 'success'
                                                : ($booking['PaymentStatus'] == 'Pending'
                                                    ? 'warning'
                                                    : 'danger'));
                                            ?>">

                                                <?php echo htmlspecialchars($booking['PaymentStatus'] ?? 'Pending'); ?>

                                            </span>

                                            <?php if (!empty($booking['PaidAt'])): ?>

                                                <br>

                                                <strong>Paid At:</strong>

                                                <?php echo date('d M Y, H:i', strtotime($booking['PaidAt'])); ?>

                                            <?php endif; ?>

                                        </p>

                                        <!-- BUTTONS -->
                                        <?php if ($booking['Status'] == 'Confirmed'): ?>

                                            <a href="booking-confirmation.php?booking_id=<?php echo $booking['BookingID']; ?>"
                                                class="btn btn-primary">

                                                View Tickets

                                            </a>

                                        <?php endif; ?>

                                        <?php if ($booking['Status'] == 'Confirmed' && strtotime($booking['ShowTime']) > time()): ?>

                                            <a href="cancel-booking.php?booking_id=<?php echo $booking['BookingID']; ?>"
                                                class="btn btn-danger ml-2"
                                                onclick="return confirm('Are you sure you want to cancel this booking?')">

                                                Cancel Booking

                                            </a>

                                        <?php endif; ?>

                                        <!-- QR CODE -->
                                        <?php if (!empty($booking['QRCode'])): ?>

                                            <?php
                                            $qrData = $booking['QRCode']; // TK19_27_6a0b730726a2c
                                            ?>

                                            <div class="text-center mt-4">

                                               <h4>QR Code : <?php echo htmlspecialchars($qrData); ?></h4>

                                            </div>

                                        <?php endif; ?>

                                    </div>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>

                <!-- PAST BOOKINGS -->
                <div class="tab-pane fade" id="past" role="tabpanel">

                    <div class="row">

                        <?php foreach ($past as $booking): ?>

                            <div class="col-md-6 mb-4">

                                <div class="card shadow">

                                    <div class="card-body">

                                        <h4 class="card-title">
                                            <?php echo htmlspecialchars($booking['Title']); ?>
                                        </h4>

                                        <p class="card-text">

                                            <strong>Booking ID:</strong>
                                            <?php echo $booking['BookingID']; ?>
                                            <br>

                                            <strong>Date:</strong>
                                            <?php echo date('d M Y', strtotime($booking['BookingDate'])); ?>
                                            <br>

                                            <strong>Show Time:</strong>
                                            <?php echo date('d M Y, H:i', strtotime($booking['ShowTime'])); ?>
                                            <br>

                                            <strong>Cinema:</strong>
                                            <?php echo htmlspecialchars($booking['CinemaName']); ?>
                                            (<?php echo htmlspecialchars($booking['City']); ?>)
                                            <br>

                                            <strong>Amount:</strong>
                                            PKR <?php echo number_format($booking['TotalAmount'], 2); ?>
                                            <br>

                                            <strong>Status:</strong>

                                            <span class="badge badge-<?php
                                            echo $booking['Status'] == 'Confirmed'
                                                ? 'success'
                                                : ($booking['Status'] == 'Pending'
                                                    ? 'warning'
                                                    : 'danger');
                                            ?>">

                                                <?php echo $booking['Status']; ?>

                                            </span>

                                        </p>

                                        <?php if ($booking['Status'] == 'Confirmed'): ?>

                                            <a href="booking-confirmation.php?booking_id=<?php echo $booking['BookingID']; ?>"
                                                class="btn btn-primary">

                                                View Tickets

                                            </a>

                                        <?php endif; ?>

                                         <!-- QR CODE -->
                                        <?php if (!empty($booking['QRCode'])): ?>

                                            <?php
                                            $qrData = $booking['QRCode']; // TK19_27_6a0b730726a2c
                                            ?>

                                            <div class="text-center mt-4">

                                               <h4>QR Code : <?php echo htmlspecialchars($qrData); ?></h4>

                                            </div>

                                        <?php endif; ?>

                                    </div>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>

            </div>

        <?php endif; ?>

    </div>

    <?php include('includes/footer.php'); ?>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>