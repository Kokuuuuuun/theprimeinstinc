<?php
require_once 'conexion.php';
require_once 'check_email.php';

// Generar nonce para CSP
$nonce = base64_encode(random_bytes(16));

try {
    if (!isset($connection) || $connection->connect_error) {
        throw new Exception("Error de conexión a la base de datos");
    }

    // Sanitización moderna y validación
    $nombre = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    // Validación de campos
    if (empty($nombre) || empty($email) || empty($password)) {
        throw new Exception("Todos los campos son obligatorios");
    }

    // Validación de formato de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Formato de correo electrónico inválido");
    }

    // Validación de nombre (permite letras, espacios y acentos)
    if (!preg_match("/^[\p{L}\s]{2,50}$/u", $nombre)) {
        throw new Exception("El nombre solo puede contener letras y espacios (2-50 caracteres)");
    }

    if (strlen($password) < 6) {
        throw new Exception("La contraseña debe tener al menos 6 caracteres");
    }

    if ($password !== $confirmPassword) {
        throw new Exception("Las contraseñas no coinciden");
    }

    // Verificar email duplicado
    if (checkDuplicateEmail($connection, $email)) {
        throw new Exception("Este correo electrónico ya está registrado");
    }

    // Hash seguro con coste personalizado
    $hashed_password = password_hash($password, PASSWORD_ARGON2ID, ['memory_cost' => 1<<17, 'time_cost' => 4, 'threads' => 2]);

    // Transacción para integridad de datos
    $connection->begin_transaction();
    $stmt = null;

    try {
        $sql = "INSERT INTO usuario (nombre, correo, contraseña) VALUES (?, ?, ?)";
        $stmt = $connection->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error en preparación: " . $connection->error);
        }

        $stmt->bind_param("sss", $nombre, $email, $hashed_password);

        // Ejecutar la consulta
        if(!$stmt->execute()) {
            throw new Exception("Error al ejecutar: " . $stmt->error);
        }

        $connection->commit();

        // Registrar éxito
        error_log(date('[Y-m-d H:i:s]') . " Usuario registrado: $email", 3, __DIR__ . '/../logs/user_registrations.log');

        echo '<script nonce="'.$nonce.'">
            alert("Usuario registrado correctamente");
            window.location.href = "login-admin.php";
        </script>';

    } catch (Exception $e) {
        // Asegurarse de hacer rollback en caso de error
        $connection->rollback();
        throw $e;
    } finally {
        // Cerrar statement si existe
        if ($stmt) {
            $stmt->close();
        }

        // Cerrar la conexión
        $connection->close();
    }

} catch (Exception $e) {
    // Asegurar que la conexión se cierra incluso cuando hay un error
    if (isset($connection) && $connection) {
        if (!$connection->connect_error) {
            $connection->close();
        }
    }

    // Registrar el error
    error_log(date('[Y-m-d H:i:s]') . " Error en registro: " . $e->getMessage(), 3, __DIR__ . '/../logs/db_errors.log');

    echo '<script nonce="'.$nonce.'">
        alert("Error: ' . addslashes($e->getMessage()) . '");
        window.history.back();
    </script>';
    exit();
}
?>
