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
if (!is_array($selected_seats) || empty($selected_seats)) {
    header("Location: seat-selection.php?show_id=$show_id");
    exit();
}

// Fetch show and movie details
$stmt = $con->prepare("SELECT s.show_id AS ShowID, s.show_time AS ShowTime, s.price AS Price, m.movie_id AS MovieID, m.title AS Title, sc.screen_name AS ScreenName, c.cinema_name AS CinemaName, c.city AS City
                       FROM movie_show s
                       JOIN movie m ON s.movie_id = m.movie_id
                       JOIN screen sc ON s.screen_id = sc.screen_id
                       JOIN cinema c ON sc.cinema_id = c.cinema_id
                       WHERE s.show_id = ?");
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Booking - Online Movie Ticket Booking</title>
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
    <?php include('includes/header.php'); ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Review Your Booking</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Movie:</strong> <?php echo htmlspecialchars($show['Title']); ?></p>
                        <p><strong>Show Time:</strong> <?php echo date('d M Y, H:i', strtotime($show['ShowTime'])); ?></p>
                        <p><strong>Cinema:</strong> <?php echo htmlspecialchars($show['CinemaName']); ?> (<?php echo htmlspecialchars($show['City']); ?>)</p>
                        <p><strong>Screen:</strong> <?php echo htmlspecialchars($show['ScreenName']); ?></p>
                        <p><strong>Price per Seat:</strong> PKR <?php echo number_format($price_per_seat, 2); ?></p>

                        <h5 class="mt-4">Selected Seats</h5>
                        <p><?php echo htmlspecialchars(implode(', ', array_column($selected_seats, 'number'))); ?></p>
                        <p><strong>Seat Count:</strong> <?php echo count($selected_seats); ?></p>
                        <p><strong>Total Amount:</strong> PKR <?php echo number_format($total_amount, 2); ?></p>

                        <form method="POST" action="booking.php">
                            <input type="hidden" name="show_id" value="<?php echo $show_id; ?>">
                            <input type="hidden" name="selected_seats" value="<?php echo htmlspecialchars(json_encode($selected_seats), ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="submit" class="btn btn-primary">Confirm and Pay</button>
                            <a href="seat-selection.php?show_id=<?php echo $show_id; ?>" class="btn btn-outline-secondary ml-2">Back to Seat Selection</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>
</body>
</html>
