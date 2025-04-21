<?php
require_once 'conexion.php';
require_once 'check_email.php';

try {
    // Validar conexión activa
    if (!isset($conexion) || $conexion->connect_error) {
        throw new Exception("Error de conexión a la base de datos");
    }

    // Recibir y sanitizar datos
    $nombre = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    // Validaciones
    if (empty($nombre) || empty($email) || empty($password)) {
        throw new Exception("Todos los campos son obligatorios");
    }

    if (strlen($password) < 6) {
        throw new Exception("La contraseña debe tener al menos 6 caracteres");
    }

    if ($password !== $confirmPassword) {
        throw new Exception("Las contraseñas no coinciden");
    }

    // Verificar email duplicado
    if (checkDuplicateEmail($conexion, $email)) {
        $conexion->close();
        echo '<script>
            alert("Este correo electrónico ya está registrado");
            window.history.back();
        </script>';
        exit();
    }

    // Hash de contraseña
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insertar usuario
    $sql = "INSERT INTO usuario (nombre, correo, contraseña) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Error en preparación: " . $conexion->error);
    }

    $stmt->bind_param("sss", $nombre, $email, $hashed_password);
    
    if ($stmt->execute()) {
        $last_id = $conexion->insert_id;
        $stmt->close();
        $conexion->close();
        
        echo '<script>
            alert("Usuario registrado correctamente");
            window.location.href = "login-admin.php";
        </script>';
    } else {
        throw new Exception("Error al registrar: " . $stmt->error);
    }

} catch (Exception $e) {
    if (isset($stmt)) $stmt->close();
    if (isset($conexion)) $conexion->close();
    
    echo '<script>
        alert("Error: ' . addslashes($e->getMessage()) . '");
        window.history.back();
    </script>';
    exit();
}
