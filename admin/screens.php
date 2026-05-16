<?php
include 'config.php';
if (!isset($_SESSION['uname'])) { header('Location: index.php'); exit; }

$message = '';
$cinema_id = isset($_GET['cinema_id']) ? intval($_GET['cinema_id']) : 0;

// Add screen
if (isset($_POST['add_screen'])) {
    $cinema_id = intval($_POST['cinema_id']);
    $screen_name = mysqli_real_escape_string($con, $_POST['screen_name']);
    $total_seats = intval($_POST['total_seats']);
    $sql = "INSERT INTO screen (screen_name, total_seats, cinema_id) VALUES ('$screen_name', $total_seats, $cinema_id)";
    if (mysqli_query($con, $sql)) { $message = 'Screen added.'; } else { $message = 'Error: '.mysqli_real_escape_string($con, mysqli_error($con)); }
}

// Delete screen
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    mysqli_query($con, "DELETE FROM screen WHERE screen_id = $id");
    header('Location: screens.php?cinema_id=' . urlencode($cinema_id)); exit;
}

$cinemas = [];
$cres = mysqli_query($con, "SELECT * FROM cinema ORDER BY cinema_name"); while ($r = mysqli_fetch_assoc($cres)) $cinemas[] = $r;

$screens = [];
$sres = mysqli_query($con, "SELECT sc.*, c.cinema_name FROM screen sc JOIN cinema c ON sc.cinema_id = c.cinema_id " . ($cinema_id ? "WHERE sc.cinema_id = $cinema_id" : "") . " ORDER BY c.cinema_name, sc.screen_name");
while ($r = mysqli_fetch_assoc($sres)) $screens[] = $r;

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Screens</title>
    <link rel="stylesheet" href="../style/styles.css">
    <style>.container-sm{max-width:900px}</style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        <div class="admin-section admin-section2">
            <div class="admin-section-column">
                <div class="admin-section-panel admin-section-panel2">
                    <div class="admin-panel-section-header"><h2>Screens</h2></div>
                    <?php if ($message): ?><div class="alert alert-info mx-4"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
                    <div class="card mx-4 mb-4 container-sm">
                        <div class="card-body">
                            <h5>Add Screen</h5>
                            <form method="POST">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Cinema</label>
                                        <select name="cinema_id" class="form-control" required>
                                            <option value="">Select cinema</option>
                                            <?php foreach ($cinemas as $c): ?>
                                                <option value="<?php echo $c['cinema_id']; ?>" <?php echo $cinema_id == $c['cinema_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['cinema_name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Screen Name</label>
                                        <input name="screen_name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Total Seats</label>
                                        <input name="total_seats" type="number" class="form-control" required>
                                    </div>
                                </div>
                                <button name="add_screen" class="btn btn-primary">Save Screen</button>
                            </form>
                        </div>
                    </div>

                    <div class="mx-4">
                        <h5>Existing Screens</h5>
                        <table class="table table-sm">
                            <thead><tr><th>Screen</th><th>Cinema</th><th>Total Seats</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php foreach ($screens as $s): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($s['screen_name']); ?></td>
                                        <td><?php echo htmlspecialchars($s['cinema_name']); ?></td>
                                        <td><?php echo intval($s['total_seats']); ?></td>
                                        <td>
                                            <a class="btn btn-sm btn-outline-primary" href="seats.php?screen_id=<?php echo $s['screen_id']; ?>">Manage Seats</a>
                                            <a class="btn btn-sm btn-danger" href="screens.php?delete_id=<?php echo $s['screen_id']; ?>&cinema_id=<?php echo $s['cinema_id']; ?>" onclick="return confirm('Delete screen?')">Delete</a>
                                        </td>
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
