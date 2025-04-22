<?php
session_start();
require_once("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($connection, $_POST['correo']);
    $password = $_POST['contraseña'];

    try {
        $sql = "SELECT * FROM usuario WHERE correo = ?";
        $stmt = mysqli_prepare($connection, $sql);

        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . mysqli_error($connection));
        }

        mysqli_stmt_bind_param($stmt, "s", $email);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al ejecutar la consulta: " . mysqli_stmt_error($stmt));
        }

        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['contraseña'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['email'] = $row['correo'];
                $_SESSION['username'] = $row['nombre'];

                // Cerrar el statement y la conexión antes de redirigir
                mysqli_stmt_close($stmt);
                mysqli_close($connection);

                header("Location: index-admin.php");
                exit();
            }
        }

        // Cerrar el statement y la conexión antes de redirigir
        mysqli_stmt_close($stmt);
        mysqli_close($connection);

        header("Location: login-admin.php?error=1");
        exit();
    } catch (Exception $e) {
        // Registrar el error
        error_log("Error en login: " . $e->getMessage(), 3, __DIR__ . '/../logs/db_errors.log');

        // Asegurarse de cerrar recursos si están abiertos
        if (isset($stmt) && $stmt) {
            mysqli_stmt_close($stmt);
        }
        if (isset($connection) && $connection) {
            mysqli_close($connection);
        }

        header("Location: login-admin.php?error=2");
        exit();
    }
} else {
    header("Location: login-admin.php");
    exit();
}
?>
