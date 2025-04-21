<?php
session_start();
include("conexion.php");

// Verificar si hay una sesión activa y datos del formulario
if (!isset($_SESSION['user_id']) || !isset($_POST['nombre'])) {
    header("Location: tienda-admin.php");
    exit();
}

// Recoger y sanitizar los datos del formulario
$nombre = mysqli_real_escape_string($connection, $_POST['nombre']);
$email = mysqli_real_escape_string($connection, $_POST['email']);
$direccion = mysqli_real_escape_string($connection, $_POST['direccion']);
$telefono = mysqli_real_escape_string($connection, $_POST['telefono']);
$metodo_pago = mysqli_real_escape_string($connection, $_POST['metodo_pago']);
$numero_tarjeta = ($metodo_pago === 'tarjeta') ? mysqli_real_escape_string($connection, $_POST['numero_tarjeta']) : '';
$total = floatval($_POST['total']);

// Preparar el string de productos
$productos_array = array();
foreach ($_POST['productos'] as $producto) {
    $productos_array[] = $producto['nombre'] . ' (x' . $producto['cantidad'] . ')';
}
$productos = mysqli_real_escape_string($connection, implode(', ', $productos_array));

// Preparar la consulta SQL
$query = "INSERT INTO pedidos (nombre, email, direccion, telefono, producto, total, metodo_pago, numero_tarjeta) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

// Preparar y ejecutar la consulta
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "sssssdss", 
    $nombre,
    $email,
    $direccion,
    $telefono,
    $productos,
    $total,
    $metodo_pago,
    $numero_tarjeta
);

// Ejecutar la consulta y manejar el resultado
if (mysqli_stmt_execute($stmt)) {
    // Limpiar el carrito y mostrar mensaje de éxito
    $_SESSION['cart'] = array();
    echo "<script>
        alert('¡Pedido realizado con éxito!');
        window.location.href = 'tienda-admin.php';
    </script>";
} else {
    // Mostrar mensaje de error
    echo "<script>
        alert('Error al procesar el pedido: " . mysqli_error($connection) . "');
        window.location.href = 'checkout.php';
    </script>";
}

// Cerrar la conexión
mysqli_stmt_close($stmt);
mysqli_close($connection);
?>