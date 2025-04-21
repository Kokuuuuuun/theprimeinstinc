<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['correo'];
    $password = $_POST['contraseña']; // No hagas hash aquí

    if (!$conexion) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Primero obtén el usuario por correo
    $query = "SELECT * FROM usuario WHERE correo=?";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        // Verifica la contraseña
        if (password_verify($password, $row['contraseña'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['nombre'];
            $_SESSION['email'] = $row['correo'];

            // Redirigir según el ID de usuario
            if($row['id'] == 1) {
                header("Location: ../admin/index.php");
            } else {
                header("Location: ../index.php");
            }
            exit();
        } else {
            echo "<script>
                alert('Usuario o contraseña incorrectos. Por favor, verifique los datos.');
                window.location = 'login-admin.php';
            </script>";
            exit();
        }
    } else {
        echo "<script>
            alert('Usuario o contraseña incorrectos. Por favor, verifique los datos.');
            window.location = 'login-admin.php';
        </script>";
        exit();
    }
    mysqli_stmt_close($stmt);
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
