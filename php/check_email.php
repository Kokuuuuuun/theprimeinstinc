<?php
require_once 'conexion.php';

function checkDuplicateEmail($connection, $email, $exclude_id = null) {
    // Verify connection
    if (!$connection || $connection->connect_error) {
        throw new Exception("Error de conexión a la base de datos");
    }

    try {
        // Using 'usuario' instead of 'usuarios'
        $sql = "SELECT id FROM usuario WHERE correo = ?";
        $params = [$email];
        $types = "s";
        
        if ($exclude_id) {
            $sql .= " AND id != ?";
            $params[] = $exclude_id;
            $types .= "i";
        }
        
        $stmt = $connection->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta: " . $connection->error);
        }

        $stmt->bind_param($types, ...$params);
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }

        $result = $stmt->get_result();
        return $result->num_rows > 0;

    } catch (Exception $e) {
        throw new Exception("Error al verificar el correo: " . $e->getMessage());
    }
}

// Make sure conexion.php exists and has the correct database configuration
if (!isset($connection)) {
    die("Error: La conexión a la base de datos no está disponible");
}
?>