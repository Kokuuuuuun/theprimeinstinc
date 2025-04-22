<?php
// Script de prueba para la conexión a la base de datos
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Prueba de Conexión a Base de Datos</h1>";

// Cargar variables de entorno
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env', false, INI_SCANNER_TYPED);
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

echo "<h2>Configuración de conexión:</h2>";
echo "<ul>";
foreach ($db_config as $key => $value) {
    if ($key === 'pass') {
        echo "<li>$key: ********</li>";
    } else {
        echo "<li>$key: $value</li>";
    }
}
echo "</ul>";

// Probar una conexión directa
echo "<h2>Prueba de conexión directa</h2>";
try {
    $conn = new mysqli(
        $db_config['host'],
        $db_config['user'],
        $db_config['pass'],
        $db_config['db'],
        $db_config['port']
    );

    if ($conn->connect_error) {
        echo "<p style='color:red;'>Error de conexión: " . htmlspecialchars($conn->connect_error) . "</p>";
    } else {
        echo "<p style='color:green;'>✓ Conexión exitosa</p>";

        // Configurar charset
        if (!$conn->set_charset($db_config['charset'])) {
            echo "<p style='color:red;'>Error al configurar charset: " . htmlspecialchars($conn->error) . "</p>";
        } else {
            echo "<p>Charset configurado: " . htmlspecialchars($conn->character_set_name()) . "</p>";
        }

        // Mostrar tablas
        $result = $conn->query("SHOW TABLES");
        if ($result) {
            echo "<p>Tablas en la base de datos:</p><ul>";
            while ($row = $result->fetch_array()) {
                echo "<li>" . htmlspecialchars($row[0]) . "</li>";
            }
            echo "</ul>";
            $result->free();
        } else {
            echo "<p style='color:red;'>Error al obtener tablas: " . htmlspecialchars($conn->error) . "</p>";
        }

        // Mostrar usuarios
        $result = $conn->query("SELECT id, nombre, correo FROM usuario LIMIT 5");
        if ($result) {
            echo "<p>Usuarios en la base de datos (primeros 5):</p>";
            if ($result->num_rows > 0) {
                echo "<table border='1' cellpadding='5'>";
                echo "<tr><th>ID</th><th>Nombre</th><th>Correo</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['correo']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No hay usuarios registrados.</p>";
            }
            $result->free();
        } else {
            echo "<p style='color:red;'>Error al obtener usuarios: " . htmlspecialchars($conn->error) . "</p>";
        }

        // Cerrar la conexión
        $conn->close();
        echo "<p>Conexión cerrada correctamente</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>Excepción: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>Archivos de logs</h2>";
$log_files = glob(__DIR__ . '/logs/*.log');
if (empty($log_files)) {
    echo "<p>No hay archivos de log disponibles.</p>";
} else {
    echo "<ul>";
    foreach ($log_files as $log_file) {
        $file_name = basename($log_file);
        $file_size = filesize($log_file);
        echo "<li>" . htmlspecialchars($file_name) . " (" . round($file_size / 1024, 2) . " KB)</li>";
    }
    echo "</ul>";
}

echo "<h2>Enlaces útiles</h2>";
echo "<ul>";
echo "<li><a href='php/login-admin.php'>Iniciar sesión</a></li>";
echo "<li><a href='php/register-admin.php'>Registrarse</a></li>";
echo "<li><a href='diagnostico.php'>Diagnóstico completo</a></li>";
echo "</ul>";
?>
