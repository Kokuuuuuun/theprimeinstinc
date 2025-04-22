<?php
require_once 'conexion.php';
require_once 'check_email.php';

try {
    $email = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_EMAIL);
    $exclude_id = isset($_GET['exclude_id']) ? intval($_GET['exclude_id']) : null;

    if (!$email) {
        throw new Exception('Email no proporcionado o inválido');
    }

    $exists = checkDuplicateEmail($connection, $email, $exclude_id);

    // Cerrar la conexión antes de enviar la respuesta
    if (isset($connection) && $connection) {
        $connection->close();
    }

    header('Content-Type: application/json');
    echo json_encode(['exists' => $exists, 'success' => true]);

} catch (Exception $e) {
    // En caso de error, registrarlo y asegurarnos de cerrar la conexión
    error_log("Error en check_email_ajax: " . $e->getMessage(), 3, __DIR__ . '/../logs/db_errors.log');

    if (isset($connection) && $connection) {
        $connection->close();
    }

    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
