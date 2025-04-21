<?php
// test-mysql.php
$hosts = [
    'qs0404k8swsk4wsc00osc0co', // Nombre del contenedor
    'mysql',                     // Intento con alias genérico
    '172.17.0.1'                // IP por defecto de Docker
];

foreach ($hosts as $host) {
    try {
        $mysqli = new mysqli(
            $host,
            'root',
            'ONflEz9QYm64VDg',
            'prime',
            3306
        );
        
        echo "✅ Conexión exitosa a $host\n";
        $mysqli->close();
    } catch (Exception $e) {
        echo "❌ Fallo en $host: " . $e->getMessage() . "\n";
    }
}
<?php
// db-diagnostic.php
// -------------------------------------------
// Configuración (Editar según tu entorno)
// -------------------------------------------
$mysqlConfig = [
    'hosts' => [
        'mysqld',                        // Nombre del servicio
        'qs0404k8swsk4wsc00osc0co',     // ID del contenedor MySQL
        '172.17.0.1',                   // Red por defecto de Docker
        'host.docker.internal',         // Alias especial para hosts
        '127.0.0.1'                     // Loopback (para probar config local)
    ],
    'port' => 3306,
    'user' => 'root',
    'pass' => 'ONflEz9QYm64VDg9FdZqjeEQqanwhsxn31u1HTCHlX6dJh3OdPuWSHrA2lHTrXsV',
    'db' => 'prime'
];

// -------------------------------------------
// Funciones de diagnóstico
// -------------------------------------------
function testPort($host, $port) {
    try {
        $socket = @fsockopen($host, $port, $errno, $errstr, 2);
        if (!$socket) throw new Exception("❌ PORT $port: $errstr ($errno)");
        fclose($socket);
        return true;
    } catch (Exception $e) {
        die($e->getMessage());
    }
}

function testMysqlConnection($host, $port, $user, $pass, $db) {
    try {
        $mysqli = new mysqli($host, $user, $pass, $db, $port);
        
        if ($mysqli->connect_error) {
            throw new Exception("❌ MySQL: " . $mysqli->connect_error);
        }
        
        $result = [
            'success' => true,
            'version' => $mysqli->server_version,
            'host_info' => $mysqli->host_info
        ];
        $mysqli->close();
        return $result;
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// -------------------------------------------
// Ejecutar pruebas
// -------------------------------------------
echo "================================\n";
echo "  DIAGNÓSTICO DE CONEXIÓN MySQL  \n";
echo "================================\n\n";

foreach ($mysqlConfig['hosts'] as $host) {
    echo "🔥 Probando: $host:{$mysqlConfig['port']}\n";
    
    // 1. Prueba de puerto
    if (testPort($host, $mysqlConfig['port'])) {
        echo "   ✓ Puerto accesible\n";
    }
    
    // 2. Prueba de conexión MySQL
    $result = testMysqlConnection(
        $host,
        $mysqlConfig['port'],
        $mysqlConfig['user'],
        $mysqlConfig['pass'],
        $mysqlConfig['db']
    );
    
    if ($result['success']) {
        echo "   ✓ Conexión exitosa\n";
        echo "      Versión: {$result['version']}\n";
        echo "      Protocolo: {$result['host_info']}\n\n";
    } else {
        echo "   {$result['error']}\n\n";
    }
}

// -------------------------------------------
// Diagnóstico Docker
// -------------------------------------------
echo "================================\n";
echo "  DIAGNÓSTICO DOCKER/COOLIFY  \n";
echo "================================\n";

// 1. Estado del contenedor MySQL
echo "\n📦 Contenedor MySQL:\n";
echo shell_exec('docker inspect qs0404k8swsk4wsc00osc0co --format "📌 Estado: {{.State.Status}}\n🔄 Reiniciado: {{.RestartCount}} veces\n🎯 IP: {{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}"');

// 2. Redes asociadas
echo "\n🌐 Redes:\n";
echo shell_exec('docker inspect qs0404k8swsk4wsc00osc0co --format "{{range .NetworkSettings.Networks}}🔗 {{.NetworkID}}: {{.IPAddress}}\n{{end}}"');

// 3. Configuración MySQL crítica
echo "\n🔧 Configuración MySQL:\n";
echo shell_exec("docker exec qs0404k8swsk4wsc00osc0co sh -c 'cat /etc/mysql/my.cnf | grep -E \"bind-address|port\"'");

// 4. Usuarios y privilegios
echo "\n👤 Usuarios MySQL:\n";
echo shell_exec("docker exec qs0404k8swsk4wsc00osc0co mysql -u root -p{$mysqlConfig['pass']} --execute \"SELECT host, user FROM mysql.user WHERE user = 'root';\" 2>&1");

// -------------------------------------------
// Soluciones Automáticas Sugeridas
// -------------------------------------------
echo "\n================================\n";
echo "  POSIBLES SOLUCIONES  \n";
echo "================================\n";

echo "1. 🔄 Forzar conexión TCP/IP:\n";
echo "   Modificar en settings.php:\n";
echo "   'host' => 'mysql:3306' // ← Agregar puerto explícito\n\n";

echo "2. 🌐 Configurar acceso remoto en MySQL:\n";
echo "   docker exec -it qs0404k8swsk4wsc00osc0co mysql -u root -p\n";
echo "   SQL> ALTER USER 'root'@'%' IDENTIFIED WITH mysql_native_password BY '{$mysqlConfig['pass']}';\n";
echo "   SQL> FLUSH PRIVILEGES;\n\n";

echo "3. 🔧 Modificar bind-address:\n";
echo "   docker exec qs0404k8swsk4wsc00osc0co sh -c \"echo 'bind-address = 0.0.0.0' >> /etc/mysql/my.cnf\"\n";
echo "   docker restart qs0404k8swsk4wsc00osc0co\n\n";

echo "4. 🛠️ Verificar redes Docker:\n";
echo "   docker network ls\n";
echo "   docker network inspect [network-id]\n";
echo "   Asegurar que Drupal y MySQL están en la misma red\n";
