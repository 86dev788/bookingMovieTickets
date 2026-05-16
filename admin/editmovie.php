<?php
include "config.php";

// Check user login or not
if (!isset($_SESSION['uname'])) {
    header('Location: index.php');
    exit;
}

// logout
if (isset($_POST['but_logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$movie = null;

if ($id > 0) {
    $query = "SELECT * FROM movie WHERE movie_id = $id";
    $result = mysqli_query($con, $query);
    $movie = mysqli_fetch_assoc($result);
}

if (!$movie) {
    header('Location: showmovie.php');
    exit;
}

// Handle update
if (isset($_POST['update'])) {
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $duration_min = intval($_POST['duration_min']);
    $language = mysqli_real_escape_string($con, $_POST['language']);
    $rating = mysqli_real_escape_string($con, $_POST['rating']);
    $genres = mysqli_real_escape_string($con, $_POST['genres']);
    $release_date = mysqli_real_escape_string($con, $_POST['release_date']);
    $description = mysqli_real_escape_string($con, $_POST['description']);

    $update_query = "UPDATE movie SET 
        title = '$title',
        duration_min = $duration_min,
        language = '$language',
        rating = '$rating',
        genres = '$genres',
        release_date = '$release_date',
        description = '$description'
        WHERE movie_id = $id";

    if (mysqli_query($con, $update_query)) {
        echo "<script>alert('Movie updated successfully'); window.location.href='showmovie.php';</script>";
    } else {
        echo "Error updating movie: " . mysqli_error($con);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Edit Movie</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link rel="stylesheet" href="../style/styles.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
        .movie-form-card {
            max-width: 1100px;
            margin: 0 auto;
            width: 100%;
        }
        .movie-form .card {
            border: none;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,0.12);
        }
        .movie-form .card-body {
            padding: 2rem;
            background: #ffffff;
        }
        .movie-form .card-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1f2a48;
            margin-bottom: 1.5rem;
        }
        .movie-form .form-group {
            margin-bottom: 1.2rem;
        }
        .movie-form label {
            font-weight: 600;
            color: #222;
            display: block;
            margin-bottom: 0.55rem;
            text-align: left;
        }
        .movie-form .form-control {
            width: 100% !important;
            border: 1px solid #d7dce8 !important;
            border-radius: 0.75rem !important;
            padding: 0.95rem 1rem !important;
            background: #fcfdff !important;
            color: #1f2a48 !important;
            box-shadow: none !important;
        }
        .movie-form .form-control:focus {
            border-color: #5d83ff !important;
            box-shadow: 0 0 0 0.2rem rgba(93, 131, 255, 0.15) !important;
        }
        .movie-form .btn-primary {
            min-height: 52px;
            font-size: 1rem;
            border-radius: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            font-weight: 700;
        }
        .movie-form .btn-block {
            width: 100% !important;
        }
        .movie-form .form-group.col-12 {
            margin-bottom: 1.4rem;
        }
        @media (max-width: 991px) {
            .movie-form-card {
                max-width: 100%;
                padding: 0 1rem;
            }
        }
        @media (max-width: 767px) {
            .movie-form .form-row {
                display: block;
            }
            .movie-form .form-row .form-group {
                width: 100% !important;
                padding: 0 !important;
            }
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>

    <div class="admin-container">
        <?php include('sidebar.php'); ?>
        <div class="admin-section admin-section2">
            <div class="admin-section-column">
                <div class="admin-section-panel admin-section-panel2">
                    <div class="admin-panel-section-header">
                        <h2>Edit Movie</h2>
                        <i class="fas fa-edit" style="background-color: #4547cf"></i>
                    </div>
                    <div class="card shadow-sm mb-4 movie-form-card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Edit Movie Details</h4>
                            <form action="" method="POST" class="movie-form">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="title">Title</label>
                                        <input id="title" class="form-control" placeholder="Enter movie title" type="text" name="title" value="<?php echo htmlspecialchars($movie['title']); ?>" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="duration_min">Duration (min)</label>
                                        <input id="duration_min" class="form-control" placeholder="120" type="number" name="duration_min" value="<?php echo htmlspecialchars($movie['duration_min']); ?>" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="language">Language</label>
                                        <input id="language" class="form-control" placeholder="English" type="text" name="language" value="<?php echo htmlspecialchars($movie['language']); ?>" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="rating">Rating</label>
                                        <input id="rating" class="form-control" placeholder="PG-13" type="text" name="rating" value="<?php echo htmlspecialchars($movie['rating']); ?>" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="genres">Genres</label>
                                        <input id="genres" class="form-control" placeholder="Action, Drama" type="text" name="genres" value="<?php echo htmlspecialchars($movie['genres']); ?>" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="release_date">Release Date</label>
                                        <input id="release_date" class="form-control" type="date" name="release_date" value="<?php echo htmlspecialchars($movie['release_date']); ?>" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-12">
                                        <label for="description">Description</label>
                                        <textarea id="description" class="form-control" placeholder="Movie description" name="description" rows="4"><?php echo htmlspecialchars($movie['description']); ?></textarea>
                                    </div>
                                </div>
                                <button type="submit" value="update" name="update" class="btn btn-primary btn-block">Update Movie</button>
                            </form>
                        </div>
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