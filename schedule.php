<?php
session_start();
include('connection.php');

// Get distinct dates from shows (next 7 days)
$dates_query = "
    SELECT DISTINCT DATE(show_time) as show_date 
    FROM movie_show 
    WHERE show_time >= NOW() 
    ORDER BY show_date ASC 
    LIMIT 7
";
$dates_result = mysqli_query($con, $dates_query);
$dates = [];
while ($row = mysqli_fetch_assoc($dates_result)) {
    $dates[] = $row['show_date'];
}

$selected_date = $_GET['date'] ?? ($dates[0] ?? date('Y-m-d'));

// Get all shows for selected date grouped by cinema and screen
$shows_query = "
    SELECT 
        s.show_id,
        m.movie_id,
        m.title,
        m.duration_min,
        c.cinema_id,
        c.cinema_name,
        sc.screen_id,
        sc.screen_name,
        s.show_time,
        s.price,
        COUNT(DISTINCT t.ticket_id) as booked_seats
    FROM movie_show s
    JOIN movie m ON s.movie_id = m.movie_id
    JOIN screen sc ON s.screen_id = sc.screen_id
    JOIN cinema c ON sc.cinema_id = c.cinema_id
    LEFT JOIN booking b ON s.show_id = b.show_id AND b.status IN ('Pending', 'Confirmed')
    LEFT JOIN ticket t ON b.booking_id = t.booking_id
    WHERE DATE(s.show_time) = ?
    GROUP BY s.show_id
    ORDER BY c.cinema_name, s.show_time
";

