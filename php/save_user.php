<?php
require_once 'conexion.php';
require_once 'check_email.php';

// Generar nonce para CSP (debes implementar un sistema para generarlo y pasarlo a la vista)
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
        $connection->close();
        echo '<script nonce="'.$nonce.'">
            alert("Este correo electrónico ya está registrado");
            window.history.back();
        </script>';
        exit();
    }

    // Hash seguro con coste personalizado
    $hashed_password = password_hash($password, PASSWORD_ARGON2ID, ['memory_cost' => 1<<17, 'time_cost' => 4, 'threads' => 2]);

    // Transacción para integridad de datos
    $connection->begin_transaction();

    try {
        $sql = "INSERT INTO usuario (nombre, correo, contraseña) VALUES (?, ?, ?)";
        $stmt = $connection->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error en preparación: " . $connection->error);
        }

        $stmt->bind_param("sss", $nombre, $email, $hashed_password);

        // Ejecutar la consulta
        $stmt->execute();

        $connection->commit();

        echo '<script nonce="'.$nonce.'">
            alert("Usuario registrado correctamente");
            window.location.href = "login-admin.php";
        </script>';

    } catch (Exception $e) {
        $connection->rollback();
        throw $e;
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
        $connection->close();
    }

} catch (Exception $e) {
    echo '<script nonce="'.$nonce.'">
        alert("Error: ' . addslashes($e->getMessage()) . '");
        window.history.back();
    </script>';
    exit();
}
