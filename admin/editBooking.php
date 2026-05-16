<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/styles.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <title>update</title>
</head>

<body>
<?php

include "config.php";

if (!isset($_SESSION['uname'])) {
    header('Location: index.php');
    exit;
}

$bookingId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($bookingId <= 0) {
    header('Location: view.php');
    exit;
}

if (isset($_POST['update'])) {
    $customerId = intval($_POST['customer_id']);
    $customerName = trim($_POST['name']);
    $customerEmail = trim($_POST['email']);
    $customerPhone = trim($_POST['phone']);
    $amount = floatval($_POST['amount']);
    $status = trim($_POST['status']);

    $customerUpdate = $con->prepare("UPDATE customer SET name = ?, email = ?, phone = ? WHERE customer_id = ?");
    $customerUpdate->bind_param('sssi', $customerName, $customerEmail, $customerPhone, $customerId);
    $customerUpdate->execute();
    $customerUpdate->close();

    $bookingUpdate = $con->prepare("UPDATE booking SET total_amount = ?, status = ? WHERE booking_id = ?");
    $bookingUpdate->bind_param('dsi', $amount, $status, $bookingId);
    $bookingUpdate->execute();
    $bookingUpdate->close();

    mysqli_close($con);
    header('Location: view.php');
    exit;
}

$bookingQuery = "SELECT b.booking_id, b.total_amount, b.status, b.customer_id, c.name AS customer_name, c.email, c.phone,
    m.title AS movie_title, s.show_time
    FROM booking b
    JOIN customer c ON b.customer_id = c.customer_id
    JOIN movie_show s ON b.show_id = s.show_id
    JOIN movie m ON s.movie_id = m.movie_id
    WHERE b.booking_id = ?";
$bookingStmt = $con->prepare($bookingQuery);
$bookingStmt->bind_param('i', $bookingId);
$bookingStmt->execute();
$result = $bookingStmt->get_result();
$data = $result->fetch_assoc();
$bookingStmt->close();

if (!$data) {
    header('Location: view.php');
    exit;
}
?>

    <?php include('header.php'); ?>

    <div class="admin-container">
        <?php include('sidebar.php'); ?>
        <div class="admin-section admin-section2">
            <div class="admin-section-column">


                <div class="admin-section-panel admin-section-panel2">
                    <div class="admin-panel-section-header">
                        <h2>UPDATE DATA</h2>
                        <i class="fas fa-film" style="background-color: #4547cf"></i>
                    </div>
                    <div class="booking-form-container">
                        <form method="POST">
                            <input type="hidden" name="customer_id" value="<?php echo htmlspecialchars($data['customer_id']); ?>">
                            <div class="mb-3"><input class="form-control" type="text" name="name" value="<?php echo htmlspecialchars($data['customer_name']); ?>" placeholder="Customer Name" required></div>
                            <div class="mb-3"><input class="form-control" type="email" name="email" value="<?php echo htmlspecialchars($data['email']); ?>" placeholder="Customer Email" required></div>
                            <div class="mb-3"><input class="form-control" type="text" name="phone" value="<?php echo htmlspecialchars($data['phone']); ?>" placeholder="Customer Phone" required></div>
                            <div class="mb-3"><input class="form-control" type="text" value="<?php echo htmlspecialchars($data['movie_title']); ?>" disabled></div>
                            <div class="mb-3"><input class="form-control" type="text" value="<?php echo htmlspecialchars(date('d M Y H:i', strtotime($data['show_time']))); ?>" disabled></div>
                            <div class="mb-3"><input class="form-control" type="number" step="0.01" name="amount" value="<?php echo htmlspecialchars($data['total_amount']); ?>" placeholder="Enter Amount" required></div>
                            <div class="mb-3">
                                <select name="status" class="form-select" required>
                                    <option value="Pending" <?php echo $data['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Confirmed" <?php echo $data['status'] === 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="Cancelled" <?php echo $data['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            <input type="submit" name="update" class="btn btn-primary" value="Update">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    

    
</body>

</html>