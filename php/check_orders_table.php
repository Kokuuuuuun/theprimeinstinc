<?php
include("conexion.php");

// Verificar si la tabla pedidos existe
$table_check = "SHOW TABLES LIKE 'pedidos'";
$table_result = mysqli_query($connection, $table_check);

echo "<h2>Diagnóstico de la tabla pedidos</h2>";

if ($table_result && mysqli_num_rows($table_result) == 0) {
    // La tabla no existe, crearla
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
    echo "<p>La tabla 'pedidos' ya existe. Verificando estructura...</p>";

    // Verificar la estructura de la tabla
    $describe_query = "DESCRIBE pedidos";
    $result = mysqli_query($connection, $describe_query);

    $found_telefono = false;
    $telefono_type = "";

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['Field'] == 'telefono') {
                $found_telefono = true;
                $telefono_type = $row['Type'];
                break;
            }
        }
    }

    if ($found_telefono) {
        echo "<p>Campo 'telefono' encontrado con tipo: " . $telefono_type . "</p>";

        // Verificar si necesita actualizarse a VARCHAR(20)
        if (stripos($telefono_type, 'varchar(20)') === false) {
            echo "<p>Se necesita actualizar el campo 'telefono' a VARCHAR(20)...</p>";

            $update_query = "ALTER TABLE pedidos MODIFY COLUMN telefono VARCHAR(20) NOT NULL";
            if (mysqli_query($connection, $update_query)) {
                echo "<p style='color:green;'>✅ Campo 'telefono' actualizado correctamente a VARCHAR(20).</p>";
            } else {
                echo "<p style='color:red;'>❌ Error al actualizar el campo 'telefono': " . mysqli_error($connection) . "</p>";
            }
        } else {
            echo "<p style='color:green;'>✅ El campo 'telefono' ya tiene el formato correcto (VARCHAR(20)).</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ No se encontró el campo 'telefono' en la tabla.</p>";
    }
}

// Verificar si hay pedidos
$count_check = "SELECT COUNT(*) as total FROM pedidos";
$count_result = mysqli_query($connection, $count_check);
$row = mysqli_fetch_assoc($count_result);

echo "<p>Total de pedidos en la tabla: " . $row['total'] . "</p>";

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

    h2 {
        color: #333;
        border-bottom: 2px solid #ddd;
        padding-bottom: 10px;
    }

    p {
        margin: 10px 0;
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
