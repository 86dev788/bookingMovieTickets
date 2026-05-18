<?php
include "config.php";

// Check user login or not
if (!isset($_SESSION['uname'])) {
    header('Location: index.php');
    exit;
}

// logout
if (isset($_POST['but_logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

$query = "SELECT 
    b.booking_id,
    b.booking_date,
    b.status,
    b.total_amount,
    m.title AS movie_title,
    s.show_time,
    cust.name AS customer_name,
    cust.email AS customer_email,
    cust.phone AS customer_phone,
    p.status AS payment_status,
    p.method AS payment_method,
    GROUP_CONCAT(se.seat_number SEPARATOR ', ') AS seats

FROM booking b

JOIN customer cust ON b.customer_id = cust.customer_id
JOIN movie_show s ON b.show_id = s.show_id
JOIN movie m ON s.movie_id = m.movie_id
LEFT JOIN payment p ON p.booking_id = b.booking_id

LEFT JOIN ticket t ON t.booking_id = b.booking_id
LEFT JOIN seat se ON se.seat_id = t.seat_id

GROUP BY b.booking_id
ORDER BY b.booking_date DESC";
$result = mysqli_query($con, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Booking Dashboard</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link rel="stylesheet" href="../style/styles.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
    <?php include('header.php'); ?>

    <div class="admin-container">
        <?php include('sidebar.php'); ?>
        <div class="container-lg">
            <div class="table-responsive">
                <div class="table-wrapper bg-white mt-3 p-4 rounded">
                    <div class="table-title">
                        <div class="row">
                            <div class="col-sm-8">
                                <h2>Booking <b>Details</b></h2>
                            </div>
                        </div>
                    </div>

                    <?php if (isset($_GET['message'])): ?>
                        <?php if ($_GET['message'] === 'booking_cancelled'): ?>
                            <div class="alert alert-success">Booking cancelled successfully.</div>
                        <?php elseif ($_GET['message'] === 'booking_refunded'): ?>
                            <div class="alert alert-success">Booking refunded successfully.</div>
                        <?php elseif ($_GET['message'] === 'booking_deleted'): ?>
                            <div class="alert alert-success">Booking deleted successfully.</div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if (isset($_GET['error'])): ?>
                        <?php if ($_GET['error'] === 'invalid_booking'): ?>
                            <div class="alert alert-danger">Invalid booking selected.</div>
                        <?php elseif ($_GET['error'] === 'booking_not_found'): ?>
                            <div class="alert alert-danger">Booking not found.</div>
                        <?php elseif ($_GET['error'] === 'refund_not_available'): ?>
                            <div class="alert alert-danger">Refund is not available for this booking.</div>
                        <?php elseif ($_GET['error'] === 'cancel_not_available'): ?>
                            <div class="alert alert-danger">Cancel is not available for paid bookings.</div>
                        <?php elseif ($_GET['error'] === 'delete_failed'): ?>
                            <div class="alert alert-danger">Unable to delete booking. Please try again.</div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div >
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <tr>
                            <th>Booking ID</th>
                            <th>Movie</th>
                            <th>Seats</th>
                                <th>Show Time</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </tr>
                        <tbody>
                            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr align="center">
                                        <td><?php echo htmlspecialchars($row['booking_id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['movie_title']); ?></td>
                                        <td><?php echo htmlspecialchars($row['seats'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars(date('d M Y H:i', strtotime($row['show_time']))); ?></td>
                                        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['customer_phone']); ?></td>
                                        <td><?php echo htmlspecialchars($row['customer_email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                                        <td>PKR <?php echo number_format($row['total_amount'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($row['payment_method'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($row['payment_status'] ?? 'Pending'); ?></td>
                                        <td>
                                            <?php $paymentStatus = trim(strtolower($row['payment_status'] ?? '')); ?>
                                            <a href="editBooking.php?id=<?php echo $row['booking_id']; ?>" class="btn btn-outline-warning btn-sm mb-1">Update</a>
                                            <?php if ($paymentStatus !== 'success'): ?>
                                                <a href="cancelBooking.php?id=<?php echo $row['booking_id']; ?>" class="btn btn-outline-danger btn-sm mb-1">Cancel</a>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-outline-danger btn-sm mb-1" disabled>Cancel</button>
                                            <?php endif; ?>
                                            <?php if ($paymentStatus === 'success'): ?>
                                                <a href="refundBooking.php?id=<?php echo $row['booking_id']; ?>" class="btn btn-outline-secondary btn-sm mb-1">Refund</a>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-outline-secondary btn-sm mb-1" disabled>Refund</button>
                                            <?php endif; ?>
                                            <a href="deleteBooking.php?id=<?php echo $row['booking_id']; ?>" class="btn btn-outline-dark btn-sm">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11">No bookings found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../scripts/jquery-3.3.1.min.js "></script>
    <script src="../scripts/owl.carousel.min.js "></script>
    <script src="../scripts/script.js "></script>
</body>

</html>