<?php
// Definir directorios
$source_dir = "../uploads/";
$target_dir = "../../uploads/";

// Crear el directorio destino si no existe
if (!file_exists($target_dir)) {
    if (mkdir($target_dir, 0777, true)) {
        echo "Directorio $target_dir creado correctamente.<br>";
    } else {
        echo "Error al crear el directorio $target_dir.<br>";
    }
}

// Verificar si hay archivos en el directorio fuente
$files = scandir($source_dir);
$copied_count = 0;

echo "<h1>Sincronización de directorios de uploads</h1>";

foreach ($files as $file) {
    if ($file != "." && $file != "..") {
        $source_file = $source_dir . $file;
        $target_file = $target_dir . $file;

        // Verificar si el archivo ya existe en el destino
        if (!file_exists($target_file)) {
            // Copiar el archivo
            if (copy($source_file, $target_file)) {
                echo "Archivo <strong>$file</strong> copiado correctamente.<br>";
                $copied_count++;
            } else {
                echo "Error al copiar el archivo $file.<br>";
            }
        } else {
            echo "El archivo $file ya existe en el directorio destino.<br>";
        }
    }
}

echo "<p>Total de archivos copiados: $copied_count</p>";

// Actualizar las rutas de las imágenes en la base de datos
include("conexion.php");

$sql = "SELECT id, img FROM productos";
$result = mysqli_query($connection, $sql);

$updated_count = 0;

if ($result && mysqli_num_rows($result) > 0) {
    echo "<h2>Actualizando rutas en la base de datos:</h2>";

    while ($row = mysqli_fetch_assoc($result)) {
        $img_path = $row['img'];
        $id = $row['id'];

        // Obtener solo el nombre del archivo de la ruta
        $file_name = basename($img_path);

        // Crear nueva ruta que apunte al directorio principal de uploads
        $new_path = "../uploads/" . $file_name;

        // Actualizar la base de datos
        $update_sql = "UPDATE productos SET img = ? WHERE id = ?";
        $stmt = mysqli_prepare($connection, $update_sql);
        mysqli_stmt_bind_param($stmt, "si", $new_path, $id);

        if (mysqli_stmt_execute($stmt)) {
            echo "Actualizada la ruta del producto ID: $id - Nueva ruta: $new_path<br>";
            $updated_count++;
        } else {
            echo "Error al actualizar el producto ID: $id - " . mysqli_error($connection) . "<br>";
        }

        mysqli_stmt_close($stmt);
    }

    echo "<p>Total de rutas actualizadas: $updated_count</p>";
} else {
    echo "<p>No hay productos en la base de datos para actualizar.</p>";
}

mysqli_close($connection);
?>

<a href="tienda-admin.php">Volver a la tienda</a>
