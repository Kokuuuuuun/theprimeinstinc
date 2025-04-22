<?php
include("conexion.php");

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Get image path before deleting
    $sql = "SELECT img FROM productos WHERE id = ?";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $producto = mysqli_fetch_assoc($result);
    
    // Delete from database
    $sql = "DELETE FROM productos WHERE id = ?";
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if(mysqli_stmt_execute($stmt)) {
        // Delete image file if exists
        if($producto && file_exists($producto['img'])) {
            unlink($producto['img']);
        }
        echo "<script>
            alert('Producto eliminado correctamente');
            window.location.href = 'tienda-admin.php';
        </script>";
    } else {
        echo "<script>
            alert('Error al eliminar el producto');
            window.location.href = 'tienda-admin.php';
        </script>";
    }
} else {
    header("Location: tienda-admin.php");
}

mysqli_close($connection);
?>
