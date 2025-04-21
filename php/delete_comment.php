<?php
session_start();
include("conexion.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$comment_id = intval($_POST['id']);
$username = $_SESSION['username'];

// Verificar que el comentario pertenece al usuario actual
$check_sql = "SELECT username FROM opiniones WHERE id = ?";
$check_stmt = mysqli_prepare($connection, $check_sql);
mysqli_stmt_bind_param($check_stmt, "i", $comment_id);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_store_result($check_stmt);
mysqli_stmt_bind_result($check_stmt, $comment_username);
mysqli_stmt_fetch($check_stmt);

if ($comment_username !== $username) {
    echo json_encode(['success' => false, 'message' => 'No puedes eliminar comentarios de otros usuarios']);
    exit;
}

// Eliminar el comentario
$delete_sql = "DELETE FROM opiniones WHERE id = ? AND username = ?";
$delete_stmt = mysqli_prepare($connection, $delete_sql);
mysqli_stmt_bind_param($delete_stmt, "is", $comment_id, $username);

if (mysqli_stmt_execute($delete_stmt)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar el comentario']);
}

mysqli_close($connection);
?>