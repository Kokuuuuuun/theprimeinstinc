<?php
include("conexion.php");

$id = trim($_POST['id']);
$nombre = trim($_POST['nombre']);
$correo = trim($_POST['correo']);
$password = trim($_POST['password']);

// Server-side validation
if(empty($nombre)) {
    echo json_encode(['success' => false, 'error' => 'El nombre es requerido']);
    exit();
}

if(empty($correo)) {
    echo json_encode(['success' => false, 'error' => 'El correo es requerido']);
    exit();
}

if(!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'El correo no es válido']);
    exit();
}

// Check if email exists for other users
$check_email = "SELECT COUNT(*) FROM usuario WHERE correo = ? AND id != ?";
$stmt = mysqli_prepare($connection, $check_email);
mysqli_stmt_bind_param($stmt, "si", $correo, $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $count);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if($count > 0) {
    echo json_encode(['success' => false, 'error' => 'El correo ya está registrado']);
    exit();
}

if(!empty($password)) {
    if(strlen($password) < 6) {
        echo json_encode(['success' => false, 'error' => 'La contraseña debe tener al menos 6 caracteres']);
        exit();
    }
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE usuario SET nombre = ?, correo = ?, contraseña = ? WHERE id = ?";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $nombre, $correo, $hashed_password, $id);
} else {
    $sql = "UPDATE usuario SET nombre = ?, correo = ? WHERE id = ?";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $nombre, $correo, $id);
}

$success = mysqli_stmt_execute($stmt);

if($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($connection)]);
}

mysqli_close($connection);
?>