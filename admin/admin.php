<?php
include "config.php";

// Check user login or not
if (!isset($_SESSION['uname'])) {
    header('Location: index.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Dashboard</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link rel="stylesheet" href="../style/styles.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <!-- Admin-level Bootstrap loaded via includes/header.php when on /admin/ -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
</head>

<body>
    <?php
    $bookingsNo = mysqli_num_rows(mysqli_query($con, "SELECT * FROM booking"));
    $ticketsNo = mysqli_num_rows(mysqli_query($con, "SELECT * FROM ticket"));
    $paidPaymentsNo = mysqli_num_rows(mysqli_query($con, "SELECT * FROM payment WHERE status = 'Success'"));
    $moviesNo = mysqli_num_rows(mysqli_query($con, "SELECT * FROM movie"));
    $messagesNo = mysqli_num_rows(mysqli_query($con, "SELECT * FROM feedbacktable"));
    $userNo = mysqli_num_rows(mysqli_query($con, "SELECT * FROM users"));
    ?>

    <?php include('header.php'); ?>

    <div class="admin-container">

        <?php include('sidebar.php'); ?>
        <div class="admin-section admin-section2">
            <div class="admin-section-column">
                <div class="admin-section-panel admin-section-stats">
                    <div class="admin-section-stats-panel">
                        <i class="fa fa-ticket-alt" style="background-color: #cf4545"></i>
                        <h2 style="color: #cf4545 !important"><?php echo $bookingsNo ?></h2>
                        <h3>Bookings</h3>
                    </div>
                    <div class="admin-section-stats-panel">
                        <i class="fas fa-film" style="background-color: #4547cf"></i>
                        <h2 style="color: #4547cf !important"><?php echo $moviesNo ?></h2>
                        <h3>Movies</h3>
                    </div>
                    <div class="admin-section-stats-panel">
                        <i class="fas fa-ticket-alt" style="background-color: #f39c12"></i>
                        <h2 style="color: #f39c12 !important"><?php echo $ticketsNo ?></h2>
                        <h3>Tickets Sold</h3>
                    </div>
                    <div class="admin-section-stats-panel">
                        <i class="fas fa-dollar-sign" style="background-color: #27ae60"></i>
                        <h2 style="color: #27ae60 !important"><?php echo $paidPaymentsNo ?></h2>
                        <h3>Paid Payments</h3>
                    </div>
                    <div class="admin-section-stats-panel">
                        <i class="fas fa-users" style="background-color: #000000"></i>
                        <!--<i class="fas fa-ticket-alt"></i>-->
                        <h2 style="color: #bb3c95 !important"><?php echo $userNo ?></h2>
                        <h3>Users</h3>
                    </div>
                    <div class="admin-section-stats-panel" style="border: none">
                        <i class="fas fa-envelope" style="background-color: #3cbb6c"></i>
                        <h2 style="color: #3cbb6c !important"><?php echo $messagesNo ?></h2>
                        <h3>Messages</h3>
                    </div>
                </div>
                <div class="admin-section-panel admin-section-panel1">
                    <div class="admin-panel-section-header">
                        <h2>Recent Live Bookings</h2>
                        <i class="fas fa-ticket-alt" style="background-color: #cf4545"></i>
                    </div>
                    <div class="admin-panel-section-content">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <tr>
                                <th>Booking ID</th>
                                <th>Customer ID</th>
                                <th>Movie</th>
                                <th>Show Time</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Payment</th>
                            </tr>
                            <tbody>
                                <?php

                                $select = "SELECT b.booking_id, b.customer_id, m.title AS movie_title, s.show_time, b.status, b.total_amount, p.status AS payment_status
                                           FROM booking b
                                           LEFT JOIN movie_show s ON b.show_id = s.show_id
                                           LEFT JOIN movie m ON s.movie_id = m.movie_id
                                           LEFT JOIN payment p ON p.booking_id = b.booking_id
                                           ORDER BY b.booking_date DESC
                                           LIMIT 10";
                                $run = mysqli_query($con, $select);
                                while ($row = mysqli_fetch_assoc($run)) {
                                    $bookingid = $row['booking_id'];
                                    $customerID = $row['customer_id'];
                                    $movieTitle = $row['movie_title'];
                                    $showTime = $row['show_time'];
                                    $status = $row['status'];
                                    $total = $row['total_amount'];
                                    $paymentStatus = $row['payment_status'] ?? 'Pending';
                                ?>
                                    <tr align="center">
                                        <td><?php echo $bookingid; ?></td>
                                        <td><?php echo $customerID; ?></td>
                                        <td><?php echo htmlspecialchars($movieTitle); ?></td>
                                        <td><?php echo htmlspecialchars($showTime); ?></td>
                                        <td><?php echo htmlspecialchars($status); ?></td>
                                        <td><?php echo htmlspecialchars($total); ?></td>
                                        <td><?php echo htmlspecialchars($paymentStatus); ?></td>
                                    </tr>

                                <?php }
                                ?>
                            </tbody>

                        </table>
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