$stmt = $con->prepare($shows_query);
$stmt->bind_param('s', $selected_date);
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
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" href="img/logo.png">
    <link rel="stylesheet" href="style/styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <title>Movies Schedule</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        .schedule-section { 
            padding: 60px 20px; 
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
        }
        
        .schedule-section h1 {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 40px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .schedule-dates { 
            display: flex; 
            gap: 12px; 
            margin: 30px 0; 
            overflow-x: auto; 
            padding: 20px 0; 
            scroll-behavior: smooth;
            flex-wrap: nowrap;
            white-space: nowrap;
        }
        
        .schedule-dates::-webkit-scrollbar {
            height: 6px;
        }
        
        .schedule-dates::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }
        
        .schedule-dates::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 10px;
        }

        .schedule-item { 
            padding: 14px 24px; 
            background: rgba(255,255,255,0.1); 
            border: 2px solid rgba(255,255,255,0.2); 
            border-radius: 10px; 
            cursor: pointer; 
            white-space: nowrap;
            transition: all 0.3s ease;
            color: white;
            font-size: 13px;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }
        
        .schedule-item:hover { 
            background: rgba(255,255,255,0.15); 
            border-color: rgba(255,255,255,0.4);
            transform: translateY(-2px);
        }
        
        .schedule-item-selected { 
            background: rgba(255,255,255,0.95); 
            color: #667eea;
            border-color: white;
            font-weight: 700;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .cinema-header { 
            font-size: 22px; 
            font-weight: 700; 
            color: white; 
            margin-top: 40px;
            margin-bottom: 20px;
            border-left: 6px solid white;
            padding-left: 20px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);

                .cinema-section { 
                    margin: 30px 0; 
                }
        
                .shows-container {
                    max-height: 70vh;
                    overflow-y: auto;
                    padding-right: 10px;
                }
        
                .shows-container::-webkit-scrollbar {
                    width: 8px;
                }
        
                .shows-container::-webkit-scrollbar-track {
                    background: rgba(255,255,255,0.1);
                    border-radius: 10px;
                }
        
                .shows-container::-webkit-scrollbar-thumb {
                    background: rgba(255,255,255,0.3);
                    border-radius: 10px;
                }
        
                .shows-container::-webkit-scrollbar-thumb:hover {
                    background: rgba(255,255,255,0.5);
                }
        }

        .show-card {
            background: white;
            border-radius: 16px;
            margin: 12px 0;
            padding: 16px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
            display: grid;
            grid-template-columns: 110px 1fr 110px;
            gap: 16px;
            align-items: center;
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
            min-height: 110px;
        }
        
        .show-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }
        
        .show-card:hover {
            box-shadow: 0 12px 35px rgba(0,0,0,0.16);
            transform: translateY(-2px);
        }

        .show-poster { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            height: 110px;
            width: 110px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 40px;
            text-align: center;
            padding: 12px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            flex-shrink: 0;
        }

        .show-details {
            position: relative;
            z-index: 1;
        }
        
        .show-details h5 { 
            margin: 0 0 8px 0; 
            color: #333; 
            font-size: 16px;
            font-weight: 700;
        }
        
        .show-details p { 
            margin: 5px 0; 
            color: #666; 
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .show-details p strong {
            color: #333;
            font-weight: 600;
        }

        .show-times {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: flex-end;
        }

        .show-time-btn {
            padding: 10px 14px;
            border: 2px solid #667eea;
            background: white;
            color: #667eea;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 700;
            font-size: 11px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            text-align: center;
            min-width: 95px;
            flex-shrink: 0;
        }

        .show-time-btn:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
        }

        .show-time-btn:active {
            transform: translateY(0);
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 18px 24px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .alert-info {
            background: rgba(255,255,255,0.15);
            color: white;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        @media (max-width: 768px) {
            .show-card { 
                grid-template-columns: 1fr; 
                gap: 16px;
                padding: 18px;
            }
            
            .show-poster {
                height: 90px;
                width: 90px;
            }
            
            .show-details h5 {
                font-size: 14px;
            }
            
            .show-times {
                justify-content: center;
            }
            
            .schedule-section h1 {
                font-size: 28px;
                margin-bottom: 20px;
            }
            
            .cinema-header {
                font-size: 18px;
                margin-top: 20px;
            }
        }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>

    <div class="schedule-section">
        <div class="container">
            <h1 class="text-white mb-4">
                <i class="fas fa-film"></i> Movie Schedule
            </h1>

            <!-- Date Selection -->
            <div class="schedule-dates">
                <?php foreach ($dates as $date): ?>
                    <a href="?date=<?php echo $date; ?>" class="schedule-item <?php echo $date === $selected_date ? 'schedule-item-selected' : ''; ?>">
                        <div><?php echo date('M d', strtotime($date)); ?> - <?php echo date('D', strtotime($date)); ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
                        </div>

            <!-- Shows List -->
            <?php if (empty($shows)): ?>
                <div class="alert alert-info text-center mt-5">
                    <i class="fas fa-info-circle"></i> <strong>No shows available</strong> for this date. Please select another date.
                </div>
            <?php else: ?>
                <?php
                // Group shows by cinema
                $grouped_shows = [];
                foreach ($shows as $show) {
                    $cinema_key = $show['cinema_name'];
                    if (!isset($grouped_shows[$cinema_key])) {
                        $grouped_shows[$cinema_key] = [];
                    }
                    $grouped_shows[$cinema_key][] = $show;
                }
                ?>
                
                <?php foreach ($grouped_shows as $cinema_name => $cinema_shows): ?>
                    <div class="cinema-section">
                        <div class="cinema-header">
                            <i class="fas fa-building"></i> <?php echo htmlspecialchars($cinema_name); ?>
                                                <div class="shows-container">
                        </div>
                        
                        <?php foreach ($cinema_shows as $show): ?>
                            <div class="show-card">
                                <!-- Movie Poster -->
                                <div class="show-poster">
                                    <div style="line-height: 1.2;">
                                        <div style="font-size: 32px; margin-bottom: 3px;">🎬</div>
                                        <div style="font-size: 10px; font-weight: 600;"><?php echo htmlspecialchars(substr($show['title'], 0, 12)); ?></div>
                                    </div>
                                </div>

                                <!-- Show Details -->
                                <div class="show-details">
                                    <h5><?php echo htmlspecialchars($show['title']); ?></h5>
                                    <p><i class="fas fa-video"></i> <strong><?php echo htmlspecialchars($show['screen_name']); ?></strong></p>
                                    <p><i class="fas fa-clock"></i> <?php echo $show['duration_min'] ?? '120'; ?> minutes</p>
                                    <p><i class="fas fa-tag"></i> <strong style="color: #667eea; font-size: 16px;">PKR <?php echo number_format($show['price'], 0); ?></strong></p>
                                    <p style="font-size: 12px; color: #999; margin-top: 10px;">
                                        <i class="fas fa-chair"></i> 
                                        <?php 
                                        $total_seats_query = "SELECT COUNT(*) as count FROM seat WHERE screen_id = " . $show['screen_id'];
                                        $total_seats = mysqli_fetch_assoc(mysqli_query($con, $total_seats_query))['count'];
                                        $available = $total_seats - $show['booked_seats'];
                                        echo $available . ' seats available';
                                        ?>
                                    </p>
                                </div>

                                <!-- Book Button -->
                                <div class="show-times">
                                    <?php if (isset($_SESSION['customer_id'])): ?>
                                        <a href="seat-selection.php?show_id=<?php echo $show['show_id']; ?>" class="show-time-btn">
                                            <i class="fas fa-ticket-alt" style="font-size: 16px;"></i> 
                                            <span><?php echo date('H:i', strtotime($show['show_time'])); ?></span>
                                            <span style="font-size: 10px; opacity: 0.8;">BOOK NOW</span>
                                        </a>
                                    <?php else: ?>
                                        <a href="login.php" class="show-time-btn" title="Login to book">
                                            <i class="fas fa-lock" style="font-size: 16px;"></i> 
                                            <span><?php echo date('H:i', strtotime($show['show_time'])); ?></span>
                                            <span style="font-size: 10px; opacity: 0.8;">LOGIN</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>

    <script src="scripts/jquery-3.3.1.min.js"></script>
    <script src="scripts/owl.carousel.min.js"></script>
    <script src="scripts/script.js"></script>
</body>
</html>
