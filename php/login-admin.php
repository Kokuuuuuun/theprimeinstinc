<?php
// Session management
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index-admin.php");
    exit();
}

// Display validation error
$error_message = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case '1':
            $error_message = 'Credenciales incorrectas';
            break;
        case '2':
            $error_message = 'Error de conexión';
            break;
        default:
            $error_message = 'Error desconocido';
    }
}

// Database connectivity test
$db_status = '';
try {
    if (!isset($connection)) {
        require_once 'conexion.php';
    }

    if ($connection->connect_error) {
        throw new Exception("Error de conexión: " . $connection->connect_error);
    }

    // Check if the 'usuario' table exists and has data
    $query = "SHOW TABLES LIKE 'usuario'";
    $stmt = $connection->prepare($query);

    if (!$stmt) {
        throw new Exception("Error en preparación: " . $connection->error);
    }

    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $db_status = 'conectado';
    } else {
        $db_status = 'sin tabla usuario';
    }

    $stmt->close();
    $connection->close();

} catch (Exception $e) {
    $db_status = 'error: ' . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Validar conexión
        if ($connection->connect_error) {
            throw new Exception("Error de conexión: " . $connection->connect_error);
        }

        $correo = $_POST['correo'] ?? '';
        $password = $_POST['contraseña'] ?? '';

        // Validar credenciales
        $query = "SELECT id, nombre, contraseña FROM usuario WHERE correo = ? LIMIT 1";
        $stmt = $connection->prepare($query);

        if (!$stmt) {
            throw new Exception("Error en preparación: " . $connection->error);
        }

        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();

            if (password_verify($password, $usuario['contraseña'])) {
                // Login success
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['username'] = $usuario['nombre'];
                $_SESSION['email'] = $correo;

                header("Location: index-admin.php");
                exit();
            }
        }

        // Invalid credentials
        header("Location: login-admin.php?error=1");
        exit();

    } catch (Exception $e) {
        header("Location: login-admin.php?error=2");
        exit();
    } finally {
        // Clean up
        if (isset($stmt)) $stmt->close();
        $connection->close();

    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../src/css/login-admin.css">
    <title>Inicia sesión</title>
</head>
<body>
    <div class="container-all">
        <div class="container-img">
            <img class="theimg" src="../img/loginimg.jpg" alt="limg">
        </div>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="container">
                <h1>Inicia sesión</h1>
                <?php if ($error_message): ?>
                    <p style="color:red;"><?php echo htmlspecialchars($error_message); ?></p>
                <?php endif; ?>
                <input class="input-w" type="text" id="email" name="correo" placeholder="Correo" required>
                <input class="input-w" type="password" id="password" name="contraseña" placeholder="Contraseña" required>
                <input class="l-button" type="submit" value="Inicia sesión">
                <p>¿Olvidaste la contraseña? <a href="recuperar_password.php">Recuperar</a></p>
                <p>¿Aún no tienes una cuenta? <a href="register-admin.php">Regístrate</a></p>
                <p>Estado de la base de datos: <?php echo htmlspecialchars($db_status); ?></p>
            </div>
        </form>
    </div>
    <a href="manual.php" class="manual-btn">Manual de Usuario</a>
</body>
</html>
