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

$bookingQuery = $con->prepare("SELECT b.booking_id, p.payment_id, p.status AS payment_status
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

if (trim(strtolower($bookingData['payment_status'] ?? '')) === 'success') {
    header('Location: view.php?error=cancel_not_available');
    exit;
}

$updateBooking = $con->prepare("UPDATE booking SET status = 'Cancelled' WHERE booking_id = ?");
$updateBooking->bind_param('i', $bookingId);
$updateBooking->execute();
$updateBooking->close();

if (!empty($bookingData['payment_id']) && $bookingData['payment_status'] !== 'Success') {
    $updatePayment = $con->prepare("UPDATE payment SET status = 'Failed' WHERE payment_id = ?");
    $updatePayment->bind_param('i', $bookingData['payment_id']);
    $updatePayment->execute();
    $updatePayment->close();
}

$updateTickets = $con->prepare("UPDATE ticket SET is_confirmed = 0 WHERE booking_id = ?");
$updateTickets->bind_param('i', $bookingId);
$updateTickets->execute();
$updateTickets->close();

header('Location: view.php?message=booking_cancelled');
exit;
