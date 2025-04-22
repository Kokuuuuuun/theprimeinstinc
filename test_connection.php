<?php
// Test script to verify database connections
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test de Conexión Múltiple</h1>";

// Test 1: Basic connection
echo "<h2>Test 1: Conexión Básica</h2>";
require_once 'php/conexion.php';

if ($connection && !$connection->connect_error) {
    echo "<p style='color:green'>✓ Primera conexión exitosa</p>";

    // Verify we can run a query
    $result = $connection->query("SELECT 1 as test");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>Resultado: " . $row['test'] . "</p>";
        $result->free();
    }

    // Close the connection
    $connection->close();
    echo "<p>Conexión cerrada correctamente</p>";
} else {
    echo "<p style='color:red'>✗ Error en la primera conexión</p>";
}

// Test 2: Multiple connections
echo "<h2>Test 2: Múltiples Conexiones</h2>";

// First connection
require_once 'php/conexion.php';
echo "<p>Primera conexión creada</p>";

// Run a query
$result1 = $connection->query("SHOW TABLES");
if ($result1) {
    echo "<p>Tablas en la base de datos:</p><ul>";
    while ($row = $result1->fetch_array()) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";

    // Free the result set
    $result1->free();
    echo "<p>Resultado liberado</p>";
}

// Close the first connection
$connection->close();
echo "<p>Primera conexión cerrada</p>";

// Create a second connection
require_once 'php/conexion.php';
echo "<p>Segunda conexión creada</p>";

// Run another query
$result2 = $connection->query("SELECT COUNT(*) as total FROM usuario");
if ($result2) {
    $row = $result2->fetch_assoc();
    echo "<p>Total usuarios: " . $row['total'] . "</p>";

    // Free the result set
    $result2->free();
    echo "<p>Segundo resultado liberado</p>";
} else {
    echo "<p style='color:red'>Error al consultar usuarios: " . $connection->error . "</p>";
}

// Close the second connection
$connection->close();
echo "<p>Segunda conexión cerrada</p>";

echo "<h2>Conclusión</h2>";
echo "<p>Si has llegado hasta aquí sin errores, significa que las conexiones múltiples están funcionando correctamente.</p>";
echo "<p><a href='php/login-admin.php'>Prueba iniciar sesión</a></p>";
?>
