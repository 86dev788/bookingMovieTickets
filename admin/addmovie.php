<?php
include "config.php";

// Check user login or not
if (!isset($_SESSION['uname'])) {
    header('Location: index.php');
}

// logout
if (isset($_POST['but_logout'])) {
    session_destroy();
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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
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
        .movie-form .form-control-file {
            width: 100% !important;
            padding: 0.85rem 0.95rem !important;
            border: 1px solid #d7dce8 !important;
            border-radius: 0.75rem !important;
            background: #fcfdff !important;
            color: #1f2a48 !important;
        }
        .movie-form .form-row {
            margin-right: -0.5rem;
            margin-left: -0.5rem;
        }
        .movie-form .form-row .form-group {
            padding-right: 0.5rem;
            padding-left: 0.5rem;
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
    <?php
    $sql = "SELECT * FROM booking";
    $bookingsNo = mysqli_num_rows(mysqli_query($con, $sql));
    $messagesNo = mysqli_num_rows(mysqli_query($con, "SELECT * FROM feedbacktable"));
    $moviesNo = mysqli_num_rows(mysqli_query($con, "SELECT * FROM movie"));
    ?>
    
    <?php include('header.php'); ?>

    <div class="admin-container">

        <?php include('sidebar.php'); ?>
        <div class="admin-section admin-section2">
            <div class="admin-section-column">


                <div class="admin-section-panel admin-section-panel2">
                    <div class="admin-panel-section-header">
                        <h2>Movies</h2>
                        <i class="fas fa-film" style="background-color: #4547cf"></i>
                    </div>
                    <div class="card shadow-sm mb-4 movie-form-card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Add New Movie</h4>
                            <form action="" method="POST" class="movie-form">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="movieTitle">Title</label>
                                        <input id="movieTitle" class="form-control" placeholder="Enter movie title" type="text" name="title" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="movieDuration">Duration (min)</label>
                                        <input id="movieDuration" class="form-control" placeholder="120" type="number" name="duration_min" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="language">Language</label>
                                        <input id="language" class="form-control" placeholder="English" type="text" name="language" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="rating">Rating</label>
                                        <input id="rating" class="form-control" placeholder="PG-13" type="text" name="rating" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="movieGenres">Genres</label>
                                        <input id="movieGenres" class="form-control" placeholder="Action, Drama" type="text" name="genres" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="movieRelDate">Release Date</label>
                                        <input id="movieRelDate" class="form-control" type="date" name="release_date" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-12">
                                        <label for="posterUrl">Poster URL</label>
                                        <input id="posterUrl" class="form-control" placeholder="img/movie-poster-1.jpg" type="text" name="poster_url">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-12">
                                        <label for="movieDescription">Description</label>
                                        <textarea id="movieDescription" class="form-control" name="description" rows="4" placeholder="Enter movie description"></textarea>
                                    </div>
                                </div>
                                <button type="submit" value="submit" name="submit" class="btn btn-primary btn-block">Add Movie</button>
                            </form>
                        </div>
                    </div>
                    <?php
                    if (isset($_POST['submit'])) {
                        $title = mysqli_real_escape_string($con, $_POST['title']);
                        $duration_min = intval($_POST['duration_min']);
                        $language = mysqli_real_escape_string($con, $_POST['language']);
                        $rating = mysqli_real_escape_string($con, $_POST['rating']);
                        $genres = mysqli_real_escape_string($con, $_POST['genres']);
                        $release_date = mysqli_real_escape_string($con, $_POST['release_date']);
                        $poster_url = mysqli_real_escape_string($con, trim($_POST['poster_url'] ?? ''));
                        $description = mysqli_real_escape_string($con, trim($_POST['description'] ?? ''));

                        $insert_query = "INSERT INTO movie (title, duration_min, language, rating, genres, poster_url, release_date, description)
                                         VALUES ('$title', $duration_min, '$language', '$rating', '$genres', '$poster_url', '$release_date', '$description')";

                        if (mysqli_query($con, $insert_query)) {
                            echo "<script>alert('Movie added successfully'); window.location.href='addmovie.php';</script>";
                        } else {
                            echo "<div class=\"alert alert-danger\">Error adding movie: " . htmlspecialchars(mysqli_error($con)) . "</div>";
                        }
                    }
                    ?>
                </div>
            </div>

        </div>
    </div>

    <script src="../scripts/jquery-3.3.1.min.js "></script>
    <script src="../scripts/owl.carousel.min.js "></script>
    <script src="../scripts/script.js "></script>
</body>

</html>