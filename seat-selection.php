<?php
session_start();
include('connection.php');

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['show_id']) || !is_numeric($_GET['show_id'])) {
    header("Location: movies.php");
    exit();
}

$show_id = (int)$_GET['show_id'];
$customer_id = $_SESSION['customer_id'];

// Get show details
$stmt = $con->prepare("
    SELECT s.show_id AS ShowID, s.show_time AS ShowTime, s.price AS Price, m.movie_id AS MovieID, m.title AS Title, sc.screen_id AS ScreenID, sc.screen_name AS ScreenName, c.cinema_name AS CinemaName, c.city AS City
    FROM movie_show s
    JOIN movie m ON s.movie_id = m.movie_id
    JOIN screen sc ON s.screen_id = sc.screen_id
    JOIN cinema c ON sc.cinema_id = c.cinema_id
    WHERE s.show_id = ?
");
$stmt->bind_param("i", $show_id);
$stmt->execute();
$result = $stmt->get_result();
$show = $result->fetch_assoc();
$stmt->close();

if (!$show) {
    header("Location: movies.php");
    exit();
}

// Get all seats for the screen
$stmt = $con->prepare("SELECT seat_id AS SeatID, seat_number AS SeatNumber, seat_type AS SeatType FROM seat WHERE screen_id = ? ORDER BY seat_number");
$stmt->bind_param("i", $show['ScreenID']);
$stmt->execute();
$seats_result = $stmt->get_result();
$all_seats = [];
while ($row = $seats_result->fetch_assoc()) {
    $all_seats[] = $row;
}
$stmt->close();

// Get booked seats for this show
$stmt = $con->prepare("
    SELECT s.seat_id AS SeatID
    FROM ticket t
    JOIN booking b ON t.booking_id = b.booking_id
    JOIN seat s ON t.seat_id = s.seat_id
    WHERE b.show_id = ? AND b.status IN ('Pending', 'Confirmed')
");
$stmt->bind_param("i", $show_id);
$stmt->execute();
$booked_result = $stmt->get_result();
$booked_seats = [];
while ($row = $booked_result->fetch_assoc()) {
    $booked_seats[] = $row['SeatID'];
}
$stmt->close();

// Group seats by row (assuming SeatNumber like A1, A2, B1, etc.)
$seat_map = [];
foreach ($all_seats as $seat) {
    $row = substr($seat['SeatNumber'], 0, 1);
    $col = (int)substr($seat['SeatNumber'], 1);
    if (!isset($seat_map[$row])) {
        $seat_map[$row] = [];
    }
    $seat_map[$row][$col] = $seat;
}
ksort($seat_map);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Selection - <?php echo htmlspecialchars($show['Title']); ?></title>
    <link rel="stylesheet" href="style/styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .seat-map { display: flex; flex-direction: column; align-items: center; }
        .seat-row { display: flex; margin: 5px 0; }
        .seat { width: 30px; height: 30px; margin: 2px; border: 1px solid #ccc; display: flex; align-items: center; justify-content: center; cursor: pointer; }
        .seat.available { background-color: #28a745; color: white; }
        .seat.booked { background-color: #dc3545; color: white; cursor: not-allowed; }
        .seat.vip { background-color: #ffc107; }
        .seat.selected { background-color: #007bff; }
        .screen { width: 100%; height: 20px; background-color: #333; color: white; text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>

    <div class="container mt-5">
        <h1>Seat Selection</h1>
        <p><strong>Movie:</strong> <?php echo htmlspecialchars($show['Title']); ?></p>
        <p><strong>Cinema:</strong> <?php echo htmlspecialchars($show['CinemaName']); ?> (<?php echo htmlspecialchars($show['City']); ?>)</p>
        <p><strong>Screen:</strong> <?php echo htmlspecialchars($show['ScreenName']); ?></p>
        <p><strong>Show Time:</strong> <?php echo date('d M Y, H:i', strtotime($show['ShowTime'])); ?></p>
        <p><strong>Price per Seat:</strong> PKR <?php echo number_format($show['Price'], 2); ?></p>

        <div class="screen">SCREEN</div>

        <div class="seat-legend mb-4 d-flex flex-wrap gap-3">
            <div class="legend-item"><span class="seat available"></span> Available</div>
            <div class="legend-item"><span class="seat vip"></span> VIP</div>
            <div class="legend-item"><span class="seat booked"></span> Booked</div>
            <div class="legend-item"><span class="seat selected"></span> Selected</div>
        </div>

        <form id="seat-form" method="POST" action="confirm-booking.php">
            <input type="hidden" name="show_id" value="<?php echo $show_id; ?>">
            <input type="hidden" name="selected_seats" id="selected-seats-input">
            <div class="seat-map">
                <?php foreach ($seat_map as $row => $seats): ?>
                    <div class="seat-row align-items-center">
                        <div class="seat-row-label mr-3"><?php echo $row; ?></div>
                        <?php for ($col = 1; $col <= max(array_keys($seats)); $col++): ?>
                            <?php if (isset($seats[$col])): ?>
                                <?php $seat = $seats[$col]; ?>
                                <div class="seat <?php echo in_array($seat['SeatID'], $booked_seats) ? 'booked' : 'available'; ?> <?php echo $seat['SeatType'] == 'VIP' ? 'vip' : ''; ?>"
                                     data-seat-id="<?php echo $seat['SeatID']; ?>"
                                     data-seat-number="<?php echo $seat['SeatNumber']; ?>"
                                     data-seat-type="<?php echo $seat['SeatType']; ?>">
                                    <?php echo htmlspecialchars($seat['SeatNumber']); ?>
                                </div>
                            <?php else: ?>
                                <div class="seat empty"></div>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-4">
                <h4>Selected Seats: <span id="selected-seats"></span></h4>
                <p>Total Amount: PKR <span id="total-amount">0.00</span></p>
                <button type="submit" class="btn btn-primary" id="proceed-btn" disabled>Review Booking</button>
                <a href="movie-details.php?id=<?php echo $show['MovieID']; ?>" class="btn btn-outline-secondary ml-2">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        const seats = document.querySelectorAll('.seat.available, .seat.vip');
        const selectedSeatsInput = document.getElementById('selected-seats-input');

        let selectedSeats = [];
        const pricePerSeat = <?php echo $show['Price']; ?>;

        seats.forEach(seat => {
            seat.addEventListener('click', () => {
                if (seat.classList.contains('booked')) return;

                const seatId = seat.dataset.seatId;
                const seatNumber = seat.dataset.seatNumber;

                if (seat.classList.contains('selected')) {
                    seat.classList.remove('selected');
                    selectedSeats = selectedSeats.filter(s => s.id !== seatId);
                } else {
                    seat.classList.add('selected');
                    selectedSeats.push({ id: seatId, number: seatNumber });
                }

                updateSelection();
            });
        });

        function updateSelection() {
            document.getElementById('selected-seats').textContent = selectedSeats.map(s => s.number).join(', ');
            document.getElementById('total-amount').textContent = (selectedSeats.length * pricePerSeat).toFixed(2);
            selectedSeatsInput.value = JSON.stringify(selectedSeats);
            document.getElementById('proceed-btn').disabled = selectedSeats.length === 0;
        }
    </script>

    <?php include('includes/footer.php'); ?>
</body>
</html>