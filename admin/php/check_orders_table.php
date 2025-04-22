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
    $fields = [
        "id" => ["expected" => "int(11)", "found" => "", "ok" => false],
        "nombre" => ["expected" => "varchar(100)", "found" => "", "ok" => false],
        "email" => ["expected" => "varchar(100)", "found" => "", "ok" => false],
        "direccion" => ["expected" => "varchar(255)", "found" => "", "ok" => false],
        "telefono" => ["expected" => "varchar(20)", "found" => "", "ok" => false],
        "producto" => ["expected" => "text", "found" => "", "ok" => false],
        "total" => ["expected" => "decimal(10,2)", "found" => "", "ok" => false],
        "metodo_pago" => ["expected" => "varchar(50)", "found" => "", "ok" => false],
        "numero_tarjeta" => ["expected" => "varchar(50)", "found" => "", "ok" => false],
        "fecha_pedido" => ["expected" => "timestamp", "found" => "", "ok" => false]
    ];

    $describe_query = "DESCRIBE pedidos";
    $result = mysqli_query($connection, $describe_query);

    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Campo</th><th>Tipo actual</th><th>Tipo esperado</th><th>Estado</th></tr>";

    $needs_update = false;
    $found_fields = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $field_name = $row['Field'];
            $field_type = strtolower($row['Type']);
            $found_fields[] = $field_name;

            if (array_key_exists($field_name, $fields)) {
                $fields[$field_name]["found"] = $field_type;

                // Comparar el tipo
                if (strpos($field_type, strtolower($fields[$field_name]["expected"])) !== false) {
                    $fields[$field_name]["ok"] = true;
                } else {
                    $needs_update = true;
                }
            }
        }
    }

    // Mostrar tabla de resultados
    foreach ($fields as $field => $data) {
        $status = in_array($field, $found_fields) ?
            ($data["ok"] ? "✅ Correcto" : "⚠️ Tipo incorrecto") :
            "❌ Campo faltante";

        $row_color = in_array($field, $found_fields) ?
            ($data["ok"] ? "#e8f5e9" : "#fff9c4") :
            "#ffebee";

        echo "<tr style='background-color: $row_color;'>";
        echo "<td>" . $field . "</td>";
        echo "<td>" . ($data["found"] ? $data["found"] : "No existe") . "</td>";
        echo "<td>" . $data["expected"] . "</td>";
        echo "<td>" . $status . "</td>";
        echo "</tr>";
    }

    echo "</table>";

    // Verificar si se necesita actualizar el campo telefono
    $telefono_field = $fields["telefono"];
    if (in_array("telefono", $found_fields) && !$telefono_field["ok"]) {
        echo "<p>Se detectó que el campo 'telefono' tiene un tipo incorrecto. Intentando corregir...</p>";

        $update_query = "ALTER TABLE pedidos MODIFY COLUMN telefono VARCHAR(20) NOT NULL";
        if (mysqli_query($connection, $update_query)) {
            echo "<p style='color:green;'>✅ Campo 'telefono' actualizado correctamente a VARCHAR(20).</p>";
        } else {
            echo "<p style='color:red;'>❌ Error al actualizar el campo 'telefono': " . mysqli_error($connection) . "</p>";
        }
    }

    // Verificar campos faltantes
    $missing_fields = array_diff(array_keys($fields), $found_fields);
    if (!empty($missing_fields)) {
        echo "<p>Se encontraron campos faltantes en la tabla. Intentando añadir...</p>";

        foreach ($missing_fields as $field) {
            $add_query = "ALTER TABLE pedidos ADD COLUMN " . $field . " " . $fields[$field]["expected"];

            if ($field === "fecha_pedido") {
                $add_query .= " DEFAULT CURRENT_TIMESTAMP";
            } else if ($field === "numero_tarjeta") {
                $add_query .= " NULL";
            } else if ($field !== "id") {
                $add_query .= " NOT NULL";
            }

            if (mysqli_query($connection, $add_query)) {
                echo "<p style='color:green;'>✅ Campo '$field' añadido correctamente.</p>";
            } else {
                echo "<p style='color:red;'>❌ Error al añadir el campo '$field': " . mysqli_error($connection) . "</p>";
            }
        }
    }
}

// Verificar si hay pedidos
$count_check = "SELECT COUNT(*) as total FROM pedidos";
$count_result = mysqli_query($connection, $count_check);
$row = mysqli_fetch_assoc($count_result);

echo "<p>Total de pedidos en la tabla: " . $row['total'] . "</p>";

mysqli_close($connection);
?>

<p><a href="tienda-admin.php">Volver a la tienda</a></p>
