<?php
// Configuración de la conexión a la base de datos
$host = "172.20.1.7";    // Dirección IP del servidor MySQL
$usuario = "root";       // Usuario de MySQL
$password = "1234567890"; // Contraseña de MySQL
$base_datos = "prime";   // Nombre de la base de datos
$puerto = 3306;          // Puerto de MySQL

// Crear conexión
$connection = new mysqli($host, $usuario, $password, $base_datos, $puerto);

// Verificar conexión
if ($connection->connect_error) {
    die("Error de conexión: " . $connection->connect_error);
}
?>
