<?php
session_start();
require_once("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conexion, $_POST['correo']);
    $password = $_POST['contraseña'];
    
    $sql = "SELECT * FROM usuario WHERE correo = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['contraseña'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $row['correo'];
            $_SESSION['username'] = $row['nombre'];
            
            header("Location: index-admin.php");
            exit();
        }
    }
    
    header("Location: login-admin.php?error=1");
    exit();
} else {
    header("Location: login-admin.php");
}
?>