<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];

    // Definir los directorios de destino
    $admin_uploads_dir = "../uploads/";
    $main_uploads_dir = "../../uploads/";

    // Crear los directorios si no existen
    if (!file_exists($admin_uploads_dir)) {
        mkdir($admin_uploads_dir, 0777, true);
    }

    if (!file_exists($main_uploads_dir)) {
        mkdir($main_uploads_dir, 0777, true);
    }

    $imagen = $_FILES['imagen'];
    $imageFileType = strtolower(pathinfo($imagen['name'], PATHINFO_EXTENSION));
    $newFileName = uniqid() . '.' . $imageFileType;

    $admin_target_file = $admin_uploads_dir . $newFileName;
    $main_target_file = $main_uploads_dir . $newFileName;

    // Ruta para guardar en la base de datos (relativa a la raíz del proyecto)
    $db_file_path = "../uploads/" . $newFileName;

    // Validate image
    $valid_types = array('jpg', 'jpeg', 'png', 'gif');
    if (!in_array($imageFileType, $valid_types)) {
        echo "<script>
            alert('Solo se permiten archivos JPG, JPEG, PNG & GIF');
            window.location.href = 'tienda-admin.php';
        </script>";
        exit;
    }

    // Subir la imagen a ambos directorios
    $upload_success = move_uploaded_file($imagen["tmp_name"], $admin_target_file);

    if ($upload_success) {
        // Copiar la imagen al directorio principal de uploads
        copy($admin_target_file, $main_target_file);

        // Guardar en la base de datos
        $sql = "INSERT INTO productos (nombre, descripcion, precio, img) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "ssds", $nombre, $descripcion, $precio, $db_file_path);

        if (mysqli_stmt_execute($stmt)) {
            // Obtener el ID del producto insertado
            $producto_id = mysqli_insert_id($connection);

            echo "<script>
                alert('Producto agregado correctamente');
                window.location.href = 'tienda-admin.php';
            </script>";
        } else {
            echo "<script>
                alert('Error al guardar en la base de datos: " . mysqli_error($connection) . "');
                window.location.href = 'tienda-admin.php';
            </script>";
        }
    } else {
        echo "<script>
            alert('Error al subir la imagen');
            window.location.href = 'tienda-admin.php';
        </script>";
    }

    mysqli_close($connection);
}
?>
