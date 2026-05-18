<?php
include 'config.php';

if (!isset($_SESSION['uname'])) {
    header('Location: index.php');
    exit;
}

$bookingId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($bookingId <= 0) {
    header('Location: view.php?error=invalid_booking');
    exit;
}

// Fetch the booking and payment to determine current status.
$bookingQuery = $con->prepare("SELECT b.booking_id, b.status, p.payment_id, p.status AS payment_status
    FROM booking b
    LEFT JOIN payment p ON p.booking_id = b.booking_id
    WHERE b.booking_id = ?");
$bookingQuery->bind_param('i', $bookingId);
$bookingQuery->execute();
$result = $bookingQuery->get_result();
$bookingData = $result->fetch_assoc();
$bookingQuery->close();

if (!$bookingData) {
    header('Location: view.php?error=booking_not_found');
    exit;
}

if (trim(strtolower($bookingData['payment_status'] ?? '')) !== 'success') {
    header('Location: view.php?error=refund_not_available');
    exit;
}

// Update booking status to Refunded.
$updateBooking = $con->prepare("UPDATE booking SET status = 'Refunded' WHERE booking_id = ?");
$updateBooking->bind_param('i', $bookingId);
$updateBooking->execute();
$updateBooking->close();

// Update payment if a record exists.
$message = 'booking_refunded';
if (!empty($bookingData['payment_id'])) {
    $newPaymentStatus = 'Refunded';
    $updatePayment = $con->prepare("UPDATE payment SET status = ? WHERE payment_id = ?");
    $updatePayment->bind_param('si', $newPaymentStatus, $bookingData['payment_id']);
    $updatePayment->execute();
    $updatePayment->close();
}

// Mark tickets as not confirmed so cancelled bookings are clearly invalid.
$updateTickets = $con->prepare("UPDATE ticket SET is_confirmed = 0 WHERE booking_id = ?");
$updateTickets->bind_param('i', $bookingId);
$updateTickets->execute();
$updateTickets->close();

header('Location: view.php?message=' . $message);
exit;
