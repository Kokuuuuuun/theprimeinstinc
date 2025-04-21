<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar que los campos no estén vacíos
    if(empty($_POST['nombre']) || empty($_POST['descripcion']) || empty($_POST['precio'])) {
        echo "<script>
            alert('Todos los campos son obligatorios');
            window.location.href = 'tienda-admin.php';
        </script>";
        exit;
    }
    
    $nombre = mysqli_real_escape_string($connection, $_POST['nombre']);
    $descripcion = mysqli_real_escape_string($connection, $_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    
    // Verificar si el producto ya existe
    $check_sql = "SELECT id FROM productos WHERE nombre = ?";
    $check_stmt = mysqli_prepare($connection, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "s", $nombre);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);
    
    if(mysqli_stmt_num_rows($check_stmt) > 0) {
        echo "<script>
            alert('Ya existe un producto con ese nombre');
            window.location.href = 'tienda-admin.php';
        </script>";
        exit;
    }
    
    // Handle image upload
    $target_dir = "../../uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
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
        
        $sql = "INSERT INTO productos (nombre, descripcion, precio, img) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($connection, $sql);

        if(!$stmt) {
            echo "<script>
                alert('Error en la preparación de la consulta: " . mysqli_error($connection) . "');
                window.location.href = 'tienda-admin.php';
            </script>";
            exit;
        }

        // Agregar índice único en la base de datos
        // ALTER TABLE productos ADD UNIQUE (nombre);

        mysqli_stmt_bind_param($stmt, "ssds", $nombre, $descripcion, $precio, $relative_path);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($connection);
            echo "<script>
                alert('Producto agregado correctamente');
                window.location.href = 'tienda-admin.php';
            </script>";
            exit;
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