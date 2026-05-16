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
    <style>
        .seat-map { display: flex; flex-direction: column; gap: 8px; max-width: 900px; margin: 0 auto; }
        .seat-row { display: flex; align-items: center; }
        .seat-row-label { width: 48px; text-align: center; font-weight: 700; color: #333; margin-right: 12px; }
        .seat-row .seat { width: 48px; height: 48px; margin: 4px; border-radius: 8px; border: 1px solid #d1d7e0; display: flex; align-items: center; justify-content: center; cursor: pointer; font-weight: 600; transition: transform .08s, box-shadow .08s; }
        .seat-row .seat:hover { transform: translateY(-4px); box-shadow: 0 6px 18px rgba(31,42,72,0.12); }
        .seat.available { background-color: #e9f7ef; color: #186a3b; }
        .seat.booked { background-color: #f1f3f5; color: #9aa3ad; cursor: not-allowed; text-decoration: line-through; }
        .seat.vip { background: linear-gradient(135deg,#ffebcc,#ffd27a); color: #6a3f00; }
        .seat.selected { background-color: #2f6fff; color: #fff; box-shadow: 0 8px 22px rgba(47,111,255,0.2); }
        .seat.empty { width: 48px; height: 48px; margin: 4px; background: transparent; border: none; }
        .screen { width: 100%; height: 18px; background-color: #333; color: white; text-align: center; margin: 18px 0; border-radius: 4px; }
        .seat-legend .legend-item { display:flex; align-items:center; gap:8px; margin-right: 12px; }
        .legend-item .seat { width: 22px; height: 22px; margin:0; }
        @media (max-width: 768px) {
            .seat-row-label { width: 36px; margin-right: 8px; }
            .seat-row .seat { width: 38px; height: 38px; }
        }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>

    <div class="container mt-5">
      <div class="row">
        <div class="col-md-4">
              <h1 class="text-light">Seat Selection</h1>
        <p class="text-light"><strong>Movie:</strong> <?php echo htmlspecialchars($show['Title']); ?></p>
        <p class="text-light"><strong>Cinema:</strong> <?php echo htmlspecialchars($show['CinemaName']); ?> (<?php echo htmlspecialchars($show['City']); ?>)</p>
        <p class="text-light"><strong>Screen:</strong> <?php echo htmlspecialchars($show['ScreenName']); ?></p>
        <p class="text-light"><strong>Show Time:</strong> <?php echo date('d M Y, H:i', strtotime($show['ShowTime'])); ?></p>
        <p class="text-light"><strong>Price per Seat:</strong> PKR <?php echo number_format($show['Price'], 2); ?></p>

        </div>
        <div class="col-md-8">
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
                    <?php $maxCol = max(array_keys($seats)); ?>
                    <div class="seat-row ">
                        <div class="seat-row-label text-light"><?php echo $row; ?></div>
                        <div class="seat-row-seats" style="display:flex;flex-wrap:wrap;">
                        <?php for ($col = 1; $col <= $maxCol; $col++): ?>
                            <?php if (isset($seats[$col])): ?>
                                <?php $seat = $seats[$col]; ?>
                                <?php $isBooked = in_array($seat['SeatID'], $booked_seats); ?>
                                <div class="seat <?php echo $isBooked ? 'booked' : 'available'; ?> <?php echo $seat['SeatType'] == 'VIP' ? 'vip' : ''; ?>"
                                     title="<?php echo htmlspecialchars($seat['SeatNumber'] . ' - ' . $seat['SeatType']); ?>"
                                     data-seat-id="<?php echo $seat['SeatID']; ?>"
                                     data-seat-number="<?php echo $seat['SeatNumber']; ?>"
                                     data-seat-type="<?php echo $seat['SeatType']; ?>">
                                    <?php echo htmlspecialchars($seat['SeatNumber']); ?>
                                </div>
                            <?php else: ?>
                                <div class="seat empty" aria-hidden="true"></div>
                            <?php endif; ?>
                        <?php endfor; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-4">
                <h4 class="text-light">Selected Seats: <span id="selected-seats"></span></h4>
                <p class="text-light">Total Amount: PKR <span id="total-amount">0.00</span></p>
                <button type="submit" class="btn btn-primary" id="proceed-btn" disabled>Review Booking</button>
                <a href="movie-details.php?id=<?php echo $show['MovieID']; ?>" class="btn btn-outline-secondary ml-2">Cancel</a>
            </div>
        </form>
        </div>
      </div>
        <!-- <div class="screen">SCREEN</div> -->

        <?php include('includes/footer.php'); ?>
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
            // keyboard accessibility
            seat.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); seat.click(); }
            });
        });

        function updateSelection() {
            document.getElementById('selected-seats').textContent = selectedSeats.map(s => s.number).join(', ');
            document.getElementById('total-amount').textContent = (selectedSeats.length * pricePerSeat).toFixed(2);
            selectedSeatsInput.value = JSON.stringify(selectedSeats);
            document.getElementById('proceed-btn').disabled = selectedSeats.length === 0;
        }
    </script>

   
</body>
</html>