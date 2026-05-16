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

// Verify booking belongs to customer and is confirmed and future
$stmt = $con->prepare("SELECT b.status AS Status, s.show_time AS ShowTime FROM booking b JOIN movie_show s ON b.show_id = s.show_id WHERE b.booking_id = ? AND b.customer_id = ?");
$stmt->bind_param("ii", $booking_id, $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

if (!$booking || $booking['Status'] !== 'Confirmed' || strtotime($booking['ShowTime']) <= time()) {
    header("Location: my-bookings.php");
    exit();
}

// Update booking status to Cancelled
$stmt = $con->prepare("UPDATE booking SET status = 'Cancelled' WHERE booking_id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$stmt->close();

// Update payment status to Refunded
$stmt = $con->prepare("UPDATE payment SET status = 'Refunded' WHERE booking_id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$stmt->close();

header("Location: my-bookings.php?cancelled=1");
exit();
?>