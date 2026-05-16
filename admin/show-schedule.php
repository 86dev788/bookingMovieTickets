<?php
include 'config.php';

if (!isset($_SESSION['uname'])) {
    header('Location: index.php');
    exit;
}

$shows = [];
$query = "SELECT ms.show_id, ms.show_time, ms.price, m.title AS movie_title, sc.screen_name, c.cinema_name, c.city
          FROM movie_show ms
          JOIN movie m ON ms.movie_id = m.movie_id
          JOIN screen sc ON ms.screen_id = sc.screen_id
          JOIN cinema c ON sc.cinema_id = c.cinema_id
          ORDER BY ms.show_time";
if ($result = mysqli_query($con, $query)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $shows[] = $row;
    }
    mysqli_free_result($result);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show Schedule - Admin</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link rel="stylesheet" href="../style/styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include('header.php'); ?>
    <div class="admin-container">
        <?php include('sidebar.php'); ?>
        <div class="admin-section admin-section2">
            <div class="admin-section-column">
                <div class="admin-section-panel admin-section-panel2">
                    <div class="admin-panel-section-header">
                        <h2>Show Schedule</h2>
                        <i class="fas fa-list" style="background-color: #4547cf"></i>
                    </div>
                    <div class="table-responsive mx-4 mb-4">
                        <?php if (empty($shows)): ?>
                            <div class="alert alert-warning">No show schedule found. Please add shows first.</div>
                        <?php else: ?>
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Show ID</th>
                                        <th>Movie</th>
                                        <th>Cinema</th>
                                        <th>Screen</th>
                                        <th>Show Time</th>
                                        <th>Price</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($shows as $show): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($show['show_id']); ?></td>
                                            <td><?php echo htmlspecialchars($show['movie_title']); ?></td>
                                            <td><?php echo htmlspecialchars($show['cinema_name'] . ' (' . $show['city'] . ')'); ?></td>
                                            <td><?php echo htmlspecialchars($show['screen_name']); ?></td>
                                            <td><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($show['show_time']))); ?></td>
                                            <td>PKR <?php echo htmlspecialchars(number_format($show['price'], 2)); ?></td>
                                            <td>
                                                <a href="delete-show.php?id=<?php echo $show['show_id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this show?');">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                    <div class="mx-4 mb-4">
                        <a href="add-show.php" class="btn btn-primary">Add New Show</a>
                        <a href="showmovie.php" class="btn btn-outline-secondary ml-2">View Movies</a>
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
