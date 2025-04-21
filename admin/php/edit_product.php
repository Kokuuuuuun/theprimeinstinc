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
        $target_dir = "../uploads/";  // Corrigido o caminho
        $imagen = $_FILES['imagen'];
        $imageFileType = strtolower(pathinfo($imagen['name'], PATHINFO_EXTENSION));
        $newFileName = uniqid() . '.' . $imageFileType;
        $target_file = $target_dir . $newFileName;

        if(move_uploaded_file($imagen["tmp_name"], $target_file)) {
            // Get old image to delete
            $sql = "SELECT imagen FROM productos WHERE id = ?";  // Corrigido nome da tabela e coluna
            $stmt = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $old_product = mysqli_fetch_assoc($result);

            // Update with new image
            $sql = "UPDATE productos SET nombre=?, descripcion=?, precio=?, imagen=? WHERE id=?";  // Corrigido nome da tabela e coluna
            $stmt = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($stmt, "ssdsi", $nombre, $descripcion, $precio, $target_file, $id);

            if(mysqli_stmt_execute($stmt)) {
                // Delete old image
                if($old_product && file_exists($old_product['imagen'])) {  // Corrigido nome da coluna
                    unlink($old_product['imagen']);
                }
                echo "<script>
                    alert('Producto actualizado correctamente');
                    window.location.href = 'tienda-admin.php';
                </script>";
            } else {
                echo "<script>
                    alert('Error al actualizar el producto: " . mysqli_error($conexion) . "');
                    window.location.href = 'tienda-admin.php';
                </script>";
            }
        }
    } else {
        // Update without changing image
        $sql = "UPDATE productos SET nombre=?, descripcion=?, precio=? WHERE id=?";  // Corrigido nome da tabela
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "ssdi", $nombre, $descripcion, $precio, $id);

        if(mysqli_stmt_execute($stmt)) {
            echo "<script>
                alert('Producto actualizado correctamente');
                window.location.href = 'tienda-admin.php';
            </script>";
        } else {
            echo "<script>
                alert('Error al actualizar el producto: " . mysqli_error($conexion) . "');
                window.location.href = 'tienda-admin.php';
            </script>";
        }
    }
} else {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        
        $sql = "SELECT * FROM productos WHERE id = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $product = mysqli_fetch_assoc($result);
            
            if ($product) {
                // Mostrar formulario de edición
                include("editar_producto.php");
            } else {
                echo "<script>
                    alert('Producto no encontrado');
                    window.location.href = 'tienda-admin.php';
                </script>";
            }
        } else {
            echo "<script>
                alert('Error al cargar el producto');
                window.location.href = 'tienda-admin.php';
            </script>";
        }
    } else {
        header("Location: tienda-admin.php");
        exit();
    }
}
mysqli_close($conexion);
?>
