<?php
include("conexion.php");

// Verificar si la tabla productos existe
$table_check = "SHOW TABLES LIKE 'productos'";
$table_result = mysqli_query($connection, $table_check);

if ($table_result && mysqli_num_rows($table_result) == 0) {
    // La tabla no existe, crearla
    $create_table = "CREATE TABLE productos (
        id INT(11) NOT NULL AUTO_INCREMENT,
        nombre VARCHAR(255) NOT NULL,
        descripcion TEXT,
        precio DECIMAL(10,2) NOT NULL,
        img VARCHAR(255) NOT NULL,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    if (mysqli_query($connection, $create_table)) {
        echo "Tabla 'productos' creada correctamente.<br>";
    } else {
        echo "Error al crear la tabla: " . mysqli_error($connection) . "<br>";
    }
} else {
    echo "La tabla 'productos' ya existe.<br>";
}

// Verificar si la tabla tiene datos
$count_check = "SELECT COUNT(*) as total FROM productos";
$count_result = mysqli_query($connection, $count_check);
$row = mysqli_fetch_assoc($count_result);

echo "Total de productos en la tabla: " . $row['total'] . "<br>";

mysqli_close($connection);
?>

<a href="tienda-admin.php">Volver a la tienda</a>
