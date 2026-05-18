<?php
include "config.php";

if (!isset($_SESSION['uname'])) {
    header('Location: index.php');
    exit;
}

$reportType = $_GET['type'] ?? 'cinema';
$cinemaId = isset($_GET['cinema_id']) ? intval($_GET['cinema_id']) : 0;
$movieId = isset($_GET['movie_id']) ? intval($_GET['movie_id']) : 0;

// Fetch all cinemas for dropdown
$cinemasQuery = "SELECT cinema_id, cinema_name FROM cinema ORDER BY cinema_name ASC";
$cinemasResult = mysqli_query($con, $cinemasQuery);
$cinemas = [];
while ($row = mysqli_fetch_assoc($cinemasResult)) {
    $cinemas[] = $row;
}

// Fetch all movies for dropdown
$moviesQuery = "SELECT movie_id, title FROM movie ORDER BY title ASC";
$moviesResult = mysqli_query($con, $moviesQuery);
$movies = [];
while ($row = mysqli_fetch_assoc($moviesResult)) {
    $movies[] = $row;
}

$revenueData = [];
$totalRevenue = 0;

if ($reportType === 'cinema' && $cinemaId > 0) {
    // Revenue by cinema
    $query = "SELECT c.cinema_name, COUNT(b.booking_id) AS booking_count, SUM(b.total_amount) AS total_revenue
              FROM booking b
              JOIN movie_show s ON b.show_id = s.show_id
              JOIN screen sc ON s.screen_id = sc.screen_id
              JOIN cinema c ON sc.cinema_id = c.cinema_id
              WHERE c.cinema_id = ? AND b.status IN ('Confirmed', 'Refunded')
              GROUP BY c.cinema_id, c.cinema_name";
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $cinemaId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $revenueData[] = $row;
        $totalRevenue += $row['total_revenue'];
    }
    $stmt->close();
} elseif ($reportType === 'cinema') {
    // All cinemas revenue
    $query = "SELECT c.cinema_id, c.cinema_name, COUNT(b.booking_id) AS booking_count, SUM(b.total_amount) AS total_revenue
              FROM cinema c
              LEFT JOIN screen sc ON c.cinema_id = sc.cinema_id
              LEFT JOIN movie_show s ON sc.screen_id = s.screen_id
              LEFT JOIN booking b ON s.show_id = b.show_id AND b.status IN ('Confirmed', 'Refunded')
              GROUP BY c.cinema_id, c.cinema_name
              ORDER BY total_revenue DESC";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $revenueData[] = $row;
        $totalRevenue += ($row['total_revenue'] ?? 0);
    }
} elseif ($reportType === 'movie' && $movieId > 0) {
    // Revenue by specific movie
    $query = "SELECT m.title, COUNT(b.booking_id) AS booking_count, SUM(b.total_amount) AS total_revenue
              FROM booking b
              JOIN movie_show s ON b.show_id = s.show_id
              JOIN movie m ON s.movie_id = m.movie_id
              WHERE m.movie_id = ? AND b.status IN ('Confirmed', 'Refunded')
              GROUP BY m.movie_id, m.title";
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $movieId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $revenueData[] = $row;
        $totalRevenue += $row['total_revenue'];
    }
    $stmt->close();
} elseif ($reportType === 'movie') {
    // All movies revenue
    $query = "SELECT m.movie_id, m.title, COUNT(b.booking_id) AS booking_count, SUM(b.total_amount) AS total_revenue
              FROM movie m
              LEFT JOIN movie_show s ON m.movie_id = s.movie_id
              LEFT JOIN booking b ON s.show_id = b.show_id AND b.status IN ('Confirmed', 'Refunded')
              GROUP BY m.movie_id, m.title
              ORDER BY total_revenue DESC";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $revenueData[] = $row;
        $totalRevenue += ($row['total_revenue'] ?? 0);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Revenue Reports - Admin</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link rel="stylesheet" href="../style/styles.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
    <?php include('header.php'); ?>

    <div class="admin-container">
        <?php include('sidebar.php'); ?>
        <div class="container-lg mt-4">
            <div class="bg-white p-4 rounded">
                <h2 class="mb-4">Revenue Reports</h2>

               <!-- Filters Row -->
<div class="row align-items-end mb-4">

    <!-- Report Type -->
    <div class="col-md-3">
        <label for="reportType" class="form-label">Report Type</label>

        <select id="reportType" class="form-control" onchange="updateReport()">
            <option value="cinema" <?php echo $reportType === 'cinema' ? 'selected' : ''; ?>>
                By Cinema
            </option>

            <option value="movie" <?php echo $reportType === 'movie' ? 'selected' : ''; ?>>
                By Movie
            </option>
        </select>
    </div>

    <!-- Cinema Filter -->
    <?php if ($reportType === 'cinema'): ?>

    <div class="col-md-5">
        <form method="GET">
            
            <input type="hidden" name="type" value="cinema">

            <label for="cinemaFilter" class="form-label">
                Select Cinema
            </label>

            <div class="d-flex gap-2">

                <select name="cinema_id" id="cinemaFilter" class="form-control">
                    <option value="0">All Cinemas</option>

                    <?php foreach ($cinemas as $cinema): ?>

                        <option value="<?php echo $cinema['cinema_id']; ?>"
                            <?php echo $cinemaId === $cinema['cinema_id'] ? 'selected' : ''; ?>>

                            <?php echo htmlspecialchars($cinema['cinema_name']); ?>

                        </option>

                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn btn-primary">
                    Filter
                </button>

            </div>

        </form>
    </div>

    <?php endif; ?>

    <!-- Movie Filter -->
    <?php if ($reportType === 'movie'): ?>

    <div class="col-md-5">
        <form method="GET">

            <input type="hidden" name="type" value="movie">

            <label for="movieFilter" class="form-label">
                Select Movie
            </label>

            <div class="d-flex gap-2">

                <select name="movie_id" id="movieFilter" class="form-control">

                    <option value="0">All Movies</option>

                    <?php foreach ($movies as $movie): ?>

                        <option value="<?php echo $movie['movie_id']; ?>"
                            <?php echo $movieId === $movie['movie_id'] ? 'selected' : ''; ?>>

                            <?php echo htmlspecialchars($movie['title']); ?>

                        </option>

                    <?php endforeach; ?>

                </select>

                <button type="submit" class="btn btn-primary">
                    Filter
                </button>

            </div>

        </form>
    </div>

    <?php endif; ?>

</div>

                <div class="alert alert-info">
                    <strong>Total Revenue: PKR <?php echo number_format($totalRevenue, 2); ?></strong>
                </div>
                <!-- Selected Filter Revenue Summary -->

<?php
$filteredTotal = 0;

if (!empty($revenueData)) {
    foreach ($revenueData as $item) {
        $filteredTotal += $item['total_revenue'];
    }
}
?>

<div class="row mb-4">

    <!-- Overall Revenue -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">

            <div class="card-body">

                <h6 class="text-muted mb-2">
                    Overall Revenue
                </h6>

                <h2 class="fw-bold text-success mb-0">
                    PKR <?php echo number_format($overallRevenue ?? 0, 2); ?>
                </h2>

            </div>

        </div>
    </div>

    <!-- Filtered Revenue -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">

            <div class="card-body">

                <h6 class="text-muted mb-2">

                    <?php if ($reportType === 'movie'): ?>

                        Selected Movie Revenue

                    <?php else: ?>

                        Selected Cinema Revenue

                    <?php endif; ?>

                </h6>

                <h2 class="fw-bold text-primary mb-0">
                    PKR <?php echo number_format($filteredTotal, 2); ?>
                </h2>

            </div>

        </div>
    </div>

</div>

                <div class="table-responsive mt-4">

    <table class="table table-hover align-middle shadow-sm border rounded overflow-hidden">

        <thead class="bg-dark text-white">
            <tr>
                <th class="py-3 px-3">
                    <?php echo $reportType === 'cinema' ? 'Cinema' : 'Movie'; ?>
                </th>

                <th class="py-3 text-center">
                    Total Bookings
                </th>

                <th class="py-3 text-end pe-4">
                    Revenue (PKR)
                </th>
            </tr>
        </thead>

        <tbody>

            <?php if (!empty($revenueData)): ?>

                <?php foreach ($revenueData as $row): ?>

                    <tr>

                        <!-- Cinema / Movie Name -->
                        <td class="fw-semibold px-3">
                            <?php echo htmlspecialchars(
                                $row[$reportType === 'cinema' ? 'cinema_name' : 'title']
                            ); ?>
                        </td>

                        <!-- Booking Count -->
                        <td class="text-center">
                            <span class="badge bg-primary px-3 py-2">
                                <?php echo $row['booking_count'] ?? 0; ?>
                            </span>
                        </td>

                        <!-- Revenue -->
                        <td class="text-end pe-4 fw-bold text-success">
                            PKR <?php echo number_format($row['total_revenue'] ?? 0, 2); ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="3" class="text-center py-4 text-muted">
                        No revenue data available
                    </td>
                </tr>

            <?php endif; ?>

        </tbody>

    </table>

</div>

<style>
    .table {
    background: #fff;
    border-radius: 12px;
}

.table thead th {
    font-size: 15px;
    letter-spacing: 0.5px;
    border: none;
}

.table tbody tr {
    transition: 0.2s ease;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
    transform: scale(1.002);
}

.badge {
    font-size: 13px;
}
</style>
            </div>
        </div>
    </div>

    <script src="../scripts/jquery-3.3.1.min.js"></script>
    <script src="../scripts/owl.carousel.min.js"></script>
    <script src="../scripts/script.js"></script>
    <script>
        function updateReport() {
            const type = document.getElementById('reportType').value;
            window.location.href = 'reports.php?type=' + type;
        }
    </script>
</body>
</html>
