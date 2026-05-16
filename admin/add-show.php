<?php
include 'config.php';

if (!isset($_SESSION['uname'])) {
    header('Location: index.php');
    exit;
}

$movies = [];
$movieQuery = "SELECT movie_id, title FROM movie ORDER BY title";
if ($movieResult = mysqli_query($con, $movieQuery)) {
    while ($row = mysqli_fetch_assoc($movieResult)) {
        $movies[] = $row;
    }
    mysqli_free_result($movieResult);
}

$screens = [];
$screenQuery = "SELECT sc.screen_id, sc.screen_name, c.cinema_name, c.city
                FROM screen sc
                JOIN cinema c ON sc.cinema_id = c.cinema_id
                ORDER BY c.cinema_name, sc.screen_name";
if ($screenResult = mysqli_query($con, $screenQuery)) {
    while ($row = mysqli_fetch_assoc($screenResult)) {
        $screens[] = $row;
    }
    mysqli_free_result($screenResult);
}

$successMessage = '';
$errorMessage = '';
if (isset($_POST['submit'])) {
    $movie_id = intval($_POST['movie_id']);
    $screen_id = intval($_POST['screen_id']);
    $show_time = trim($_POST['show_time'] ?? '');
    $price = trim($_POST['price'] ?? '');

    if ($movie_id === 0 || $screen_id === 0 || $show_time === '' || $price === '') {
        $errorMessage = 'Please fill all fields and select a movie and screen.';
    } else {
        $show_time = str_replace('T', ' ', $show_time);
        $price = number_format((float)$price, 2, '.', '');
        $insertSql = "INSERT INTO movie_show (show_time, price, movie_id, screen_id) VALUES ('" . mysqli_real_escape_string($con, $show_time) . "', $price, $movie_id, $screen_id)";

        if (mysqli_query($con, $insertSql)) {
            $successMessage = 'Show added successfully.';
        } else {
            $errorMessage = 'Error adding show: ' . mysqli_real_escape_string($con, mysqli_error($con));
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Show - Admin</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link rel="stylesheet" href="../style/styles.css">
</head>
<body>
    <?php include('header.php'); ?>
    <div class="admin-container">
        <?php include('sidebar.php'); ?>
        <div class="admin-section admin-section2">
            <div class="admin-section-column">
                <div class="admin-section-panel admin-section-panel2">
                    <div class="admin-panel-section-header">
                        <h2>Add Show</h2>
                        <i class="fas fa-calendar-plus" style="background-color: #4547cf"></i>
                    </div>
                    <?php if ($successMessage): ?>
                        <div class="alert alert-success mx-4"><?php echo htmlspecialchars($successMessage); ?></div>
                    <?php endif; ?>
                    <?php if ($errorMessage): ?>
                        <div class="alert alert-danger mx-4"><?php echo htmlspecialchars($errorMessage); ?></div>
                    <?php endif; ?>
                    <div class="booking-form-container">
                        <form action="" method="POST">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="movie_id">Movie</label>
                                    <select id="movie_id" name="movie_id" class="form-control" required>
                                        <option value="" disabled selected>Select a movie</option>
                                        <?php foreach ($movies as $movie): ?>
                                            <option value="<?php echo $movie['movie_id']; ?>"><?php echo htmlspecialchars($movie['title']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="screen_id">Screen</label>
                                    <select id="screen_id" name="screen_id" class="form-control" required>
                                        <option value="" disabled selected>Select a screen</option>
                                        <?php foreach ($screens as $screen): ?>
                                            <option value="<?php echo $screen['screen_id']; ?>"><?php echo htmlspecialchars($screen['cinema_name'] . ' / ' . $screen['screen_name'] . ' (' . $screen['city'] . ')'); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="show_time">Show Time</label>
                                    <input id="show_time" class="form-control" type="datetime-local" name="show_time" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="price">Ticket Price</label>
                                    <input id="price" class="form-control" type="number" step="0.01" name="price" placeholder="1500.00" required>
                                </div>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary">Save Show</button>
                            <a href="show-schedule.php" class="btn btn-outline-secondary ml-2">Show Schedule</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../scripts/jquery-3.3.1.min.js"></script>
    <script src="../scripts/owl.carousel.min.js"></script>
    <script src="../scripts/script.js"></script>
</body>
</html>
