<?php
session_start();
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Validar conexión
        if ($conexion->connect_error) {
            throw new Exception("Error de conexión: " . $conexion->connect_error);
        }

        // Sanitizar inputs
        $email = filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_EMAIL);
        $password = $_POST['contraseña'];

        // Consulta preparada
        $query = "SELECT id, nombre, contraseña FROM usuario WHERE correo = ? LIMIT 1";
        $stmt = $conexion->prepare($query);
        
        if (!$stmt) {
            throw new Exception("Error en preparación: " . $conexion->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        // Manejar resultados
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $result->free();
            $stmt->close();
            
            if (password_verify($password, $row['contraseña'])) {
                $_SESSION = [
                    'user_id' => $row['id'],
                    'username' => $row['nombre'],
                    'email' => $email,
                    'last_login' => time()
                ];
                
                header("Location: " . ($row['id'] == 1 ? '../admin/index.php' : '../index.php'));
                exit();
            }
        }

        throw new Exception("Credenciales inválidas");

    } catch (Exception $e) {
        if (isset($result)) $result->free();
        if (isset($stmt)) $stmt->close();
        $conexion->close();
        
        echo '<script>
            alert("Error: ' . addslashes($e->getMessage()) . '");
            window.location.href = "login-admin.php";
        </script>';
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../src/css/login-admin.css">
    <title>Inicio de sesión</title>
</head>
<body>
    <div class="container-all">
        <form action="" method="POST">
            <div class="container">
                <h1>Inicia sesión</h1>
                <input class="input-w" type="text" id="email" name="correo" placeholder="Correo">
                <input class="input-w" type="password" id="password" name="contraseña" placeholder="Contraseña">
                <input class="l-button" type="submit" value="Inicia sesión">
                <p>¿Olvidaste tu contraseña? <a href="recuperar_password.php">Recuperar contraseña</a></p>
                <p>¿Aún no tienes una cuenta? <a href="register-admin.php">Regístrate</a></p>
            </div>
        </form>
        <div class="container-img">
            <img class="theimg" src="../img/loginimg.jpg" alt="limg">
        </div>
    </div>
    <a href="manual.php" class="manual-btn">Manual de Usuario</a>
</body>
<script src="../src/js/login-admin.js"></script>
</html>
