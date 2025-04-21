<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../php/login-admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../src/css/tienda-admin.css" />
    <link rel="icon" href="../../img/black-logo - copia.ico">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Editar Producto</title>
</head>
<body>
    <header>
        <!-- ...existing header code... -->
    </header>
    <main>
        <div class="edit-product-modal">
            <h2>Editar Producto</h2>
            <form action="update_product.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                <div class="form-group">
                    <label for="nombre">Nombre del producto:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo $product['nombre']; ?>">
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion"><?php echo $product['descripcion']; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="precio">Precio:</label>
                    <input type="number" id="precio" name="precio" step="0.01" value="<?php echo $product['precio']; ?>">
                </div>
                <div class="form-group">
                    <label for="imagen">Imagen:</label>
                    <input type="file" id="imagen" name="imagen">
                    <img src="<?php echo $product['img']; ?>" alt="Imagen actual" style="max-width: 200px;">
                </div>
                <button type="submit">Guardar Cambios</button>
            </form>
        </div>
    </main>
    <script src="../../src/js/tienda-admin.js"></script>
</body>
</html>
