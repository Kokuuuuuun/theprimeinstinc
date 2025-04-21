<?php
include("conexion.php");

$id = $_POST['id'];
$sql = "DELETE FROM opiniones WHERE id = ?";
$stmt = mysqli_prepare($connection, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
$success = mysqli_stmt_execute($stmt);

echo json_encode(['success' => $success]);
mysqli_close($connection);
?>