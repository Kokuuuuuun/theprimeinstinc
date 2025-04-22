<?php
function checkDuplicateEmail($connection, $email, $exclude_id = null) {
    try {
        // Ajustar la consulta si se proporciona un ID a excluir (útil para actualizaciones)
        if ($exclude_id) {
            $query = "SELECT id FROM usuario WHERE correo = ? AND id != ? LIMIT 1";
            $stmt = $connection->prepare($query);

            if (!$stmt) {
                throw new RuntimeException("Error en preparación: " . $connection->error);
            }

            $stmt->bind_param("si", $email, $exclude_id);
        } else {
            $query = "SELECT id FROM usuario WHERE correo = ? LIMIT 1";
            $stmt = $connection->prepare($query);

            if (!$stmt) {
                throw new RuntimeException("Error en preparación: " . $connection->error);
            }

            $stmt->bind_param("s", $email);
        }

        $stmt->execute();

        // Almacenar resultados y limpiar buffer
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;

        // Limpieza explícita
        $stmt->free_result();
        $stmt->close();

        return $exists;
    } catch (Exception $e) {
        // En caso de error, registrarlo y asegurarnos de cerrar el statement si existe
        error_log("Error en checkDuplicateEmail: " . $e->getMessage(), 3, __DIR__ . '/../logs/db_errors.log');

        if (isset($stmt) && $stmt) {
            $stmt->close();
        }

        // Re-lanzar la excepción para que sea manejada por el código que llama a esta función
        throw $e;
    }
}
