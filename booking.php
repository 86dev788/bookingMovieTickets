<?php
session_start();
include('connection.php');

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['show_id']) || !isset($_POST['selected_seats'])) {
    header("Location: movies.php");
    exit();
}

$show_id = (int)$_POST['show_id'];
$selected_seats = json_decode($_POST['selected_seats'], true);
$customer_id = $_SESSION['customer_id'];

if (empty($selected_seats)) {
    header("Location: seat-selection.php?show_id=$show_id");
    exit();
}

// Get show details
$stmt = $con->prepare("SELECT price AS Price FROM movie_show WHERE show_id = ?");
$stmt->bind_param("i", $show_id);
$stmt->execute();
$result = $stmt->get_result();
$show = $result->fetch_assoc();
$stmt->close();

if (!$show) {
    header("Location: movies.php");
    exit();
}

$price_per_seat = $show['Price'];
$total_amount = count($selected_seats) * $price_per_seat;

// Start transaction
$con->begin_transaction();

try {
    // Insert booking
    $stmt = $con->prepare("INSERT INTO booking (status, total_amount, customer_id, show_id) VALUES ('Pending', ?, ?, ?)");
    $stmt->bind_param("dii", $total_amount, $customer_id, $show_id);
    $stmt->execute();
    $booking_id = $con->insert_id;
    $stmt->close();

    // Insert payment (assuming Cash for now, status Pending)
    $stmt = $con->prepare("INSERT INTO payment (amount, method, status, booking_id) VALUES (?, 'Cash', 'Pending', ?)");
    $stmt->bind_param("di", $total_amount, $booking_id);
    $stmt->execute();
    $payment_id = $con->insert_id;
    $stmt->close();

    // Insert provisional tickets as reservations (qr_code prefixed with RESV, not yet confirmed)
    $stmt = $con->prepare("INSERT INTO ticket (qr_code, booking_id, seat_id, is_confirmed) VALUES (?, ?, ?, 0)");
    foreach ($selected_seats as $seat) {
        $qr_code = 'RESV' . uniqid();
        $stmt->bind_param("sii", $qr_code, $booking_id, $seat['id']);
        $stmt->execute();
    }
    $stmt->close();

    // Commit transaction
    $con->commit();

    // Redirect to payment page or confirmation
    header("Location: payment.php?booking_id=$booking_id");
    exit();

} catch (Exception $e) {
    $con->rollback();
    echo "Booking failed: " . $e->getMessage();
    exit();
}
?>