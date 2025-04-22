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
        // Handle new image upload
        $target_dir = "uploads/";
        $imagen = $_FILES['imagen'];
        $imageFileType = strtolower(pathinfo($imagen['name'], PATHINFO_EXTENSION));
        $newFileName = uniqid() . '.' . $imageFileType;
        $target_file = $target_dir . $newFileName;
        
        if(move_uploaded_file($imagen["tmp_name"], $target_file)) {
            // Get old image to delete
            $sql = "SELECT img FROM productos WHERE id = ?";
            $stmt = mysqli_prepare($connection, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $old_product = mysqli_fetch_assoc($result);
            
            // Update with new image
            $sql = "UPDATE productos SET nombre=?, descripcion=?, precio=?, img=? WHERE id=?";
            $stmt = mysqli_prepare($connection, $sql);
            mysqli_stmt_bind_param($stmt, "ssdsi", $nombre, $descripcion, $precio, $target_file, $id);
            
            if(mysqli_stmt_execute($stmt)) {
                // Delete old image
                if($old_product && file_exists($old_product['img'])) {
                    unlink($old_product['img']);
                }
                echo "<script>
                    alert('Producto actualizado correctamente');
                    window.location.href = 'tienda-admin.php';
                </script>";
            }
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
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="tienda-admin.css">
        <title>Editar Producto</title>
    </head>
    <body>
        <div class="edit-form-container">
            <h2>Editar Producto</h2>
            <form action="edit_product.php" method="POST" enctype="multipart/form-data" id="productForm">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                
                <div class="form-group">
                    <label for="nombre">Nombre del producto:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo $producto['nombre']; ?>">
                    <div class="error-message" id="nombre-error">El nombre del producto es obligatorio</div>
                </div>
                
                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <textarea maxlength="200" id="descripcion" name="descripcion"><?php echo $producto['descripcion']; ?></textarea>
                    <div class="error-message" id="descripcion-error">La descripción es obligatoria</div>
                </div>
                
                <div class="form-group">
                    <label for="precio">Precio:</label>
                    <input type="number" id="precio" name="precio" step="0.01" value="<?php echo $producto['precio']; ?>">
                    <div class="error-message" id="precio-error">El precio es obligatorio y debe ser mayor a 0</div>
                </div>
                
                <div class="form-group">
                    <label for="imagen">Nueva Imagen (opcional):</label>
                    <input type="file" id="imagen" name="imagen" accept="image/*">
                    <p class="current-image">Imagen actual: <img src="<?php echo $producto['img']; ?>" height="50"></p>
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