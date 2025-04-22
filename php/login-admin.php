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

// Database connectivity test - use its own connection
$db_status = '';
$test_connection = null;
try {
    // Crear una conexión específica para esta prueba
    if (file_exists(__DIR__ . '/../.env')) {
        $env = parse_ini_file(__DIR__ . '/../.env', false, INI_SCANNER_TYPED);
        foreach ($env as $key => $value) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }

    $db_config = [
        'host' => $_ENV['DB_HOST'] ?? '172.20.1.7',
        'user' => $_ENV['DB_USER'] ?? 'root',
        'pass' => $_ENV['DB_PASSWORD'] ?? '1234567890',
        'db' => $_ENV['DB_NAME'] ?? 'prime',
        'port' => $_ENV['DB_PORT'] ?? 3306
    ];

    $test_connection = new mysqli(
        $db_config['host'],
        $db_config['user'],
        $db_config['pass'],
        $db_config['db'],
        $db_config['port']
    );

    if ($test_connection->connect_error) {
        throw new Exception("Error de conexión: " . $test_connection->connect_error);
    }

    // Check if the 'usuario' table exists and has data
    $result = $test_connection->query("SHOW TABLES LIKE 'usuario'");

    if ($result && $result->num_rows > 0) {
        $db_status = 'conectado';
        $result->free();
    } else {
        $db_status = 'sin tabla usuario';
    }

    // Cerrar esta conexión después de usarla
    $test_connection->close();

} catch (Exception $e) {
    $db_status = 'error: ' . $e->getMessage();
    // Intentar cerrar la conexión si existe
    if ($test_connection && !$test_connection->connect_error) {
        $test_connection->close();
    }
}

// Procesar el formulario de login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_connection = null;
    $stmt = null;
    $result = null;

    try {
        // Crear una nueva conexión específica para el login
        if (file_exists(__DIR__ . '/../.env')) {
            $env = parse_ini_file(__DIR__ . '/../.env', false, INI_SCANNER_TYPED);
            foreach ($env as $key => $value) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }

        $db_config = [
            'host' => $_ENV['DB_HOST'] ?? '172.20.1.7',
            'user' => $_ENV['DB_USER'] ?? 'root',
            'pass' => $_ENV['DB_PASSWORD'] ?? '1234567890',
            'db' => $_ENV['DB_NAME'] ?? 'prime',
            'port' => $_ENV['DB_PORT'] ?? 3306
        ];

        $login_connection = new mysqli(
            $db_config['host'],
            $db_config['user'],
            $db_config['pass'],
            $db_config['db'],
            $db_config['port']
        );

        if ($login_connection->connect_error) {
            throw new Exception("Error de conexión: " . $login_connection->connect_error);
        }

        $correo = $_POST['correo'] ?? '';
        $password = $_POST['contraseña'] ?? '';

        // Validar credenciales
        $query = "SELECT id, nombre, contraseña FROM usuario WHERE correo = ? LIMIT 1";
        $stmt = $login_connection->prepare($query);

        if (!$stmt) {
            throw new Exception("Error en preparación: " . $login_connection->error);
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

                // Limpiar recursos
                $result->free();
                $stmt->close();
                $login_connection->close();

                header("Location: index-admin.php");
                exit();
            }
        }

        // Limpiar recursos
        if ($result) {
            $result->free();
        }
        if ($stmt) {
            $stmt->close();
        }
        if ($login_connection) {
            $login_connection->close();
        }

        // Credenciales inválidas
        header("Location: login-admin.php?error=1");
        exit();

    } catch (Exception $e) {
        // Registrar el error
        error_log("Error de login: " . $e->getMessage(), 3, __DIR__ . '/../logs/db_errors.log');

        // Limpiar recursos
        if ($result) {
            $result->free();
        }
        if ($stmt) {
            $stmt->close();
        }
        if ($login_connection) {
            $login_connection->close();
        }

        header("Location: login-admin.php?error=2");
        exit();
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
