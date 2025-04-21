<?php
include("conexion.php");

$id = $_GET['id'];
$sql = "DELETE FROM usuario WHERE id = ?";
$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
$success = mysqli_stmt_execute($stmt);

mysqli_close($conexion);

header("Location: usuario-admin.php");
exit();
?>
