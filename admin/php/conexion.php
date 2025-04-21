<?php
// Cargar variables de entorno desde .env si existe
if (file_exists(__DIR__ . '/../../.env')) {
    $env = parse_ini_file(__DIR__ . '/../../.env');
    foreach ($env as $key => $value) {
        putenv("$key=$value");
    }
}

// Usar variables de entorno o valores por defecto para InfinityFree
$host = getenv('DB_HOST') ?: '10.0.1.3';
$usuario = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: 'ONflEz9QYm64VDg9FdZqjeEQqanwhsxn31u1HTCHlX6dJh3OdPuWSHrA2lHTrXsV';
$base_datos = getenv('DB_NAME') ?: 'prime';

// Intentar conexión a la base de datos
$connection = new mysqli($host, $usuario, $password, $base_datos);

// Verificar si hay errores de conexión
if ($connection->connect_error) {
    // Guardar los errores en un archivo de registro
    $errorLog = __DIR__ . '/../../logs/db_error.log';
    $errorMessage = date('[Y-m-d H:i:s]') . " Error de conexión admin: " . $connection->connect_error . "\n";
    file_put_contents($errorLog, $errorMessage, FILE_APPEND);

    // En producción, redirigir a una página amigable
    if (!getenv('DEBUG') || getenv('DEBUG') === 'false') {
        header("Location: /diagnostico.php?error=db_admin");
        exit();
    } else {
        die("Error de conexión admin: " . $connection->connect_error);
    }
}

// Establecer el conjunto de caracteres
$connection->set_charset("utf8mb4");
?>
