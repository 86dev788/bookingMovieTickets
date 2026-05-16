<?php
session_start();
include('connection.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: movies.php");
    exit();
}

$movie_id = (int)$_GET['id'];

$stmt = $con->prepare("SELECT * FROM movie WHERE movie_id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();
$movie = $result->fetch_assoc();
$stmt->close();

if (!$movie) {
    header("Location: movies.php");
    exit();
}

// Get all shows for this movie
$stmt = $con->prepare("
    SELECT s.show_id, s.show_time, s.price, sc.screen_name, c.cinema_name, c.city
    FROM movie_show s
    JOIN screen sc ON s.screen_id = sc.screen_id
    JOIN cinema c ON sc.cinema_id = c.cinema_id
    WHERE s.movie_id = ?
    ORDER BY s.show_time
");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$shows_result = $stmt->get_result();
$shows = [];
while ($row = $shows_result->fetch_assoc()) {
    $shows[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($movie['title']); ?> - Online Movie Ticket Booking</title>
    <link rel="stylesheet" href="style/styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include('includes/header.php'); ?>

    <div class="container movie-detail-page mt-5">
        <div class="movie-detail-card mb-5">
            <div class="row no-gutters">
                <div class="col-lg-5">
                    <div class="movie-detail-poster">
                        <img src="<?php echo htmlspecialchars($movie['poster_url'] ?: 'img/default-movie.jpg'); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($movie['title']); ?> poster">
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="movie-detail-content p-4">
                        <h1 class="mb-3"><?php echo htmlspecialchars($movie['title']); ?></h1>
                        <div class="movie-detail-badges mb-4">
                            <span class="badge badge-primary"><?php echo htmlspecialchars($movie['rating']); ?></span>
                            <span class="badge badge-secondary"><?php echo htmlspecialchars($movie['language']); ?></span>
                            <span class="badge badge-info"><?php echo intval($movie['duration_min']); ?> min</span>
                        </div>
                        <p class="movie-detail-genres mb-2"><strong>Genres:</strong> <?php echo htmlspecialchars($movie['genres']); ?></p>
                        <p class="movie-detail-release mb-2"><strong>Release date:</strong> <?php echo date('d M Y', strtotime($movie['release_date'])); ?></p>
                        <p class="movie-detail-description mb-0"><strong>Description:</strong> <?php echo htmlspecialchars($movie['description']); ?></p>
                        <div class="mt-4">
                            <a href="movies.php" class="btn btn-outline-secondary">Back to Browsing</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="mb-4">Available Shows</h2>
        <?php if (empty($shows)): ?>
            <p>No shows available for this movie.</p>
        <?php else: ?>
            <div class="row">
                <?php foreach ($shows as $show): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($show['cinema_name']); ?> - <?php echo htmlspecialchars($show['city']); ?></h5>
                                <p class="card-text">
                                    Screen: <?php echo htmlspecialchars($show['screen_name']); ?><br>
                                    Time: <?php echo date('d M Y, H:i', strtotime($show['show_time'])); ?><br>
                                    Price: PKR <?php echo number_format($show['price'], 2); ?>
                                </p>
                                <a href="seat-selection.php?show_id=<?php echo $show['show_id']; ?>" class="btn btn-primary">Select Seats</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include('includes/footer.php'); ?>
</body>
</html>