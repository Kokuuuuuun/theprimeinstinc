<?php
require_once 'conexion.php';
require_once 'check_email.php';

try {
    // Verify connection is available
    if (!isset($connection) || $connection->connect_error) {
        throw new Exception("Error de conexión a la base de datos");
    }

    $nombre = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    // Validate required fields
    if (empty($nombre) || empty($email) || empty($password)) {
        throw new Exception("Todos los campos son obligatorios");
    }

    // Validate password length
    if (strlen($password) < 6) {
        throw new Exception("La contraseña debe tener al menos 6 caracteres");
    }

    // Validate passwords match
    if ($password !== $confirmPassword) {
        throw new Exception("Las contraseñas no coinciden");
    }

    // Check for duplicate email in the 'usuario' table
    if (checkDuplicateEmail($connection, $email)) {
        echo '<script>
            alert("Este correo electrónico ya está registrado");
            window.history.back();
        </script>';
        exit();
    }

    // Hash password and save user to 'usuario' table
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Corrected table name from 'usuarios' to 'usuario'
    $sql = "INSERT INTO usuario (nombre, correo, contraseña) VALUES (?, ?, ?)";
    $stmt = $connection->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta: " . $connection->error);
    }

    $stmt->bind_param("sss", $nombre, $email, $hashed_password);
    
    if ($stmt->execute()) {
        echo '<script>
            alert("Usuario registrado correctamente");
            window.location.href = "login-admin.php";
        </script>';
    } else {
        throw new Exception("Error al registrar usuario: " . $stmt->error);
    }

} catch (Exception $e) {
    echo '<script>
        alert("Error: ' . addslashes($e->getMessage()) . '");
        window.history.back();
    </script>';
}
?>