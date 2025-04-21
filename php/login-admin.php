<?php
session_start();
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Validar conexión
        if ($conexion->connect_error) {
            throw new Exception("Error de conexión: " . $conexion->connect_error);
        }

        // Sanitizar inputs
        $email = filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_EMAIL);
        $password = $_POST['contraseña'];

        // Consulta preparada
        $query = "SELECT id, nombre, contraseña FROM usuario WHERE correo = ? LIMIT 1";
        $stmt = $conexion->prepare($query);
        
        if (!$stmt) {
            throw new Exception("Error en preparación: " . $conexion->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        // Manejar resultados
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $result->free();
            $stmt->close();
            
            if (password_verify($password, $row['contraseña'])) {
                $_SESSION = [
                    'user_id' => $row['id'],
                    'username' => $row['nombre'],
                    'email' => $email,
                    'last_login' => time()
                ];
                
                header("Location: " . ($row['id'] == 1 ? '../admin/index.php' : '../index.php'));
                exit();
            }
        }

        throw new Exception("Credenciales inválidas");

    } catch (Exception $e) {
        if (isset($result)) $result->free();
        if (isset($stmt)) $stmt->close();
        $conexion->close();
        
        echo '<script>
            alert("Error: ' . addslashes($e->getMessage()) . '");
            window.location.href = "login-admin.php";
        </script>';
        exit();
    }
}
?>

<!-- El resto del HTML permanece igual -->
