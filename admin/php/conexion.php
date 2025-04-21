<?php
// conexion.php

// ===================================================
// Configuración de entorno
// ===================================================
define('ENV_PATH', realpath(__DIR__ . '/../.env'));

// Cargar variables de entorno
if (file_exists(ENV_PATH)) {
    $env = parse_ini_file(ENV_PATH, false, INI_SCANNER_TYPED);

    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

// ===================================================
// Parámetros de conexión
// ===================================================
$db_config = [
    'host' => $_ENV['DB_HOST'] ?? '10.0.1.3',
    'user' => $_ENV['DB_USER'] ?? 'root',
    'pass' => $_ENV['DB_PASSWORD'] ?? 'ONflEz9QYm64VDg9FdZqjeEQqanwhsxn31u1HTCHlX6dJh3OdPuWSHrA2lHTrXsV',
    'db' => $_ENV['DB_NAME'] ?? 'prime',
    'port' => $_ENV['DB_PORT'] ?? 3306,
    'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4'
];

// ===================================================
// Establecer conexión
// ===================================================
try {
    $connetion = new mysqli(
        $db_config['host'],
        $db_config['user'],
        $db_config['pass'],
        $db_config['db'],
        $db_config['port']
    );

    if ($connetion->connect_errno) {
        throw new RuntimeException("Error de conexión MySQL: " . $connetion->connect_error);
    }

    // Configurar charset
    if (!$connetion->set_charset($db_config['charset'])) {
        throw new RuntimeException("Error configurando charset: " . $connetion->error);
    }

} catch (RuntimeException $e) {
    // Manejo centralizado de errores
    $error_message = date('[Y-m-d H:i:s]') . " " . $e->getMessage();

    // Registrar en archivo de log
    error_log($error_message . PHP_EOL, 3, __DIR__ . '/../logs/db_errors.log');

    // Redirección segura en producción
    if (!isset($_ENV['APP_DEBUG']) || $_ENV['APP_DEBUG'] !== 'true') {
        header('HTTP/1.1 500 Internal Server Error');
        header("Location: /diagnostico.php?error=db_admin");
        exit;
    } else {
        die("<h2>Error de Desarrollo</h2><pre>{$e->getMessage()}</pre>");
    }
}
