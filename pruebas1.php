<?php
$hosts = [
    '10.0.1.6',        // IP en red coolify
    'mysql',            // Nombre del servicio (si está en misma red)
    'qs0404k8swsk4wsc00osc0co'  // Nombre del contenedor
];

foreach ($hosts as $host) {
    try {
        $connetion = new mysqli(
            $host,
            'root',
            'ONflEz9QYm64VDg',
            'prime',
            3306
        );

        echo "✅ Conexión exitosa con: $host";
        $connetion->close();
        exit;

    } catch (Exception $e) {
        echo "❌ Fallo en $host: " . $e->getMessage() . "\n";
    }
}

echo "🚨 No se pudo conectar a ninguna dirección";
