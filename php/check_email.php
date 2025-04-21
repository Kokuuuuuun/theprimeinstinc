<?php
function checkDuplicateEmail($connetion, $email) {
    $query = "SELECT id FROM usuario WHERE correo = ? LIMIT 1";
    $stmt = $connetion->prepare($query);

    if (!$stmt) {
        throw new RuntimeException("Error en preparación: " . $connetion->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();

    // Almacenar resultados y limpiar buffer
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;

    // Limpieza explícita
    $stmt->free_result();
    $stmt->close();

    return $exists;
}
