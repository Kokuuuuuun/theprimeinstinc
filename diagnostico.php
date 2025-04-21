<?php
// Definir contraseña para acceder (cambiar en producción)
$acceso_password = 'prime2025';

// Verificar contraseña
$authorized = false;
if (isset($_POST['password']) && $_POST['password'] === $acceso_password) {
    $authorized = true;
} elseif (isset($_GET['password']) && $_GET['password'] === $acceso_password) {
    $authorized = true;
}

// Permitir acceso automatizado en caso de error
if (isset($_GET['error'])) {
    $authorized = true;
}

// Función para comprobar elementos
function check_item($check, $label, $success_msg = 'OK', $error_msg = 'Error') {
    echo '<tr>';
    echo '<td>' . $label . '</td>';
    if ($check) {
        echo '<td class="success">' . $success_msg . '</td>';
    } else {
        echo '<td class="error">' . $error_msg . '</td>';
    }
    echo '</tr>';
}

// Capturar errores para extensiones
function check_extension($ext) {
    return extension_loaded($ext);
}

// Verificar conexión a la base de datos
function check_database() {
    try {
        $host = getenv('DB_HOST') ?: '10.0.1.3';
        $usuario = getenv('DB_USER') ?: 'root';
        $password = getenv('DB_PASSWORD') ?: 'ONflEz9QYm64VDg9FdZqjeEQqanwhsxn31u1HTCHlX6dJh3OdPuWSHrA2lHTrXsV';
        $base_datos = getenv('DB_NAME') ?: 'prime';

        $connection = new mysqli($host, $usuario, $password, $base_datos);

        if ($connection->connect_error) {
            return ['success' => false, 'message' => $connection->connect_error];
        }

        // Verificar tablas
        $result = $connection->query("SHOW TABLES");
        $tables = [];
        if ($result) {
            while ($row = $result->fetch_array()) {
                $tables[] = $row[0];
            }
        }

        return [
            'success' => true,
            'message' => 'Conectado a ' . $base_datos,
            'details' => 'Tablas: ' . implode(', ', $tables)
        ];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Verificar directorios
function check_directory($dir, $check_writable = true) {
    if (!file_exists($dir)) {
        return ['success' => false, 'message' => 'No existe'];
    }

    if ($check_writable && !is_writable($dir)) {
        return ['success' => false, 'message' => 'Sin permisos de escritura'];
    }

    return ['success' => true, 'message' => 'OK', 'details' => 'Permisos: ' . substr(sprintf('%o', fileperms($dir)), -4)];
}

// Comprobar variables de entorno
function get_env_var($name, $default = '') {
    $value = getenv($name) ?: $default;
    if (empty($value)) {
        return ['success' => false, 'message' => 'No definido o vacío'];
    }
    if ($name == 'DB_PASSWORD' && !empty($value)) {
        return ['success' => true, 'message' => 'Configurado', 'details' => '********'];
    }
    return ['success' => true, 'message' => 'Configurado', 'details' => $value];
}

// Verificar versión PHP
function check_php_version($min_version = '7.4.0') {
    if (version_compare(PHP_VERSION, $min_version, '>=')) {
        return ['success' => true, 'message' => PHP_VERSION];
    } else {
        return ['success' => false, 'message' => PHP_VERSION, 'details' => 'Se requiere PHP ' . $min_version . ' o superior'];
    }
}

// Obtener todas las variables de entorno
function get_all_env_vars() {
    $env_vars = [];
    foreach ($_ENV as $key => $value) {
        if (!in_array($key, ['DB_PASSWORD', 'MYSQL_PASSWORD']) && !str_contains(strtolower($key), 'secret')) {
            $env_vars[$key] = $value;
        } else {
            $env_vars[$key] = '********';
        }
    }
    return $env_vars;
}

// Verificar módulos de Apache
function check_apache_module($module) {
    if (function_exists('apache_get_modules')) {
        return in_array($module, apache_get_modules());
    }
    return false; // No podemos verificar
}

// Si está autorizado, realizar las comprobaciones
if ($authorized) {
    $php_version = check_php_version('7.4.0');
    $db_result = check_database();
    $uploads_dir = check_directory(__DIR__ . '/uploads', true);
    $admin_uploads_dir = check_directory(__DIR__ . '/admin/uploads', true);
    $logs_dir = check_directory(__DIR__ . '/logs', true);

    $env_db_host = get_env_var('DB_HOST', 'sql105.infinityfree.com');
    $env_db_user = get_env_var('DB_USER', 'if0_38793011');
    $env_db_password = get_env_var('DB_PASSWORD');
    $env_db_name = get_env_var('DB_NAME', 'if0_38793011_prime');

    $all_env_vars = get_all_env_vars();
}

// Detectar errores específicos
$specific_error = '';
$error_description = '';
$error_solution = '';

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case '500':
            $specific_error = 'Error 500 - Internal Server Error';
            $error_description = 'El servidor encontró un error interno al procesar la solicitud.';
            $error_solution = 'Revisa los archivos de registro en la carpeta logs para más detalles.';
            break;
        case 'db':
            $specific_error = 'Error de Conexión a Base de Datos';
            $error_description = 'No se pudo establecer conexión con la base de datos.';
            $error_solution = 'Verifica las credenciales de la base de datos en el archivo .env y asegúrate de que la base de datos esté accesible.';
            break;
        case 'db_admin':
            $specific_error = 'Error de Conexión a Base de Datos (Admin)';
            $error_description = 'No se pudo establecer conexión con la base de datos desde el panel de administración.';
            $error_solution = 'Verifica las credenciales de la base de datos en el archivo .env y asegúrate de que la base de datos esté accesible.';
            break;
        default:
            $specific_error = 'Error Desconocido';
            $error_description = 'Se ha producido un error no identificado.';
            $error_solution = 'Revisa los archivos de registro en la carpeta logs para más detalles.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Diagnóstico - Prime Instinct</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .details {
            font-size: 0.8em;
            color: #666;
            margin-top: 5px;
        }
        form {
            margin: 20px 0;
        }
        input[type="password"] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 3px;
            width: 250px;
        }
        button {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .section {
            margin: 20px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .hidden {
            display: none;
        }
        .error-box {
            background-color: #ffebee;
            border-left: 4px solid #f44336;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .solution-box {
            background-color: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Diagnóstico de Prime Instinct</h1>

        <?php if ($specific_error): ?>
            <div class="error-box">
                <h2><?php echo $specific_error; ?></h2>
                <p><?php echo $error_description; ?></p>
            </div>
            <div class="solution-box">
                <h3>Solución Recomendada:</h3>
                <p><?php echo $error_solution; ?></p>
            </div>
        <?php endif; ?>

        <?php if (!$authorized): ?>
            <p>Por favor ingresa la contraseña para acceder a la herramienta de diagnóstico.</p>
            <form method="post" action="">
                <input type="password" name="password" placeholder="Contraseña">
                <button type="submit">Verificar</button>
            </form>
        <?php else: ?>

            <div class="section">
                <h2>Información del Sistema</h2>
                <table>
                    <tr>
                        <th>Componente</th>
                        <th>Estado</th>
                    </tr>
                    <tr>
                        <td>Versión de PHP</td>
                        <td class="<?php echo $php_version['success'] ? 'success' : 'error'; ?>">
                            <?php echo $php_version['message']; ?>
                            <?php if (isset($php_version['details'])): ?>
                                <div class="details"><?php echo $php_version['details']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php check_item(check_extension('mysqli'), 'Extensión MySQLi'); ?>
                    <?php check_item(check_extension('pdo_mysql'), 'Extensión PDO MySQL'); ?>
                    <?php check_item(check_extension('gd'), 'Extensión GD (imágenes)'); ?>
                    <?php check_item(check_extension('zip'), 'Extensión ZIP'); ?>
                    <?php check_item(check_apache_module('mod_rewrite'), 'Apache mod_rewrite'); ?>
                </table>
            </div>

            <div class="section">
                <h2>Base de Datos</h2>
                <table>
                    <tr>
                        <th>Componente</th>
                        <th>Estado</th>
                    </tr>
                    <tr>
                        <td>Conexión a la Base de Datos</td>
                        <td class="<?php echo $db_result['success'] ? 'success' : 'error'; ?>">
                            <?php echo $db_result['message']; ?>
                            <?php if (isset($db_result['details'])): ?>
                                <div class="details"><?php echo $db_result['details']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <h2>Variables de Entorno</h2>
                <table>
                    <tr>
                        <th>Variable</th>
                        <th>Estado</th>
                    </tr>
                    <tr>
                        <td>DB_HOST</td>
                        <td class="<?php echo $env_db_host['success'] ? 'success' : 'error'; ?>">
                            <?php echo $env_db_host['message']; ?>
                            <?php if (isset($env_db_host['details'])): ?>
                                <div class="details"><?php echo $env_db_host['details']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>DB_USER</td>
                        <td class="<?php echo $env_db_user['success'] ? 'success' : 'error'; ?>">
                            <?php echo $env_db_user['message']; ?>
                            <?php if (isset($env_db_user['details'])): ?>
                                <div class="details"><?php echo $env_db_user['details']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>DB_PASSWORD</td>
                        <td class="<?php echo $env_db_password['success'] ? 'success' : 'error'; ?>">
                            <?php echo $env_db_password['message']; ?>
                            <?php if (isset($env_db_password['details'])): ?>
                                <div class="details"><?php echo $env_db_password['details']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>DB_NAME</td>
                        <td class="<?php echo $env_db_name['success'] ? 'success' : 'error'; ?>">
                            <?php echo $env_db_name['message']; ?>
                            <?php if (isset($env_db_name['details'])): ?>
                                <div class="details"><?php echo $env_db_name['details']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <h2>Directorios</h2>
                <table>
                    <tr>
                        <th>Directorio</th>
                        <th>Estado</th>
                    </tr>
                    <tr>
                        <td>Directorio de uploads</td>
                        <td class="<?php echo $uploads_dir['success'] ? 'success' : 'error'; ?>">
                            <?php echo $uploads_dir['message']; ?>
                            <?php if (isset($uploads_dir['details'])): ?>
                                <div class="details"><?php echo $uploads_dir['details']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Directorio admin/uploads</td>
                        <td class="<?php echo $admin_uploads_dir['success'] ? 'success' : 'error'; ?>">
                            <?php echo $admin_uploads_dir['message']; ?>
                            <?php if (isset($admin_uploads_dir['details'])): ?>
                                <div class="details"><?php echo $admin_uploads_dir['details']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Directorio de logs</td>
                        <td class="<?php echo $logs_dir['success'] ? 'success' : 'error'; ?>">
                            <?php echo $logs_dir['message']; ?>
                            <?php if (isset($logs_dir['details'])): ?>
                                <div class="details"><?php echo $logs_dir['details']; ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <h2>Todas las Variables de Entorno</h2>
                <button onclick="toggleEnvVars()">Mostrar/Ocultar Variables</button>
                <div id="envVarsContainer" class="hidden">
                    <table>
                        <tr>
                            <th>Nombre</th>
                            <th>Valor</th>
                        </tr>
                        <?php foreach ($all_env_vars as $name => $value): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($name); ?></td>
                            <td><?php echo htmlspecialchars($value); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>

            <div class="section">
                <h2>Verificación de logs</h2>
                <p>Archivos de log disponibles:</p>
                <?php
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
                ?>
            </div>

        <?php endif; ?>

        <footer>
            <p><a href="/">Volver a la página principal</a></p>
        </footer>
    </div>

    <script>
        function toggleEnvVars() {
            var container = document.getElementById('envVarsContainer');
            if (container.classList.contains('hidden')) {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
