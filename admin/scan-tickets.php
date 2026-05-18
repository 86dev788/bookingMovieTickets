<?php
session_start();
include 'config.php';

if (!isset($_SESSION['uname'])) {
    header('Location: index.php');
    exit;
}

$message = '';
$error = '';
$ticket_data = null;

// Handle QR code submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qr_code'])) {
    $qr_code = trim($_POST['qr_code']);
    
    if (empty($qr_code)) {
        $error = 'Please enter or scan a QR code.';
    } else {
        // Find ticket by QR code
        $stmt = $con->prepare("
            SELECT t.ticket_id, t.qr_code, t.is_used, t.booking_id, t.seat_id, 
                   s.seat_number, b.show_id, m.title, c.cinema_name, sc.screen_name, 
                   ms.show_time, cu.name as customer_name
            FROM ticket t
            JOIN seat s ON t.seat_id = s.seat_id
            JOIN booking b ON t.booking_id = b.booking_id
            JOIN movie_show ms ON b.show_id = ms.show_id
            JOIN movie m ON ms.movie_id = m.movie_id
            JOIN screen sc ON ms.screen_id = sc.screen_id
            JOIN cinema c ON sc.cinema_id = c.cinema_id
            JOIN customer cu ON b.customer_id = cu.customer_id
            WHERE t.qr_code = ?
        ");
        $stmt->bind_param('s', $qr_code);
        $stmt->execute();
        $result = $stmt->get_result();
        $ticket_data = $result->fetch_assoc();
        $stmt->close();
        
        if (!$ticket_data) {
            $error = 'Invalid QR code. Ticket not found.';
        } elseif ($ticket_data['is_used'] == 1) {
            $error = 'This ticket has already been used for entry.';
            $ticket_data = null;
        } else {
            // Mark ticket as used
            $stmt = $con->prepare("UPDATE ticket SET is_used = 1 WHERE ticket_id = ?");
            $stmt->bind_param('i', $ticket_data['ticket_id']);
            if ($stmt->execute()) {
                $message = 'Ticket verified successfully! Entry granted.';
            } else {
                $error = 'Error processing ticket. Please try again.';
            }
            $stmt->close();
        }
    }
}

// Get statistics
$todayTickets = "SELECT COUNT(*) as count FROM ticket WHERE is_used = 1 AND DATE(generated_at) = CURDATE()";
$todayResult = mysqli_query($con, $todayTickets);
$todayData = mysqli_fetch_assoc($todayResult);

$pendingTickets = "SELECT COUNT(*) as count FROM ticket WHERE is_used = 0";
$pendingResult = mysqli_query($con, $pendingTickets);
$pendingData = mysqli_fetch_assoc($pendingResult);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ticket Scanning - Cinema Gate</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link rel="stylesheet" href="../style/styles.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        .qr-scanner-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px;
            border-radius: 10px;
            color: white;
            text-align: center;
            margin: 20px 0;
        }
        .qr-input {
            font-size: 18px;
            text-align: center;
            padding: 15px;
            border: 3px solid #667eea;
        }
        .ticket-info {
            background-color: #f8f9fa;
            border-left: 5px solid #28a745;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
        }
        .stats-box {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stat-item {
            text-align: center;
            padding: 15px;
        }
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <?php include('header.php'); ?>

    <div class="admin-container">
        <?php include('sidebar.php'); ?>
        <div class="container-lg mt-4">
            <div class="bg-white p-4 rounded">
                <h2 class="mb-4">
                    <i class="fas fa-qrcode"></i> Ticket Scanning - Cinema Gate Entry
                </h2>

                <!-- Statistics -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="stats-box">
                            <div class="stat-item">
                                <div class="stat-number"><?php echo $todayData['count'] ?? 0; ?></div>
                                <div class="stat-label">Tickets Used Today</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stats-box">
                            <div class="stat-item">
                                <div class="stat-number"><?php echo $pendingData['count'] ?? 0; ?></div>
                                <div class="stat-label">Pending Tickets</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- QR Scanner -->
                <div class="qr-scanner-box">
                    <h4 class="mb-4">
                        <i class="fas fa-camera"></i> Scan QR Code or Enter Code
                    </h4>
                    <form method="POST" action="">
                        <div class="form-group">
                            <input 
                                type="text" 
                                name="qr_code" 
                                id="qr_code" 
                                class="form-control qr-input" 
                                placeholder="Scan QR code here..."
                                autofocus
                                autocomplete="off">
                        </div>
                        <button type="submit" class="btn btn-light btn-lg mt-2">
                            <i class="fas fa-check"></i> Verify Ticket
                        </button>
                    </form>
                </div>

                <!-- Alert Messages -->
                <?php if ($message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <!-- Ticket Details -->
                <?php if ($ticket_data): ?>
                    <div class="ticket-info">
                        <h5 class="mb-3"><i class="fas fa-check-circle text-success"></i> Ticket Verified</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Movie:</strong> <?php echo htmlspecialchars($ticket_data['title']); ?></p>
                                <p><strong>Cinema:</strong> <?php echo htmlspecialchars($ticket_data['cinema_name']); ?></p>
                                <p><strong>Screen:</strong> <?php echo htmlspecialchars($ticket_data['screen_name']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Show Time:</strong> <?php echo date('d M Y, H:i', strtotime($ticket_data['show_time'])); ?></p>
                                <p><strong>Seat:</strong> <?php echo htmlspecialchars($ticket_data['seat_number']); ?></p>
                                <p><strong>Customer:</strong> <?php echo htmlspecialchars($ticket_data['customer_name']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Instructions -->
                <div class="alert alert-info mt-4">
                    <h5><i class="fas fa-info-circle"></i> How to Use:</h5>
                    <ul class="mb-0">
                        <li>Staff scans the QR code from customer's ticket/phone</li>
                        <li>System verifies ticket validity and shows customer/movie details</li>
                        <li>Once verified, ticket is marked as used for entry</li>
                        <li>Cannot be used again for entry (prevents duplicate entry)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="../scripts/jquery-3.3.1.min.js"></script>
    <script src="../scripts/owl.carousel.min.js"></script>
    <script src="../scripts/script.js"></script>
    <script>
        // Auto-focus input after page loads
        document.getElementById('qr_code').focus();
        
        // Clear input after verification for next scan
        <?php if ($message): ?>
            setTimeout(function() {
                document.getElementById('qr_code').value = '';
                document.getElementById('qr_code').focus();
            }, 2000);
        <?php endif; ?>
    </script>
</body>
</html>
