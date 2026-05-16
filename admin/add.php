<?php
include "config.php";

// Check user login or not
if (!isset($_SESSION['uname'])) {
    header('Location: index.php');
    exit;
}

$bookingsNo = mysqli_num_rows(mysqli_query($con, "SELECT * FROM booking"));
$messagesNo = mysqli_num_rows(mysqli_query($con, "SELECT * FROM feedbacktable"));
$moviesNo = mysqli_num_rows(mysqli_query($con, "SELECT * FROM movie"));

$showOptions = [];
$showQuery = "SELECT ms.show_id, m.title AS movie_title, c.cinema_name, sc.screen_name, ms.show_time, ms.price
    FROM movie_show ms
    JOIN movie m ON ms.movie_id = m.movie_id
    JOIN screen sc ON ms.screen_id = sc.screen_id
    JOIN cinema c ON sc.cinema_id = c.cinema_id
    ORDER BY ms.show_time";

if ($showResult = mysqli_query($con, $showQuery)) {
    while ($row = mysqli_fetch_assoc($showResult)) {
        $showOptions[] = $row;
    }
    mysqli_free_result($showResult);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add Booking</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link rel="stylesheet" href="../style/styles.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
</head>

<body>
    <?php include('header.php'); ?>
    <div class="admin-container">
        <?php include('sidebar.php'); ?>
        <div class="admin-section admin-section2">
            <div class="admin-section-column">
                <div class="admin-section-panel admin-section-panel2">
                    <div class="admin-panel-section-header">
                        <h2>ADD BOOKING</h2>
                        <i class="fas fa-film" style="background-color: #4547cf"></i>
                    </div>
                    <div class="booking-form-container">
                        <form action="spot.php" method="POST">
                            <div class="mb-3">
                                <select name="show_id" class="form-select" required>
                                    <option value="" disabled selected>SELECT SHOW</option>
                                    <?php foreach ($showOptions as $show): ?>
                                        <option value="<?php echo $show['show_id']; ?>">
                                            <?php echo htmlspecialchars($show['movie_title'] . ' - ' . $show['cinema_name'] . ' / ' . $show['screen_name'] . ' @ ' . date('d M Y H:i', strtotime($show['show_time'])) . ' (PKR ' . number_format($show['price'], 2) . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3"><input class="form-control" placeholder="First Name" type="text" name="fName" required></div>
                            <div class="mb-3"><input class="form-control" placeholder="Last Name" type="text" name="lName"></div>
                            <div class="mb-3"><input class="form-control" placeholder="Phone Number" type="text" name="pNumber" required></div>
                            <div class="mb-3"><input class="form-control" placeholder="Email" type="email" name="email" required></div>
                            <div class="mb-3"><input class="form-control" placeholder="Total Amount (leave blank for show price)" type="text" name="amount"></div>

                            <button type="submit" value="submit" name="submit" class="btn btn-primary">ADD BOOKING</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../scripts/jquery-3.3.1.min.js "></script>
    <script src="../scripts/owl.carousel.min.js "></script>
    <script src="../scripts/script.js "></script>
</body>

</html>