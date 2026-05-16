<?php
include 'config.php';

if (!isset($_SESSION['uname'])) {
    header('Location: index.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: show-schedule.php');
    exit;
}

$show_id = intval($_GET['id']);

$deleteSql = "DELETE FROM movie_show WHERE show_id = $show_id";
if (!mysqli_query($con, $deleteSql)) {
    $_SESSION['show_delete_error'] = 'Could not delete show. It may be linked to existing bookings.';
}

header('Location: show-schedule.php');
exit;
