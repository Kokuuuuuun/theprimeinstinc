<?php
session_start();
header('Content-Type: application/json');
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $comment = mysqli_real_escape_string($connection, $_POST['comment']);
    $rating = mysqli_real_escape_string($connection, $_POST['rating']);
    $username = $_SESSION['username']; 

    $sql = "INSERT INTO opiniones (comment, rating, username, date) VALUES (?, ?, ?, NOW())";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "sis", $comment, $rating, $username);
    
    if(mysqli_stmt_execute($stmt)) {
        header("Location: opiniones-admin.php");
    } else {
        echo "Error: " . mysqli_error($connection);
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
} else {
    header("Location: opiniones-admin.php");
}
?>