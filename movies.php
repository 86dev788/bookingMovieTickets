<?php
session_start();
include('connection.php');

$search = trim($_GET['search'] ?? '');
$city = trim($_GET['city'] ?? '');
$cinema = trim($_GET['cinema'] ?? '');

$cityResult = $con->query("SELECT DISTINCT city FROM cinema ORDER BY city");
$cinemaResult = $con->query("SELECT DISTINCT cinema_name FROM cinema ORDER BY cinema_name");
$cities = $cityResult ? $cityResult->fetch_all(MYSQLI_ASSOC) : [];
$cinemas = $cinemaResult ? $cinemaResult->fetch_all(MYSQLI_ASSOC) : [];

$sql = "SELECT m.*, 
    GROUP_CONCAT(DISTINCT c.city ORDER BY c.city SEPARATOR ', ') AS available_cities,
    GROUP_CONCAT(DISTINCT c.cinema_name ORDER BY c.cinema_name SEPARATOR ', ') AS available_cinemas
FROM movie m
LEFT JOIN movie_show ms ON m.movie_id = ms.movie_id
LEFT JOIN screen sc ON ms.screen_id = sc.screen_id
LEFT JOIN cinema c ON sc.cinema_id = c.cinema_id
WHERE 1=1";

$params = [];
$types = '';
if ($search !== '') {
    $sql .= " AND (m.title LIKE ? OR m.genres LIKE ? )";
    $searchTerm = "%$search%";
    $types .= 'ss';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}
if ($city !== '') {
    $sql .= " AND c.city = ?";
    $types .= 's';
    $params[] = $city;
}
if ($cinema !== '') {
    $sql .= " AND c.cinema_name = ?";
    $types .= 's';
    $params[] = $cinema;
}
$sql .= " GROUP BY m.movie_id ORDER BY m.release_date DESC";

$stmt = $con->prepare($sql);
if ($stmt) {
    if ($types !== '') {
        $bindParams = array_merge([$types], $params);
        $bindRefs = [];
        foreach ($bindParams as $key => $value) {
            $bindRefs[$key] = &$bindParams[$key];
        }
        call_user_func_array([$stmt, 'bind_param'], $bindRefs);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $movies = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();
} else {
    $movies = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movies - Online Movie Ticket Booking</title>
    <link rel="stylesheet" href="style/styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include('includes/header.php'); ?>

    <div class="browse-hero py-5 bg-dark text-white">
        <div class="container">
            <small class="text-uppercase text-muted">Movie browsing</small>
            <h1 class="display-4 font-weight-bold">Browse Available Movies</h1>
            <p class="lead text-light">Search by title or genre, and filter by city or cinema to find the best show for you.</p>
        </div>
    </div>

    <div class="container my-5">
        <div class="browse-filters card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" class="form-row align-items-end">
                    <div class="col-md-4 mb-3">
                        <label for="search" class="font-weight-bold">Search title or genre</label>
                        <input id="search" type="search" name="search" value="<?php echo htmlspecialchars($search); ?>" class="form-control" placeholder="Search movies, genres...">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="city" class="font-weight-bold">City</label>
                        <select id="city" name="city" class="form-control">
                            <option value="">All cities</option>
                            <?php foreach ($cities as $item): ?>
                                <option value="<?php echo htmlspecialchars($item['city']); ?>" <?php echo $city === $item['city'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($item['city']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="cinema" class="font-weight-bold">Cinema</label>
                        <select id="cinema" name="cinema" class="form-control">
                            <option value="">All cinemas</option>
                            <?php foreach ($cinemas as $item): ?>
                                <option value="<?php echo htmlspecialchars($item['cinema_name']); ?>" <?php echo $cinema === $item['cinema_name'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($item['cinema_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3 d-flex">
                        <button type="submit" class="btn btn-primary btn-block mr-2">Filter</button>
                        <a href="movies.php" class="btn btn-outline-secondary btn-block">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <?php if (empty($movies)): ?>
            <div class="alert alert-warning">No movies match your search and filters. Try removing some filters or search terms.</div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($movies as $movie): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="movie-card shadow-sm h-100">
                            <div class="movie-card-image">
                                <img src="<?php echo htmlspecialchars($movie['poster_url'] ?: 'img/default-movie.jpg'); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?> poster">
                            </div>
                            <div class="movie-card-body">
                                <h3 class="movie-card-title"><?php echo htmlspecialchars($movie['title']); ?></h3>
                                <div class="movie-card-meta mb-3">
                                    <span class="badge badge-pill badge-primary"><?php echo htmlspecialchars($movie['rating']); ?></span>
                                    <span class="badge badge-pill badge-secondary"><?php echo htmlspecialchars($movie['language']); ?></span>
                                    <span class="badge badge-pill badge-info"><?php echo intval($movie['duration_min']); ?> min</span>
                                </div>
                                <p class="movie-card-description mb-3"><?php echo htmlspecialchars(substr($movie['description'], 0, 115)); ?><?php echo strlen($movie['description']) > 115 ? '...' : ''; ?></p>
                                <div class="movie-card-tags mb-3">
                                    <strong>Genres:</strong> <?php echo htmlspecialchars($movie['genres']); ?>
                                </div>
                                <div class="movie-card-availability mb-3">
                                    <small><strong>Cities:</strong> <?php echo htmlspecialchars($movie['available_cities'] ?: 'All'); ?></small><br>
                                    <small><strong>Cinemas:</strong> <?php echo htmlspecialchars($movie['available_cinemas'] ?: 'All'); ?></small>
                                </div>
                                <a href="movie-details.php?id=<?php echo $movie['movie_id']; ?>" class="btn btn-block btn-outline-primary">View Movie & Book</a>
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