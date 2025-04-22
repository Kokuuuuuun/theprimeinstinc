<?php
$host = "localhost";
$usuario = "root"; 
$password = "";  // Por defecto en XAMPP no tiene contraseña
$base_datos = "prime"; // Asegúrate que este nombre es correcto

$connection = new mysqli($host, $usuario, $password, $base_datos);

if ($connection->connect_error) {
    die("Error de conexión: " . $connection->connect_error);
}
?>
