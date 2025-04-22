<?php
include("conexion.php");

// Verificar el directorio de uploads
$uploads_dir = "../uploads/";

echo "<h2>Diagnóstico del directorio de uploads</h2>";

// Verificar si el directorio existe
if (file_exists($uploads_dir)) {
    echo "El directorio $uploads_dir existe. ✅<br>";

    // Verificar si es escribible
    if (is_writable($uploads_dir)) {
        echo "El directorio $uploads_dir tiene permisos de escritura. ✅<br>";
    } else {
        echo "El directorio $uploads_dir NO tiene permisos de escritura. ❌<br>";
        echo "Intentando establecer permisos...<br>";

        if (chmod($uploads_dir, 0777)) {
            echo "Permisos establecidos correctamente. ✅<br>";
        } else {
            echo "No se pudieron establecer los permisos. ❌<br>";
            echo "Por favor, establezca manualmente los permisos 777 al directorio $uploads_dir<br>";
        }
    }

    // Listar archivos en el directorio
    echo "<h3>Archivos en el directorio:</h3>";
    $files = scandir($uploads_dir);
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            echo "<li>$file";
            // Verificar si el archivo es accesible
            $file_url = $uploads_dir . $file;
            echo " (Ruta completa: $file_url)";
            echo "</li>";
        }
    }
    echo "</ul>";

} else {
    echo "El directorio $uploads_dir NO existe. ❌<br>";
    echo "Intentando crear el directorio...<br>";

    if (mkdir($uploads_dir, 0777, true)) {
        echo "Directorio creado correctamente. ✅<br>";
    } else {
        echo "No se pudo crear el directorio. ❌<br>";
    }
}

// Mostrar información de las imágenes en la base de datos
echo "<h3>Imágenes registradas en la base de datos:</h3>";
$sql = "SELECT id, nombre, img FROM productos";
$result = mysqli_query($connection, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Producto</th><th>Ruta de imagen</th><th>Existe</th></tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        $img_path = $row['img'];
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
        echo "<td>" . htmlspecialchars($img_path) . "</td>";

        // Verificar si el archivo existe (quitando "../" para la verificación real)
        $real_path = str_replace("../", "", $img_path);
        $exists = file_exists("../" . $real_path) ? "✅" : "❌";

        echo "<td>$exists</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "No hay productos registrados en la base de datos.<br>";
}

mysqli_close($connection);
?>

<a href="tienda-admin.php">Volver a la tienda</a>
