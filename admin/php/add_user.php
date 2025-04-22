<?php
include("conexion.php");

$nombre = trim($_POST['nombre']);
$correo = trim($_POST['correo']);
$password = trim($_POST['contraseña']);
$es_admin = isset($_POST['es_admin']) ? 1 : 0;

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

if(empty($password)) {
    echo json_encode(['success' => false, 'error' => 'La contraseña es requerida']);
    exit();
}

if(strlen($password) < 6) {
    echo json_encode(['success' => false, 'error' => 'La contraseña debe tener al menos 6 caracteres']);
    exit();
}

// Check if email already exists
$check_email = "SELECT COUNT(*) FROM usuario WHERE correo = ?";
$stmt = mysqli_prepare($connection, $check_email);
mysqli_stmt_bind_param($stmt, "s", $correo);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $count);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if($count > 0) {
    echo json_encode(['success' => false, 'error' => 'El correo ya está registrado']);
    exit();
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert user with the es_admin field
$sql = "INSERT INTO usuario (nombre, correo, contraseña, es_admin) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($connection, $sql);
mysqli_stmt_bind_param($stmt, "sssi", $nombre, $correo, $hashed_password, $es_admin);

$success = mysqli_stmt_execute($stmt);

if($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($connection)]);
}

mysqli_close($connection);
?>
