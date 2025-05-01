<?php
// Configuración de la conexión a la base de datos
$host = "10.0.1.6";    // Dirección IP del servidor MySQL
$usuario = "root";       // Usuario de MySQL
$password = ""; // Contraseña de MySQL
$base_datos = "prime";   // Nombre de la base de datos
$puerto = 3306;          // Puerto de MySQL

// Crear conexión
$connection = new mysqli($host, $usuario, $password, $base_datos, $puerto);

// Verificar conexión
if ($connection->connect_error) {
    die("Error de conexión: " . $connection->connect_error);
}
?>
