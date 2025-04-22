<?php
include("conexion.php");

// Iniciar la salida
echo "<h2>Verificación y Corrección de la Tabla de Pedidos</h2>";

// Verificar si la tabla pedidos existe
$table_check = "SHOW TABLES LIKE 'pedidos'";
$table_result = mysqli_query($connection, $table_check);

if ($table_result && mysqli_num_rows($table_result) == 0) {
    // La tabla no existe, crearla
    echo "<p>La tabla 'pedidos' no existe. Creándola ahora...</p>";

    $create_table = "CREATE TABLE pedidos (
        id INT(11) NOT NULL AUTO_INCREMENT,
        nombre VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        direccion VARCHAR(255) NOT NULL,
        telefono VARCHAR(20) NOT NULL,
        producto TEXT NOT NULL,
        total DECIMAL(10,2) NOT NULL,
        metodo_pago VARCHAR(50) NOT NULL,
        numero_tarjeta VARCHAR(50),
        fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    if (mysqli_query($connection, $create_table)) {
        echo "<p style='color:green;'>✅ Tabla 'pedidos' creada correctamente con todos los campos necesarios.</p>";
    } else {
        echo "<p style='color:red;'>❌ Error al crear la tabla: " . mysqli_error($connection) . "</p>";
    }
} else {
    echo "<p>La tabla 'pedidos' ya existe. Verificando y corrigiendo estructura...</p>";

    // Verificar la estructura actual de la tabla
    $describe_query = "DESCRIBE pedidos";
    $result = mysqli_query($connection, $describe_query);

    if (!$result) {
        echo "<p style='color:red;'>❌ Error al consultar la estructura de la tabla: " . mysqli_error($connection) . "</p>";
        exit;
    }

    // Guardar la estructura actual
    $columns = [];
    $numero_tarjeta_found = false;
    $numero_tarjeta_type = '';

    while ($row = mysqli_fetch_assoc($result)) {
        $columns[$row['Field']] = $row;

        if ($row['Field'] == 'numero_tarjeta') {
            $numero_tarjeta_found = true;
            $numero_tarjeta_type = $row['Type'];
        }
    }

    // Verificar y corregir el campo numero_tarjeta
    if ($numero_tarjeta_found) {
        echo "<p>Campo 'numero_tarjeta' encontrado con tipo: $numero_tarjeta_type</p>";

        // Verificar si el tipo es INT o similar
        if (strpos(strtolower($numero_tarjeta_type), 'int') !== false) {
            echo "<p>El campo 'numero_tarjeta' es de tipo INT. Modificando a VARCHAR(50)...</p>";

            $alter_query = "ALTER TABLE pedidos MODIFY COLUMN numero_tarjeta VARCHAR(50)";
            if (mysqli_query($connection, $alter_query)) {
                echo "<p style='color:green;'>✅ Campo 'numero_tarjeta' modificado a VARCHAR(50) exitosamente.</p>";
            } else {
                echo "<p style='color:red;'>❌ Error al modificar el campo 'numero_tarjeta': " . mysqli_error($connection) . "</p>";
            }
        } else if (strpos(strtolower($numero_tarjeta_type), 'varchar') !== false) {
            echo "<p style='color:green;'>✅ El campo 'numero_tarjeta' ya es de tipo VARCHAR. No se requieren cambios.</p>";
        } else {
            echo "<p>El campo 'numero_tarjeta' es de tipo $numero_tarjeta_type. Modificando a VARCHAR(50)...</p>";

            $alter_query = "ALTER TABLE pedidos MODIFY COLUMN numero_tarjeta VARCHAR(50)";
            if (mysqli_query($connection, $alter_query)) {
                echo "<p style='color:green;'>✅ Campo 'numero_tarjeta' modificado a VARCHAR(50) exitosamente.</p>";
            } else {
                echo "<p style='color:red;'>❌ Error al modificar el campo 'numero_tarjeta': " . mysqli_error($connection) . "</p>";
            }
        }
    } else {
        echo "<p style='color:red;'>❌ No se encontró el campo 'numero_tarjeta' en la tabla. Añadiéndolo...</p>";

        $add_column_query = "ALTER TABLE pedidos ADD COLUMN numero_tarjeta VARCHAR(50) AFTER metodo_pago";
        if (mysqli_query($connection, $add_column_query)) {
            echo "<p style='color:green;'>✅ Campo 'numero_tarjeta' añadido correctamente como VARCHAR(50).</p>";
        } else {
            echo "<p style='color:red;'>❌ Error al añadir el campo 'numero_tarjeta': " . mysqli_error($connection) . "</p>";
        }
    }
}

// Mostrar los pedidos actuales para verificar
echo "<h3>Pedidos actuales en la base de datos:</h3>";

$select_query = "SELECT id, nombre, email, fecha_pedido, total, metodo_pago, numero_tarjeta FROM pedidos LIMIT 10";
$select_result = mysqli_query($connection, $select_query);

if ($select_result && mysqli_num_rows($select_result) > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Fecha</th><th>Total</th><th>Método de Pago</th><th>Número de Tarjeta</th></tr>";

    while ($row = mysqli_fetch_assoc($select_result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . $row['fecha_pedido'] . "</td>";
        echo "<td>€" . number_format($row['total'], 2) . "</td>";
        echo "<td>" . htmlspecialchars($row['metodo_pago']) . "</td>";
        echo "<td>" . htmlspecialchars($row['numero_tarjeta']) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p>No hay pedidos en la base de datos o ocurrió un error al consultar.</p>";
}

mysqli_close($connection);
?>

<style>
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        background-color: #f9f9f9;
    }

    h2, h3 {
        color: #333;
        border-bottom: 2px solid #ddd;
        padding-bottom: 10px;
    }

    p {
        margin: 10px 0;
    }

    table {
        width: 100%;
        margin: 20px 0;
        border-collapse: collapse;
    }

    th {
        background-color: #f2f2f2;
    }

    td, th {
        padding: 8px;
        text-align: left;
        border: 1px solid #ddd;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    a {
        display: inline-block;
        background-color: #4CAF50;
        color: white;
        padding: 10px 15px;
        text-decoration: none;
        border-radius: 4px;
        margin-top: 20px;
    }

    a:hover {
        background-color: #45a049;
    }
</style>

<p><a href="tienda-admin.php">Volver a la tienda</a></p>
