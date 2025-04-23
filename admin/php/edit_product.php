<?php
include("conexion.php");

// Cambiar la lógica inicial para manejar tanto GET como POST
$id = isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? $_GET['id'] : null);

if(!$id) {
    echo "<script>
        alert('Error: No se proporcionó ID del producto');
        window.location.href = 'tienda-admin.php';
    </script>";
    exit;
}

if(!is_numeric($id)) {
    echo "<script>
        alert('Error: El ID debe ser un número');
        window.location.href = 'tienda-admin.php';
    </script>";
    exit;
}

if(isset($_POST['submitBtn'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];

    // Validate required fields server-side
    if(empty($nombre) || empty($descripcion) || $precio <= 0) {
        echo "<script>
            alert('Por favor, complete todos los campos correctamente');
            window.history.back();
        </script>";
        exit;
    }

    if($_FILES['imagen']['size'] > 0) {
        // Definir el directorio de uploads y crear si no existe
        $upload_dir = "../uploads/";

        // Crear el directorio si no existe
        if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                error_log("Error: No se pudo crear el directorio de uploads: " . $upload_dir);
                echo "<script>
                    alert('Error: No se pudo crear el directorio de uploads');
                    window.history.back();
                </script>";
                exit;
            }
            error_log("Directorio de uploads creado: " . $upload_dir);
        }

        // Validar permisos del directorio
        if (!is_writable($upload_dir)) {
            error_log("Error: El directorio de uploads no tiene permisos de escritura: " . $upload_dir);
            echo "<script>
                alert('Error: El directorio de uploads no tiene permisos de escritura');
                window.history.back();
            </script>";
            exit;
        }

        // Handle new image upload
        $imagen = $_FILES['imagen'];
        $imageFileType = strtolower(pathinfo($imagen['name'], PATHINFO_EXTENSION));
        $newFileName = uniqid() . '.' . $imageFileType;
        $target_file = $upload_dir . $newFileName;
        $db_file_path = "uploads/" . $newFileName; // Ruta relativa para la base de datos

        error_log("Intentando cargar imagen a: " . $target_file);

        if(move_uploaded_file($imagen["tmp_name"], $target_file)) {
            error_log("Imagen cargada exitosamente: " . $target_file);

            // Get old image to delete
            $sql = "SELECT img FROM productos WHERE id = ?";
            $stmt = mysqli_prepare($connection, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $old_product = mysqli_fetch_assoc($result);

            // Extraer solo el nombre del archivo de la ruta completa
            $old_image_path = $old_product['img'];
            // Si la ruta comienza con "uploads/", obtener el nombre del archivo
            if (strpos($old_image_path, "uploads/") === 0) {
                $old_filename = basename($old_image_path);
                $old_full_path = "../uploads/" . $old_filename;
            } else {
                $old_full_path = "../" . $old_image_path;
            }

            // Update with new image
            $sql = "UPDATE productos SET nombre=?, descripcion=?, precio=?, img=? WHERE id=?";
            $stmt = mysqli_prepare($connection, $sql);
            mysqli_stmt_bind_param($stmt, "ssdsi", $nombre, $descripcion, $precio, $db_file_path, $id);

            if(mysqli_stmt_execute($stmt)) {
                // Delete old image
                if($old_product && file_exists($old_full_path)) {
                    unlink($old_full_path);
                    error_log("Imagen anterior eliminada: " . $old_full_path);
                }
                echo "<script>
                    alert('Producto actualizado correctamente');
                    window.location.href = 'tienda-admin.php';
                </script>";
            } else {
                error_log("Error al actualizar producto: " . mysqli_error($connection));
                echo "<script>
                    alert('Error al actualizar producto: " . mysqli_error($connection) . "');
                    window.history.back();
                </script>";
            }
        } else {
            error_log("Error al cargar imagen: de " . $imagen["tmp_name"] . " a " . $target_file);
            echo "<script>
                alert('Error al cargar la imagen');
                window.history.back();
            </script>";
        }
    } else {
        // Update without changing image
        $sql = "UPDATE productos SET nombre=?, descripcion=?, precio=? WHERE id=?";
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "ssdi", $nombre, $descripcion, $precio, $id);

        if(mysqli_stmt_execute($stmt)) {
            echo "<script>
                alert('Producto actualizado correctamente');
                window.location.href = 'tienda-admin.php';
            </script>";
        } else {
            error_log("Error al actualizar producto (sin imagen): " . mysqli_error($connection));
            echo "<script>
                alert('Error al actualizar producto: " . mysqli_error($connection) . "');
                window.history.back();
            </script>";
        }
    }
} else {
    // Usar la variable $id que ya definimos arriba en lugar de $_GET['id']
    $sql = "SELECT * FROM productos WHERE id = ?";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(!$producto = mysqli_fetch_assoc($result)) {
        echo "<script>
            alert('Producto no encontrado');
            window.location.href = 'tienda-admin.php';
        </script>";
        exit;
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../src/css/tienda-admin.css">
    <link rel="icon" href="../img/black-logo - copia.ico">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Editar Producto</title>
</head>
<body>
    <div class="edit-form-container">
        <h2>Editar Producto</h2>
        <form action="edit_product.php" method="POST" enctype="multipart/form-data" id="productForm">
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <div class="form-group">
                <label for="nombre">Nombre del producto:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8'); ?>">
                <div class="error-message" id="nombre-error">El nombre del producto es obligatorio</div>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea maxlength="200" id="descripcion" name="descripcion"><?php echo htmlspecialchars($producto['descripcion'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                <div class="error-message" id="descripcion-error">La descripción es obligatoria</div>
            </div>

            <div class="form-group">
                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" step="0.01" value="<?php echo htmlspecialchars($producto['precio'], ENT_QUOTES, 'UTF-8'); ?>">
                <div class="error-message" id="precio-error">El precio es obligatorio y debe ser mayor a 0</div>
            </div>

            <div class="form-group">
                <label for="imagen">Nueva Imagen (opcional):</label>
                <input type="file" id="imagen" name="imagen" accept="image/*">
                <p class="current-image">Imagen actual: <img src="<?php echo htmlspecialchars($producto['img'], ENT_QUOTES, 'UTF-8'); ?>" height="50" alt="Imagen actual"></p>
            </div>

            <div class="form-buttons">
                <button type="submit" name="submitBtn" class="btn-submit">Actualizar Producto</button>
                <a href="tienda-admin.php" class="btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>
    <script src="js/edit-product-validation.js"></script>
</body>
</html>
<?php
}
mysqli_close($connection);
?>
