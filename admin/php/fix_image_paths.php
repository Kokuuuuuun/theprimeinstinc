<?php
include("conexion.php");

// Obtener todos los productos
$sql = "SELECT id, img FROM productos";
$result = mysqli_query($connection, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $img_path = $row['img'];
        $id = $row['id'];

        // Verificar si la ruta contiene "uploads/" sin "../" antes
        if (strpos($img_path, "uploads/") === 0) {
            // Corregir la ruta añadiendo "../" al principio
            $new_path = "../" . $img_path;

            // Actualizar la base de datos
            $update_sql = "UPDATE productos SET img = ? WHERE id = ?";
            $stmt = mysqli_prepare($connection, $update_sql);
            mysqli_stmt_bind_param($stmt, "si", $new_path, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            echo "Corregida la ruta para el producto ID: " . $id . "<br>";
        }
    }
    echo "Proceso completado.";
} else {
    echo "Error al obtener productos: " . mysqli_error($connection);
}

mysqli_close($connection);
?>

<a href="tienda-admin.php">Volver a la tienda</a>
