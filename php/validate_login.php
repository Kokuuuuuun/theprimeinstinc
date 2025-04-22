<?php
session_start();

// Crear una conexión específica para este script en lugar de incluir conexion.php
if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env', false, INI_SCANNER_TYPED);
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

// Parámetros de conexión
$db_config = [
    'host' => $_ENV['DB_HOST'] ?? '172.20.1.7',
    'user' => $_ENV['DB_USER'] ?? 'root',
    'pass' => $_ENV['DB_PASSWORD'] ?? '1234567890',
    'db' => $_ENV['DB_NAME'] ?? 'prime',
    'port' => $_ENV['DB_PORT'] ?? 3306,
    'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4'
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $connection = null;
    $stmt = null;
    $result = null;

    try {
        // Crear una conexión
        $connection = new mysqli(
            $db_config['host'],
            $db_config['user'],
            $db_config['pass'],
            $db_config['db'],
            $db_config['port']
        );

        // Configurar charset
        $connection->set_charset($db_config['charset']);

        if ($connection->connect_error) {
            throw new Exception("Error de conexión: " . $connection->connect_error);
        }

        $email = mysqli_real_escape_string($connection, $_POST['correo']);
        $password = $_POST['contraseña'];

        $sql = "SELECT * FROM usuario WHERE correo = ?";
        $stmt = mysqli_prepare($connection, $sql);

        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . mysqli_error($connection));
        }

        mysqli_stmt_bind_param($stmt, "s", $email);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al ejecutar la consulta: " . mysqli_stmt_error($stmt));
        }

        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['contraseña'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['email'] = $row['correo'];
                $_SESSION['username'] = $row['nombre'];

                // Cerrar el statement y la conexión antes de redirigir
                if ($stmt) mysqli_stmt_close($stmt);
                if ($result) mysqli_free_result($result);
                if ($connection) mysqli_close($connection);

                header("Location: index-admin.php");
                exit();
            }
        }

        // Cerrar el statement y la conexión antes de redirigir
        if ($stmt) mysqli_stmt_close($stmt);
        if ($result) mysqli_free_result($result);
        if ($connection) mysqli_close($connection);

        header("Location: login-admin.php?error=1");
        exit();
    } catch (Exception $e) {
        // Registrar el error
        error_log("Error en login: " . $e->getMessage(), 3, __DIR__ . '/../logs/db_errors.log');

        // Asegurarse de cerrar recursos si están abiertos
        if (isset($stmt) && $stmt) mysqli_stmt_close($stmt);
        if (isset($result) && $result) mysqli_free_result($result);
        if (isset($connection) && $connection) mysqli_close($connection);

        header("Location: login-admin.php?error=2");
        exit();
    }
} else {
    header("Location: login-admin.php");
    exit();
}
