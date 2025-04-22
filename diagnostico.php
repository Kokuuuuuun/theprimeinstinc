<?php
// Configurar para mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <title>Diagnóstico de Conexión a Base de Datos</title>
    <meta name='robots' content='noindex, nofollow'>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 30px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.09);
            padding: 25px 30px 30px 30px;
        }
        h1, h2, h3 {
            color: #333;
        }
        ul {
            margin-left: 20px;
        }
        hr {
            margin: 30px 0;
            border: none;
            border-top: 1px solid #eee;
        }
        p {
            line-height: 1.5;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: #d8000c;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #888;
            font-size: 0.95em;
        }
        code {
            background: #eee;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 1em;
        }
    </style>
</head>
<body>
<div class='container'>
";

echo "<h1>Diagnóstico de Conexión a Base de Datos</h1>";

// Cargar variables de entorno desde .env si existe
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

echo "<h2>Configuración actual:</h2>";
echo "<ul>";
foreach ($db_config as $key => $value) {
    // No mostrar la contraseña real por seguridad
    if ($key === 'pass') {
        echo "<li><b>$key</b>: ********</li>";
    } else {
        echo "<li><b>$key</b>: <code>" . htmlspecialchars($value) . "</code></li>";
    }
}
echo "</ul>";

echo "<h2>Prueba de conexiones múltiples:</h2>";

// Función para probar una conexión
function testConnection($db_config, $num) {
    echo "<h3>Conexión #$num</h3>";

    try {
        $start = microtime(true);

        $conn = new mysqli(
            $db_config['host'],
            $db_config['user'],
            $db_config['pass'],
            $db_config['db'],
            $db_config['port']
        );

        $time = number_format((microtime(true) - $start) * 1000, 2); // en milisegundos

        if ($conn->connect_error) {
            echo "<p class='error'>✗ Error de conexión: " . htmlspecialchars($conn->connect_error) . "</p>";
            return false;
        }

        echo "<p class='success'>✓ Conexión establecida exitosamente (tomó $time ms)</p>";

        // Establecer charset
        if (!$conn->set_charset($db_config['charset'])) {
            echo "<p class='error'>No se pudo establecer el charset '" . htmlspecialchars($db_config['charset']) . "': " . htmlspecialchars($conn->error) . "</p>";
        } else {
            echo "<p>Charset de conexión: <b>" . htmlspecialchars($db_config['charset']) . "</b></p>";
        }

        // Probar una consulta simple
        $query_start = microtime(true);
        $result = $conn->query("SELECT 1 as test");
        $query_time = number_format((microtime(true) - $query_start) * 1000, 2);

        if ($result) {
            echo "<p class='success'>✓ Consulta simple exitosa (tomó $query_time ms)</p>";
            $row = $result->fetch_assoc();
            echo "<p>Resultado: <b>" . htmlspecialchars($row['test']) . "</b></p>";
            $result->free();
        } else {
            echo "<p class='error'>✗ Error en consulta: " . htmlspecialchars($conn->error) . "</p>";
        }

        // Probar una consulta a tabla 'usuario'
        echo "<p>Probando consulta a tabla <b>usuario</b>...</p>";
        $result = $conn->query("SELECT COUNT(*) as total FROM usuario");

        if ($result) {
            $row = $result->fetch_assoc();
            echo "<p>Total usuarios: <b>" . htmlspecialchars($row['total']) . "</b></p>";
            $result->free();
        } else {
            echo "<p class='error'>✗ Error al consultar usuarios: " . htmlspecialchars($conn->error) . "</p>";
        }

        // Probar una transacción (si es posible)
        if ($conn->begin_transaction()) {
            $conn->rollback();
            echo "<p class='success'>✓ Transacciones soportadas</p>";
        } else {
            echo "<p class='error'>✗ No se pudo iniciar una transacción: " . htmlspecialchars($conn->error) . "</p>";
        }

        // Cerrar la conexión explícitamente
        $conn->close();
        echo "<p>Conexión cerrada correctamente</p>";

        return true;
    } catch (Exception $e) {
        echo "<p class='error'>✗ Excepción: " . htmlspecialchars($e->getMessage()) . "</p>";
        return false;
    }
}

// Probar múltiples conexiones para simular concurrencia
for ($i = 1; $i <= 3; $i++) {
    $success = testConnection($db_config, $i);
    echo "<hr>";
}

echo "<h2>Recomendaciones:</h2>";
echo "<ul>";
echo "<li>Siempre cierre las consultas (<code>\$stmt->close()</code> o <code>\$result->free()</code>) antes de cerrar la conexión</li>";
echo "<li>Cierre la conexión (<code>\$connection->close()</code>) al final de cada script</li>";
echo "<li>No comparta una misma conexión entre diferentes peticiones o hilos</li>";
echo "<li>Use transacciones para operaciones complejas</li>";
echo "<li>Asegúrese de obtener todos los resultados antes de ejecutar una nueva consulta</li>";
echo "<li>Verifique que la tabla <b>usuario</b> existe y tiene registros</li>";
echo "<li>Si ve errores de <b>Too many connections</b> o <b>MySQL server has gone away</b>, revise la configuración de <code>max_connections</code> y <code>wait_timeout</code> en su servidor MySQL</li>";
echo "</ul>";

echo "<div class='footer'><a href='/'>Volver a la página principal</a></div>";

echo "
</div>
</body>
</html>
";
?>
