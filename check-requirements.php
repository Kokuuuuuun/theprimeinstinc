<?php
/**
 * Script para verificar requisitos del sistema para Prime Instinct
 * Este archivo se puede ejecutar desde línea de comandos o desde el navegador
 */

$isCli = (php_sapi_name() === 'cli');

// Función para mostrar mensajes según el entorno
function output($message, $type = 'info') {
    global $isCli;

    if ($isCli) {
        switch ($type) {
            case 'error':
                echo "\033[31m✗ $message\033[0m\n";
                break;
            case 'success':
                echo "\033[32m✓ $message\033[0m\n";
                break;
            case 'warning':
                echo "\033[33m⚠ $message\033[0m\n";
                break;
            default:
                echo "ℹ $message\n";
                break;
        }
    } else {
        $color = '';
        $icon = '';

        switch ($type) {
            case 'error':
                $color = 'red';
                $icon = '✗';
                break;
            case 'success':
                $color = 'green';
                $icon = '✓';
                break;
            case 'warning':
                $color = 'orange';
                $icon = '⚠';
                break;
            default:
                $color = 'blue';
                $icon = 'ℹ';
                break;
        }

        echo "<div style='color: $color; margin: 5px 0;'>$icon $message</div>";
    }
}

// Encabezado
if (!$isCli) {
    echo "<!DOCTYPE html><html><head><title>Verificación de requisitos - Prime Instinct</title>";
    echo "<style>body { font-family: Arial, sans-serif; margin: 20px; }</style></head><body>";
    echo "<h1>Verificación de requisitos - Prime Instinct</h1>";
} else {
    echo "=== Verificación de requisitos - Prime Instinct ===\n\n";
}

// Verificar versión de PHP
$requiredPhpVersion = '7.4.0';
if (version_compare(PHP_VERSION, $requiredPhpVersion, '>=')) {
    output("Versión de PHP: " . PHP_VERSION, 'success');
} else {
    output("Versión de PHP: " . PHP_VERSION . " (se requiere $requiredPhpVersion o superior)", 'error');
}

// Verificar extensiones requeridas
$requiredExtensions = [
    'mysqli' => 'Base de datos MySQL',
    'pdo_mysql' => 'PDO MySQL',
    'gd' => 'Procesamiento de imágenes',
    'zip' => 'Manejo de archivos ZIP',
    'json' => 'Soporte JSON',
    'curl' => 'Soporte cURL para API',
    'mbstring' => 'Soporte multibyte strings'
];

foreach ($requiredExtensions as $ext => $description) {
    if (extension_loaded($ext)) {
        output("Extensión $ext: Instalada ($description)", 'success');
    } else {
        output("Extensión $ext: No instalada ($description)", 'error');
    }
}

// Verificar permisos de directorios
$directories = [
    __DIR__ . '/uploads' => 'Directorio de uploads',
    __DIR__ . '/admin/uploads' => 'Directorio de uploads de administración',
    __DIR__ . '/logs' => 'Directorio de logs'
];

foreach ($directories as $dir => $description) {
    if (file_exists($dir)) {
        if (is_writable($dir)) {
            output("$description: Existe y tiene permisos de escritura", 'success');
        } else {
            output("$description: Existe pero NO tiene permisos de escritura", 'error');
        }
    } else {
        output("$description: No existe", 'error');
    }
}

// Verificar variables de entorno
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    output("Archivo .env: Existe", 'success');

    // Intentar leer variables clave
    $env = parse_ini_file($envFile);
    $requiredEnvVars = ['DB_HOST', 'DB_USER', 'DB_PASSWORD', 'DB_NAME'];

    foreach ($requiredEnvVars as $var) {
        if (isset($env[$var]) && !empty($env[$var])) {
            $value = $var === 'DB_PASSWORD' ? '[oculto]' : $env[$var];
            output("Variable $var: Configurada ($value)", 'success');
        } else {
            output("Variable $var: No configurada o vacía", 'warning');
        }
    }
} else {
    output("Archivo .env: No existe", 'warning');
}

// Verificar conexión a la base de datos
try {
    $host = getenv('DB_HOST') ?: (isset($env['DB_HOST']) ? $env['DB_HOST'] : 'localhost');
    $user = getenv('DB_USER') ?: (isset($env['DB_USER']) ? $env['DB_USER'] : 'root');
    $pass = getenv('DB_PASSWORD') ?: (isset($env['DB_PASSWORD']) ? $env['DB_PASSWORD'] : '');
    $dbname = getenv('DB_NAME') ?: (isset($env['DB_NAME']) ? $env['DB_NAME'] : 'prime');

    $mysqli = new mysqli($host, $user, $pass, $dbname);

    if ($mysqli->connect_error) {
        throw new Exception($mysqli->connect_error);
    }

    output("Conexión a la base de datos: Exitosa ($host/$dbname)", 'success');

    // Verificar tablas
    $result = $mysqli->query("SHOW TABLES");
    if ($result) {
        $tables = [];
        while ($row = $result->fetch_array()) {
            $tables[] = $row[0];
        }

        output("Tablas en la base de datos: " . count($tables) . " tablas encontradas", 'success');
    }

    $mysqli->close();
} catch (Exception $e) {
    output("Conexión a la base de datos: Error - " . $e->getMessage(), 'error');
}

// Verificar si se está ejecutando en Coolify
$isCoolify = false;
if (getenv('COOLIFY_APP_ID') || getenv('COOLIFY') || file_exists('/.coolify')) {
    $isCoolify = true;
    output("Entorno: Coolify detectado", 'success');
} else {
    output("Entorno: Coolify no detectado", 'info');
}

// Verificar permisos de archivos clave
$keyFiles = [
    __DIR__ . '/index.php' => 'Archivo index.php principal',
    __DIR__ . '/coolify-init.sh' => 'Script de inicialización',
    __DIR__ . '/.htaccess' => 'Archivo .htaccess'
];

foreach ($keyFiles as $file => $description) {
    if (file_exists($file)) {
        $perms = substr(sprintf('%o', fileperms($file)), -4);
        if (is_readable($file)) {
            output("$description: Existe y es legible (permisos: $perms)", 'success');
        } else {
            output("$description: Existe pero NO es legible (permisos: $perms)", 'error');
        }
    } else {
        output("$description: No existe", 'warning');
    }
}

// Mostrar información adicional del sistema
output("\nInformación adicional del sistema:", 'info');
output("Sistema operativo: " . PHP_OS, 'info');
output("Servidor web: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido'), 'info');
output("Límite de memoria PHP: " . ini_get('memory_limit'), 'info');
output("Límite de tiempo de ejecución: " . ini_get('max_execution_time') . " segundos", 'info');
output("Tamaño máximo de subida: " . ini_get('upload_max_filesize'), 'info');
output("Tamaño máximo de POST: " . ini_get('post_max_size'), 'info');

// Pie de página
if (!$isCli) {
    echo "</body></html>";
}
?>
