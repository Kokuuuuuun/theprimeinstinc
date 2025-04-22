<?php
session_start();
include("conexion.php");

// Verificar si hay una sesión activa y datos del formulario
if (!isset($_SESSION['user_id']) || !isset($_POST['nombre'])) {
    header("Location: tienda-admin.php");
    exit();
}

// Verificar estructura de la tabla pedidos
$table_check = "DESCRIBE pedidos";
$table_result = mysqli_query($connection, $table_check);
$telefono_length = 20; // Valor predeterminado

if ($table_result) {
    while ($row = mysqli_fetch_assoc($table_result)) {
        if ($row['Field'] == 'telefono') {
            // Extraer la longitud del campo teléfono
            preg_match('/\((\d+)\)/', $row['Type'], $matches);
            if (isset($matches[1])) {
                $telefono_length = (int)$matches[1];
            }
            break;
        }
    }
}

// Recoger y sanitizar los datos del formulario
$nombre = mysqli_real_escape_string($connection, $_POST['nombre']);
$email = mysqli_real_escape_string($connection, $_POST['email']);
$direccion = mysqli_real_escape_string($connection, $_POST['direccion']);
$telefono = mysqli_real_escape_string($connection, $_POST['telefono']);

// Validar y limitar el teléfono a la longitud máxima permitida
$telefono = preg_replace('/[^0-9+\-\s]/', '', $telefono); // Dejar solo números, +, - y espacios
if (strlen($telefono) > $telefono_length) {
    $telefono = substr($telefono, 0, $telefono_length);
}

$metodo_pago = mysqli_real_escape_string($connection, $_POST['metodo_pago']);
$numero_tarjeta = ($metodo_pago === 'tarjeta') ? mysqli_real_escape_string($connection, $_POST['numero_tarjeta']) : '';

// Para tarjetas, guarda solo los últimos 4 dígitos por seguridad
if ($metodo_pago === 'tarjeta' && strlen($numero_tarjeta) > 4) {
    $numero_tarjeta = '************' . substr($numero_tarjeta, -4);
}

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
    // Mostrar mensaje de error detallado
    $error_message = mysqli_error($connection);
    echo "<script>
        alert('Error al procesar el pedido: " . $error_message . "');
        window.location.href = 'checkout.php';
    </script>";
}

// Cerrar la conexión
mysqli_stmt_close($stmt);
mysqli_close($connection);
?>
