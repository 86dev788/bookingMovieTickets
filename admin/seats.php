<?php
include 'config.php';
if (!isset($_SESSION['uname'])) { header('Location: index.php'); exit; }

$screen_id = isset($_GET['screen_id']) ? intval($_GET['screen_id']) : 0;
$message = '';

if ($screen_id <= 0) {
    echo "<script>window.location='screens.php';</script>"; exit;
}

// Add seat
if (isset($_POST['add_seat'])) {
    $seat_number = mysqli_real_escape_string($con, $_POST['seat_number']);
    $seat_type = mysqli_real_escape_string($con, $_POST['seat_type']);
    $sql = "INSERT INTO seat (seat_number, seat_type, screen_id) VALUES ('$seat_number', '$seat_type', $screen_id)";
    if (mysqli_query($con, $sql)) { $message = 'Seat added.'; } else { $message = 'Error: '.mysqli_real_escape_string($con, mysqli_error($con)); }
}

// Bulk generate seats
if (isset($_POST['generate_seats'])) {
    $row = strtoupper(substr(trim($_POST['bulk_row']),0,1));
    $start = intval($_POST['bulk_start']);
    $end = intval($_POST['bulk_end']);
    $type = mysqli_real_escape_string($con, $_POST['bulk_type']);
    $screen_id = intval($_POST['screen_id']);
    if ($row !== '' && $start > 0 && $end >= $start) {
        $count = 0;
        for ($i = $start; $i <= $end; $i++) {
            $seat_no = $row . $i;
            $safeSeat = mysqli_real_escape_string($con, $seat_no);
            $sql = "INSERT INTO seat (seat_number, seat_type, screen_id) VALUES ('$safeSeat', '$type', $screen_id)";
            if (mysqli_query($con, $sql)) $count++;
        }
        $message = "$count seats generated for row $row.";
        // refresh seats list
        header('Location: seats.php?screen_id=' . $screen_id);
        exit;
    } else {
        $message = 'Invalid bulk input.';
    }
}

if (isset($_GET['delete_seat'])) {
    $id = intval($_GET['delete_seat']); mysqli_query($con, "DELETE FROM seat WHERE seat_id = $id"); header('Location: seats.php?screen_id='.$screen_id); exit;
}

$screen = mysqli_fetch_assoc(mysqli_query($con, "SELECT sc.*, c.cinema_name FROM screen sc JOIN cinema c ON sc.cinema_id = c.cinema_id WHERE sc.screen_id = $screen_id"));
$seats = [];
$sres = mysqli_query($con, "SELECT * FROM seat WHERE screen_id = $screen_id ORDER BY seat_number"); while ($r = mysqli_fetch_assoc($sres)) $seats[] = $r;

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Seats</title>
    <link rel="stylesheet" href="../style/styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        <div class="admin-section admin-section2">
            <div class="admin-section-column">
                <div class="admin-section-panel admin-section-panel2">
                    <div class="admin-panel-section-header"><h2>Seats for <?php echo htmlspecialchars($screen['screen_name']); ?> (<?php echo htmlspecialchars($screen['cinema_name']); ?>)</h2></div>
                    <?php if ($message): ?><div class="alert alert-info mx-4"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
                    <div class="card mx-4 mb-4">
                        <div class="card-body">
                            <h5>Add Single Seat</h5>
                            <form method="POST" class="form-inline mb-3">
                                <div class="form-group mr-2"><label class="mr-2">Seat No</label><input name="seat_number" class="form-control" required></div>
                                <div class="form-group mr-2"><label class="mr-2">Type</label><select name="seat_type" class="form-control"><option>Standard</option><option>VIP</option><option>Recliner</option></select></div>
                                <button name="add_seat" class="btn btn-primary">Add Seat</button>
                            </form>

                            <hr>
                            <h5>Bulk Generate Seats</h5>
                            <form method="POST" class="form-inline">
                                <div class="form-group mr-2"><label class="mr-2">Row</label><input name="bulk_row" class="form-control" placeholder="A" required></div>
                                <div class="form-group mr-2"><label class="mr-2">Start</label><input name="bulk_start" type="number" class="form-control" placeholder="1" required></div>
                                <div class="form-group mr-2"><label class="mr-2">End</label><input name="bulk_end" type="number" class="form-control" placeholder="10" required></div>
                                <div class="form-group mr-2"><label class="mr-2">Type</label><select name="bulk_type" class="form-control"><option>Standard</option><option>VIP</option><option>Recliner</option></select></div>
                                <input type="hidden" name="screen_id" value="<?php echo $screen_id; ?>">
                                <button name="generate_seats" class="btn btn-secondary">Generate Seats</button>
                            </form>
                        </div>
                    </div>
                    <div class="mx-4">
                        <table class="table table-sm">
                            <thead><tr><th>Seat No</th><th>Type</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php foreach ($seats as $st): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($st['seat_number']); ?></td>
                                        <td><?php echo htmlspecialchars($st['seat_type']); ?></td>
                                        <td><a class="btn btn-sm btn-danger" href="seats.php?screen_id=<?php echo $screen_id; ?>&delete_seat=<?php echo $st['seat_id']; ?>" onclick="return confirm('Delete seat?')">Delete</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
