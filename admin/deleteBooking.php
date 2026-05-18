<?php
include "config.php";
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $sql = "DELETE FROM booking WHERE booking_id = $id";
    if ($con->query($sql) === TRUE) {
        header('Location: view.php?message=booking_deleted');
        exit;
    } else {
        header('Location: view.php?error=delete_failed');
        exit;
    }
} else {
    header('Location: view.php?error=invalid_booking');
    exit;
}
?>