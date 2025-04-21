<?php
// Cargar variables de entorno desde .env si existe
if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    foreach ($env as $key => $value) {
        putenv("$key=$value");
    }
}

// Usar variables de entorno o valores por defecto
$host = getenv('DB_HOST') ?: '10.0.1.6';
$usuario = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: 'ONflEz9QYm64VDg9FdZqjeEQqanwhsxn31u1HTCHlX6dJh3OdPuWSHrA2lHTrXsV';
$base_datos = getenv('DB_NAME') ?: 'prime';

// Intentar conexión a la base de datos
$conexion = new mysqli($host, $usuario, $password, $base_datos); // <-- Usar variables definidas

// Verificar errores de conexión
if ($conexion->connect_error) { // <-- Corregido a $conexion
    $errorLog = __DIR__ . '/../logs/db_error.log';
    
    // Asegurar que el directorio de logs existe
    if (!file_exists(dirname($errorLog))) {
        mkdir(dirname($errorLog), 0755, true);
    }
    
    $errorMessage = date('[Y-m-d H:i:s]') . " Error de conexión: " . $conexion->connect_error . "\n";
    file_put_contents($errorLog, $errorMessage, FILE_APPEND);

    // Redirección en producción
    if (!getenv('DEBUG') || getenv('DEBUG') === 'false') {
        header("Location: /diagnostico.php?error=db");
        exit();
    } else {
        die("Error de conexión: " . $conexion->connect_error);
    }
}

// Establecer charset (versión segura)
if (!$conexion->set_charset("utf8mb4")) { // <-- Corregido a $conexion
    $errorMessage = date('[Y-m-d H:i:s]') . " Error charset: " . $conexion->error . "\n";
    file_put_contents($errorLog, $errorMessage, FILE_APPEND);
}
