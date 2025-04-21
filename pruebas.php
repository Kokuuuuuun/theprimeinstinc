<?php
// test-mysql.php
$hosts = [
    'mysql',                   // Nombre del servicio
    'qs0404k8swsk4wsc00osc0co',// ID del contenedor MySQL
    '172.17.0.1',              // IP default de Docker
    'host.docker.internal'     // Alias especial para hosts Docker
];

$user = "root";
$pass = "ONflEz9QYm64VDg9FdZqjeEQqanwhsxn31u1HTCHlX6dJh3OdPuWSHrA2lHTrXsV";
$db = "prime";
$port = 3306;

foreach ($hosts as $host) {
    echo "=== Probando conexión a: $host ===\n";
    
    try {
        // 1. Verificar si el puerto está abierto
        $socket = @fsockopen($host, $port, $errno, $errstr, 2);
        if (!$socket) {
            throw new Exception("❌ Error de red: $errstr ($errno)");
        }
        fclose($socket);
        echo "✓ Puerto $port accesible\n";
        
        // 2. Probar conexión MySQL
        $mysqli = new mysqli($host, $user, $pass, $db);
        
        if ($mysqli->connect_error) {
            throw new Exception("❌ Error MySQL: " . $mysqli->connect_error);
        }
        
        echo "✓ Conexión exitosa!\n";
        echo "   Versión MySQL: " . $mysqli->server_version . "\n";
        echo "   Host info: " . $mysqli->host_info . "\n\n";
        $mysqli->close();
        
    } catch (Exception $e) {
        echo $e->getMessage() . "\n\n";
    }
}

// Verificación adicional
echo "=== Diagnóstico Docker ===\n";
echo "1. Contenedor MySQL: " . shell_exec('docker inspect qs0404k8swsk4wsc00osc0co --format "{{.State.Status}}"');
echo "2. Redes MySQL: " . shell_exec('docker inspect qs0404k8swsk4wsc00osc0co --format "{{.NetworkSettings.Networks}}"');
echo "3. Logs MySQL (últimas 5 líneas):\n" . shell_exec('docker logs --tail=5 qs0404k8swsk4wsc00osc0co');
