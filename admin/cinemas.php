<?php
include 'config.php';

if (!isset($_SESSION['uname'])) {
    header('Location: index.php');
    exit;
}

$message = '';
// Add cinema
if (isset($_POST['add_cinema'])) {
    $name = mysqli_real_escape_string($con, $_POST['cinema_name']);
    $city = mysqli_real_escape_string($con, $_POST['city']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);

    $sql = "INSERT INTO cinema (cinema_name, city, address, phone) VALUES ('$name', '$city', '$address', '$phone')";
    if (mysqli_query($con, $sql)) {
        $message = 'Cinema added successfully.';
    } else {
        $message = 'Error: ' . mysqli_real_escape_string($con, mysqli_error($con));
    }
}

// Delete cinema
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $sql = "DELETE FROM cinema WHERE cinema_id = $id";
    if (mysqli_query($con, $sql)) {
        header('Location: cinemas.php'); exit;
    } else {
        $message = 'Error deleting cinema.';
    }
}

$cinemas = [];
$res = mysqli_query($con, "SELECT * FROM cinema ORDER BY cinema_name");
while ($row = mysqli_fetch_assoc($res)) { $cinemas[] = $row; }

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Cinemas</title>
    <link rel="stylesheet" href="../style/styles.css">
    <style>.cinema-form {max-width:900px;margin:0 auto}</style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        <div class="admin-section admin-section2">
            <div class="admin-section-column">
                <div class="admin-section-panel admin-section-panel2">
                    <div class="admin-panel-section-header">
                        <h2>Cinemas</h2>
                    </div>
                    <?php if ($message): ?><div class="alert alert-info mx-4"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

                    <div class="card mx-4 mb-4 cinema-form">
                        <div class="card-body">
                            <h5>Add Cinema</h5>
                            <form method="POST">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Cinema Name</label>
                                        <input class="form-control" name="cinema_name" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>City</label>
                                        <input class="form-control" name="city" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Phone</label>
                                        <input class="form-control" name="phone">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <input class="form-control" name="address">
                                </div>
                                <button class="btn btn-primary" name="add_cinema">Save</button>
                            </form>
                        </div>
                    </div>

                    <div class="mx-4">
                        <h5>Existing Cinemas</h5>
                        <table class="table table-sm">
                            <thead><tr><th>Name</th><th>City</th><th>Phone</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php foreach ($cinemas as $c): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($c['cinema_name']); ?></td>
                                        <td><?php echo htmlspecialchars($c['city']); ?></td>
                                        <td><?php echo htmlspecialchars($c['phone']); ?></td>
                                        <td>
                                            <a class="btn btn-sm btn-outline-primary" href="screens.php?cinema_id=<?php echo $c['cinema_id']; ?>">Manage Screens</a>
                                            <a class="btn btn-sm btn-danger" href="cinemas.php?delete_id=<?php echo $c['cinema_id']; ?>" onclick="return confirm('Delete cinema?')">Delete</a>
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
