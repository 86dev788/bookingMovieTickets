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

function finalizeBookingTickets($con, $booking_id) {
    $tstmt = $con->prepare("SELECT ticket_id, seat_id FROM ticket WHERE booking_id = ?");
    $tstmt->bind_param("i", $booking_id);
    $tstmt->execute();
    $tres = $tstmt->get_result();
    $tstmt->close();

    if ($tres) {
        $ustmt = $con->prepare("UPDATE ticket SET qr_code = ?, is_confirmed = 1, confirmed_at = NOW(), generated_at = NOW() WHERE ticket_id = ?");
        while ($trow = $tres->fetch_assoc()) {
            $final_qr = 'TK' . $booking_id . '_' . $trow['seat_id'] . '_' . uniqid();
            $ustmt->bind_param("si", $final_qr, $trow['ticket_id']);
            $ustmt->execute();
        }
        $ustmt->close();
    }
}

$simulate_needed = false;
$selected_method = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_POST['method'] ?? '';

    // If a gateway simulation outcome was posted, finalize the payment
    if (isset($_POST['simulate_outcome'])) {
        $outcome = $_POST['simulate_outcome']; // 'success' or 'failed'

        if ($outcome === 'success') {
            // Mark payment success with timestamp
            $stmt = $con->prepare("UPDATE payment SET method = ?, status = 'Success', paid_at = NOW() WHERE booking_id = ?");
            $stmt->bind_param("si", $method, $booking_id);
            $stmt->execute();
            $stmt->close();

            // Confirm booking
            $stmt = $con->prepare("UPDATE booking SET status = 'Confirmed' WHERE booking_id = ?");
            $stmt->bind_param("i", $booking_id);
            $stmt->execute();
            $stmt->close();

            // Prepare flash message and redirect to confirmation after showing it
            $show_flash = true;
            $flash_message = 'Payment successful. Redirecting to confirmation...';
            $flash_type = 'success';
            $redirect_url = "booking-confirmation.php?booking_id=$booking_id";
            $redirect_delay = 3000;
            // continue to render page which will show the flash
            // Finalize provisional tickets: generate final QR codes and mark confirmed
            $tstmt = $con->prepare("SELECT ticket_id, seat_id FROM ticket WHERE booking_id = ?");
            $tstmt->bind_param("i", $booking_id);
            $tstmt->execute();
            $tres = $tstmt->get_result();
            $tstmt->close();
            if ($tres) {
                $ustmt = $con->prepare("UPDATE ticket SET qr_code = ?, is_confirmed = 1, confirmed_at = NOW(), generated_at = NOW() WHERE ticket_id = ?");
                while ($trow = $tres->fetch_assoc()) {
                    $final_qr = 'TK' . $booking_id . '_' . $trow['seat_id'] . '_' . uniqid();
                    $ustmt->bind_param("si", $final_qr, $trow['ticket_id']);
                    $ustmt->execute();
                }
                $ustmt->close();
            }
        } else {
            // Mark payment failed
            $stmt = $con->prepare("UPDATE payment SET method = ?, status = 'Failed' WHERE booking_id = ?");
            $stmt->bind_param("si", $method, $booking_id);
            $stmt->execute();
            $stmt->close();

            // Release seats: delete the booking which cascades to tickets and payment (if FK cascade is set)
            try {
                $del = $con->prepare("DELETE FROM booking WHERE booking_id = ?");
                $del->bind_param("i", $booking_id);
                $del->execute();
                $del->close();
            } catch (Exception $e) {
                // Log or ignore — seat release best-effort
            }

            // Prepare flash message and redirect to movies after releasing seats
            $show_flash = true;
            $flash_message = 'Payment failed. Seats released. Redirecting to movies...';
            $flash_type = 'danger';
            $redirect_url = 'movies.php?payment_failed=1';
            $redirect_delay = 3000;
            // continue to render page which will show the flash
        }
    }

    // First POST (method selection): Cash completes immediately, other methods simulate gateway
    if ($method === 'Cash') {
        $stmt = $con->prepare("UPDATE payment SET method = ?, status = 'Success', paid_at = NOW() WHERE booking_id = ?");
        $stmt->bind_param("si", $method, $booking_id);
        $stmt->execute();
        $stmt->close();

        $stmt = $con->prepare("UPDATE booking SET status = 'Confirmed' WHERE booking_id = ?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $stmt->close();

        finalizeBookingTickets($con, $booking_id);

        // Prepare flash message and redirect to confirmation after showing it
        $show_flash = true;
        $flash_message = 'Payment successful (Cash). Redirecting to confirmation...';
        $flash_type = 'success';
        $redirect_url = "booking-confirmation.php?booking_id=$booking_id";
        $redirect_delay = 3000;
        // continue to render page which will show the flash
    } else {
        // For Card / EasyPaisa / JazzCash show a simulated gateway page
        $simulate_needed = true;
        $selected_method = $method;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Online Movie Ticket Booking</title>
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
    <?php include('includes/header.php'); ?>

    <?php if (!empty($show_flash)): ?>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="alert alert-<?php echo htmlspecialchars($flash_type ?? 'info'); ?>" role="alert">
                        <?php echo htmlspecialchars($flash_message ?? ''); ?>
                        <div class="mt-2"><a href="<?php echo htmlspecialchars($redirect_url ?? 'movies.php'); ?>">Click here if you are not redirected</a></div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            setTimeout(function(){ window.location.href = '<?php echo addslashes($redirect_url ?? 'movies.php'); ?>'; }, <?php echo intval($redirect_delay ?? 3000); ?>);
        </script>
        <?php include('includes/footer.php'); ?>
        <?php exit(); ?>
    <?php endif; ?>

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

                        <?php if ($simulate_needed): ?>
                            <div class="alert alert-info">Redirecting to <strong><?php echo htmlspecialchars($selected_method); ?></strong> gateway (simulation). Choose outcome below to complete payment.</div>
                            <form method="POST">
                                <input type="hidden" name="method" value="<?php echo htmlspecialchars($selected_method); ?>">
                                <button type="submit" name="simulate_outcome" value="success" class="btn btn-success">Simulate Success</button>
                                <button type="submit" name="simulate_outcome" value="failed" class="btn btn-danger">Simulate Failure</button>
                            </form>
                        <?php else: ?>
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
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>
</body>
</html>