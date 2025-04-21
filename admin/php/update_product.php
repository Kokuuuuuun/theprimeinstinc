<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];

    // Handle image upload
    $target_dir = "../../uploads/";
    $imagen = $_FILES['imagen'];
    $imageFileType = strtolower(pathinfo($imagen['name'], PATHINFO_EXTENSION));
    $newFileName = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $newFileName;

    // Validate image
    $valid_types = array('jpg', 'jpeg', 'png', 'gif');
    if (!in_array($imageFileType, $valid_types)) {
        echo "<script>
            alert('Solo se permiten archivos JPG, JPEG, PNG & GIF');
            window.location.href = 'tienda-admin.php';
        </script>";
        exit;
    }

    if (move_uploaded_file($imagen["tmp_name"], $target_file)) {
        $relative_path = "/theprimeinstinct/uploads/" . $newFileName;

        $sql = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, img = ? WHERE id = ?";
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "ssdsi", $nombre, $descripcion, $precio, $relative_path, $id);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
                alert('Producto actualizado correctamente');
                window.location.href = 'tienda-admin.php';
            </script>";
        } else {
            echo "<script>
                alert('Error al actualizar el producto');
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
