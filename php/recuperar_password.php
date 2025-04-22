<?php
session_start();
require_once 'conexion.php';
require_once 'check_email.php';

$mensaje = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['correo'] ?? '';

    // Verificar que el email existe en la base de datos
    try {
        $sql = "SELECT id, nombre FROM usuario WHERE correo = ?";
        $stmt = $connection->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta: " . $connection->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // El usuario existe, generamos un token temporal
            $row = $result->fetch_assoc();
            $user_id = $row['id'];
            $username = $row['nombre'];

            // Generar token aleatorio
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Expira en 1 hora

            // Cerrar el primer statement antes de ejecutar otro
            $stmt->close();
            $result->free();

            // Almacenar el token en la base de datos
            $sql = "INSERT INTO password_reset (user_id, token, expiry, used) VALUES (?, ?, ?, 0)";
            $stmt = $connection->prepare($sql);

            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $connection->error);
            }

            $stmt->bind_param("iss", $user_id, $token, $expiry);

            if ($stmt->execute()) {
                // URL del enlace de restablecimiento
                $reset_url = "http://" . $_SERVER['HTTP_HOST'] . "/theprimeinstinct/php/reset_password.php?token=" . $token;

                // En un entorno real, aquí enviarías un email
                // Por ahora, solo mostramos el enlace para fines de desarrollo
                $mensaje = "Se ha enviado un enlace de recuperación a tu correo electrónico. <br>
                            <small style='font-size: 0.8em;'>(Para fines de desarrollo: <a href='$reset_url'>$reset_url</a>)</small>";
            } else {
                $error = "Error al generar el token de recuperación: " . $stmt->error;
            }

            // Cerrar el statement
            $stmt->close();
        } else {
            $error = "No existe una cuenta asociada a este correo electrónico.";
            // Cerrar statement y resultado
            $stmt->close();
            $result->free();
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
     <link rel="icon" href="img/black-logo - copia.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../src/css/login-admin.css">
    <title>Recuperar Contraseña</title>
    <style>
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        p{
            width: 400px;
        }
    </style>
</head>
<body>
    <div class="container-all">
        <form action="" method="POST">
            <div class="container">
                <h1>Recuperar Contraseña</h1>

                <?php if (!empty($mensaje)): ?>
                <div class="message success"><?php echo $mensaje; ?></div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                <div class="message error"><?php echo $error; ?></div>
                <?php endif; ?>

                <p>Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</p>
                <input class="input-w" type="email" id="email" name="correo" placeholder="Correo electrónico" required>
                <input class="l-button" type="submit" value="Enviar enlace">
                <p><a href="login-admin.php">Volver al inicio de sesión</a></p>
            </div>
        </form>
        <div class="container-img">
            <img class="theimg" src="../img/loginimg.jpg" alt="limg">
        </div>
    </div>
</body>
</html>
