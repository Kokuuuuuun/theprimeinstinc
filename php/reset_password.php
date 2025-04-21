<?php
session_start();
require_once 'conexion.php';

$mensaje = '';
$error = '';
$token_valid = false;
$token = isset($_GET['token']) ? $_GET['token'] : '';

// Verificar si el token existe y es válido
if (!empty($token)) {
    try {
        // Verificar el token en la base de datos
        $current_time = date('Y-m-d H:i:s');

        $sql = "SELECT pr.id, pr.user_id, pr.expiry, u.nombre, u.correo
                FROM password_reset pr
                JOIN usuario u ON pr.user_id = u.id
                WHERE pr.token = ? AND pr.expiry > ? AND pr.used = 0";

        $stmt = $connetion->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta: " . $connetion->error);
        }

        $stmt->bind_param("ss", $token, $current_time);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $reset_id = $row['id'];
            $user_id = $row['user_id'];
            $username = $row['nombre'];
            $email = $row['correo'];
            $token_valid = true;
        } else {
            $error = "El enlace de restablecimiento no es válido o ha expirado.";
        }

        // Liberar el resultado
        $result->free();
        $stmt->close();

    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
} else {
    $error = "Token no proporcionado.";
}

// Procesar el formulario de restablecimiento de contraseña
if ($_SERVER["REQUEST_METHOD"] == "POST" && $token_valid) {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validar la contraseña
    if (strlen($password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } else {
        try {
            // Hash de la nueva contraseña
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Actualizar la contraseña del usuario
            $sql = "UPDATE usuario SET contraseña = ? WHERE id = ?";
            $stmt = $connetion->prepare($sql);

            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $connetion->error);
            }

            $stmt->bind_param("si", $hashed_password, $user_id);

            if ($stmt->execute()) {
                // Cerrar el statement antes de ejecutar otra consulta
                $stmt->close();

                // Marcar el token como usado
                $sql = "UPDATE password_reset SET used = 1 WHERE id = ?";
                $update_stmt = $connetion->prepare($sql);

                if (!$update_stmt) {
                    throw new Exception("Error en la preparación de la consulta: " . $connetion->error);
                }

                $update_stmt->bind_param("i", $reset_id);
                $update_stmt->execute();
                $update_stmt->close();

                $mensaje = "¡Tu contraseña ha sido restablecida correctamente! Ahora puedes iniciar sesión con tu nueva contraseña.";
                $token_valid = false; // Ya no mostrar el formulario
            } else {
                $error = "Error al actualizar la contraseña: " . $stmt->error;
                $stmt->close();
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../src/css/login-admin.css">
    <title>Restablecer Contraseña</title>
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
    </style>
</head>
<body>
    <div class="container-all">
        <div class="container-img">
            <img class="theimg" src="../img/loginimg.jpg" alt="limg">
        </div>
        <div class="container">
            <h1>Restablecer Contraseña</h1>

            <?php if (!empty($mensaje)): ?>
            <div class="message success">
                <?php echo $mensaje; ?>
                <p><a href="login-admin.php">Ir al inicio de sesión</a></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
            <div class="message error">
                <?php echo $error; ?>
                <?php if ($error == "El enlace de restablecimiento no es válido o ha expirado." || $error == "Token no proporcionado."): ?>
                <p><a href="recuperar_password.php">Solicitar un nuevo enlace</a></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if ($token_valid): ?>
            <form action="?token=<?php echo htmlspecialchars($token); ?>" method="POST" onsubmit="return validateForm()">
                <p>Hola <?php echo htmlspecialchars($username); ?>, ingresa tu nueva contraseña:</p>
                <input class="input-w" type="password" id="password" name="password" placeholder="Nueva contraseña" minlength="6" required>
                <input class="input-w" type="password" id="confirm_password" name="confirm_password" placeholder="Confirmar contraseña" required>
                <input class="l-button" type="submit" value="Cambiar contraseña">
            </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function validateForm() {
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');

        if (password.value.length < 6) {
            alert('La contraseña debe tener al menos 6 caracteres');
            password.focus();
            return false;
        }

        if (password.value !== confirmPassword.value) {
            alert('Las contraseñas no coinciden');
            confirmPassword.focus();
            return false;
        }

        return true;
    }
    </script>
</body>
</html>
