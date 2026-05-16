<?php
include "config.php";
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $sql = "DELETE FROM movie WHERE movie_id = $id";
    if ($con->query($sql) === TRUE) {
        header('Location: addmovie.php');
        exit;
    } else {
        echo "Error deleting record: " . htmlspecialchars($con->error);
    }
} else {
    header('Location: addmovie.php');
    exit;
}
?>