<?php
// Establecer que se muestren todos los errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Verificación de Conexión a Base de Datos</h1>";

// Verificar si hay archivo .env
if (file_exists(__DIR__ . '/.env')) {
    echo "<p>Archivo .env encontrado.</p>";
    $env = parse_ini_file(__DIR__ . '/.env');
    echo "<p>Variables en .env: " . count($env) . "</p>";
} else {
    echo "<p style='color:red;'>Archivo .env no encontrado.</p>";
}

// Mostrar información de PHP
echo "<h2>Información de PHP</h2>";
echo "<p>Versión de PHP: " . phpversion() . "</p>";
echo "<p>Extensiones cargadas:</p><ul>";
$extensions = get_loaded_extensions();
foreach ($extensions as $extension) {
    echo "<li>$extension</li>";
}
echo "</ul>";

// Intentar conexión con los valores por defecto
echo "<h2>Intentando conexión a la base de datos</h2>";

// Usar variables de entorno o valores por defecto
$host = getenv('DB_HOST') ?: '172.20.1.7';
$usuario = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '1234567890';
$base_datos = getenv('DB_NAME') ?: 'prime';

echo "<p>Host: $host</p>";
echo "<p>Usuario: $usuario</p>";
echo "<p>Base de datos: $base_datos</p>";

try {
    $connection = new mysqli($host, $usuario, $password, $base_datos);

    if ($connection->connect_error) {
        echo "<p style='color:red;'>Error de conexión: " . $connection->connect_error . "</p>";
    } else {
        echo "<p style='color:green;'>¡Conexión exitosa a la base de datos!</p>";

        // Verificar tablas
        $result = $connection->query("SHOW TABLES");
        if ($result) {
            echo "<h3>Tablas en la base de datos:</h3><ul>";
            while ($row = $result->fetch_array()) {
                echo "<li>" . $row[0] . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color:red;'>No se pudieron obtener las tablas: " . $connection->error . "</p>";
        }

        $connection->close();
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>Excepción: " . $e->getMessage() . "</p>";
}

// Verificar permisos de directorios importantes
echo "<h2>Permisos de directorios</h2>";
$dirs_to_check = [
    'uploads' => __DIR__ . '/uploads',
    'logs' => __DIR__ . '/logs',
    'admin/uploads' => __DIR__ . '/admin/uploads'
];

foreach ($dirs_to_check as $name => $dir) {
    echo "<p>Directorio $name: ";
    if (file_exists($dir)) {
        echo "Existe";
        if (is_writable($dir)) {
            echo " y es escribible ✅";
        } else {
            echo " pero NO es escribible ❌";
        }
        echo " (Permisos: " . substr(sprintf('%o', fileperms($dir)), -4) . ")";
    } else {
        echo "No existe ❌";
    }
    echo "</p>";
}
?>
