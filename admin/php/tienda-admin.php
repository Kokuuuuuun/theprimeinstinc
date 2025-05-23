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
    <link rel="stylesheet" href="../src/css/tienda-admin.css" />
    <link rel="icon" href="../img/black-logo - copia.ico">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Tienda</title>
    <style>
        /* Additional styles for admin-tools links */
        .admin-tools {
            margin-top: 20px;
            text-align: center;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }
        .admin-tools h3 {
            margin-bottom: 15px;
            font-size: 1.2em;
            color: #333;
        }
        .admin-links {
            display: flex;
            justify-content: center;
            gap: 25px;
            flex-wrap: wrap;
        }
        .admin-tool-link {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #444;
            font-weight: 600;
            background-color: #fff;
            padding: 8px 12px;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: background-color 0.3s, color 0.3s;
        }
        .admin-tool-link i {
            font-size: 1.4em;
            color: #007bff;
        }
        .admin-tool-link:hover {
            background-color: #007bff;
            color: #fff;
        }
        .admin-tool-link:hover i {
            color: #fff;
        }
    </style>
</head>
<body>
    <header>
      <nav class="menu">
        <div class="logo">
          <a href="../index.php">
            <img src="../img/logo.png" class="logo" alt="Logo" />
          </a>
        </div>
        <button class="menu-toggle" id="menu-toggle">☰</button>
        <div id="menu-links" class="links">
          <a class="link" href="../index.php">Inicio</a>
          <a class="link" href="../index.php#destino">Acerca</a>
          <a class="selected-link" href="tienda-admin.php">Tienda</a>
          <a class="link" href="opiniones-admin.php">Opiniones</a>
          <a class="link" href="contacto-admin.php">Contacto</a>
          <a class="link" href="usuario-admin.php">Usuarios</a>
          <a class="link" href="dashboard.php">Dashboard</a>
        </div>
        <div class="user">
          <div class="user-icon" id="user-icon">
              <i class='bx bxs-user-circle' ></i>
          </div>
          <div class="user-dropdown" id="user-dropdown">
              <div class="user-info">
                  <span class="username"><?php echo $_SESSION['username']; ?></span>
                  <span class="email"><?php echo $_SESSION['email']; ?></span>
              </div>
              <a href="php/logout.php" class="logout-btn">Cerrar sesión</a>
          </div>

        <i id="cart-icon" class='bx bx-cart' ></i>
        <div id="cart-div" class="cart-div">
          <h3>Carrito de Compras</h3>
          <div id="cart-items"></div>
          <div class="cart-total">
              <p>Total: $<span id="cart-total">0.00</span></p>
          </div>
          <button id="checkout-btn" class="checkout-btn">Proceder al Pago</button>
        </div>
      </nav>
    </header>
    <main>
      <div class="carousel">
          <div class="carousel-inner">
              <div class="carousel-item active" style="background-image: url('../img/carrusel1.png');"></div>
              <div class="carousel-item" style="background-image: url('../img/carrusel2.jpg');"></div>
              <div class="carousel-item" style="background-image: url('../img/carrusel3.png');"></div>
              <div class="carousel-item" style="background-image: url('../img/carrusel4.png');"></div>
              <div class="carousel-item" style="background-image: url('../img/carrusel5.jpg');"></div>
              <div class="carousel-item" style="background-image: url('../img/carrusel6.jpg');"></div>
          </div>
      </div>


      <div class="filters">
          <div class="search-filter">
              <i class='bx bx-search search-icon'></i>
              <input type="text" id="search-input" placeholder="Buscar productos...">
          </div>
          <div class="filter-options">
              <select id="price-filter">
                  <option value="all">Todos los precios</option>
                  <option value="low">Menor precio</option>
                  <option value="high">Mayor precio</option>
              </select>
          </div>
          <button id="add-product-btn" class="admin-btn">
                  <i class='bx bx-plus'></i> Añadir Producto
          </button>
      </div>

      <div id="product-modal" class="modal">
        <div class="modal-content">
            <button id="close-modal" class="close-modal"><i class='bx bx-x'></i></button>
            <h2>Añadir Nuevo Producto</h2>
            <form action="add_product.php" method="POST" enctype="multipart/form-data" id="add-product-form">
                <div class="form-group">
                    <label for="nombre">Nombre del producto:</label>
                    <input type="text" id="nombre" class="inputarea" name="nombre">
                    <span class="error-message" id="nombre-error"></span>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" class="inputarea" name="descripcion"></textarea>
                    <span class="error-message" id="descripcion-error"></span>
                </div>

                <div class="form-group">
                    <label for="precio">Precio:</label>
                    <input type="number" id="precio" class="inputarea" name="precio" step="0.01">
                    <span class="error-message" id="precio-error"></span>
                </div>

                <div class="form-group">
                    <label for="imagen">Imagen:</label>
                    <input type="file" id="imagen" class="inputarea" name="imagen" accept="image/*">
                    <span class="error-message" id="imagen-error"></span>
                </div>

                <button type="submit">Guardar Producto</button>
            </form>
        </div>
    </div>

      <div class="products">
          <?php
            include("conexion.php");

            // Verificar si la tabla productos existe
            $table_check = "SHOW TABLES LIKE 'productos'";
            $table_result = mysqli_query($connection, $table_check);

            if ($table_result && mysqli_num_rows($table_result) == 0) {
                echo "<p>No se encontró la tabla de productos. <a href='check_products_table.php'>Haga clic aquí para crearla</a>.</p>";
            } else {
                $sql = "SELECT * FROM productos ORDER BY id DESC";
                $resultado = mysqli_query($connection, $sql);

                if ($resultado && mysqli_num_rows($resultado) > 0) {
                    while($producto = mysqli_fetch_assoc($resultado)) {
                        // Asegurar que la ruta de la imagen sea correcta
                        $img_path = $producto['img'];
                        // Si la ruta no comienza con "../", añadirlo
                        if (strpos($img_path, "../") !== 0 && strpos($img_path, "http") !== 0) {
                            $img_path = "../" . $img_path;
                        }
            ?>
                <div class="product-container">
                    <img class="product" src="<?php echo $img_path; ?>" alt="<?php echo $producto['nombre']; ?>">
                    <h3><?php echo $producto['nombre']; ?></h3>
                    <p><?php echo $producto['descripcion']; ?></p>
                    <p class="price">$<?php echo number_format($producto['precio'], 2); ?></p>
                    <button class="buy" onclick="addToCart('<?php echo htmlspecialchars($producto['nombre']); ?>',
                        <?php echo $producto['precio']; ?>,
                        '<?php echo htmlspecialchars($img_path); ?>')">
                        Comprar
                    </button>
                    <div class="admin-actions">
                        <a href="edit_product.php?id=<?php echo $producto['id']; ?>" class="edit-btn">
                           <i class='bx bx-edit admin-f'></i>
                        </a>
                        <a href="delete_product.php?id=<?php echo $producto['id']; ?>" class="delete-btn" onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                            <i class='bx bx-trash admin-f'></i>
                        </a>
                    </div>
                </div>
            <?php
                    }
                } else {
                    echo "<p>No hay productos para mostrar. <a href='#' id='add-product-link'>Añadir un producto</a></p>";
                }
            }
            mysqli_close($connection);
            ?>

            <!-- Enlaces para el diagnóstico y herramientas de administración -->
            <div class="admin-tools">
                <h3>Herramientas de Administración</h3>
                <div class="admin-links">
                    <a href="check_uploads_dir.php" class="admin-tool-link">
                        <i class='bx bx-image'></i>
                        <span>Diagnóstico de imágenes</span>
                    </a>
                    <a href="fix_image_paths.php" class="admin-tool-link">
                        <i class='bx bx-link'></i>
                        <span>Reparar rutas de imágenes</span>
                    </a>
                    <a href="fix_uploads_dir.php" class="admin-tool-link">
                        <i class='bx bx-sync'></i>
                        <span>Sincronizar directorios</span>
                    </a>
                    <a href="check_orders_table.php" class="admin-tool-link">
                        <i class='bx bx-table'></i>
                        <span>Verificar tabla de pedidos</span>
                    </a>
                </div>
            </div>


      </div>
     </main>
     <footer>
      <div class="footer-content">
        <div class="footer-icons">
          <img src="img/logo.png" class="footer-logo" alt="">
          <div class="social-icon">
            <i class='bx bxl-facebook-square'></i>
            <i class='bx bxl-instagram-alt' ></i>
            <i class='bx bxl-whatsapp-square' ></i>
          </div>
        </div>
        <div class="footer-links">
           <h3>EMPRESA</h3>
           <a href="#">Aviso legal</a>
           <a href="#">Politica de privacidad</a>
           <a href="#">Términos y condiciones</a>
          </ul>
        </div>
        <div class="footer-links">
           <h3>AYUDA</h3>
           <a href="#">Seguimiento de pedidos</a>
           <a href="#">Política de devoluciones</a>
           <a href="#">Preguntas frecuentes</a>
          </ul>
        </div>
        <div class="footer-links">
           <h3>SOBRE NOSOTROS</h3>
           <a href="#">Nuestra historia</a>
           <a href="#">Blog</a>
           <a href="#">Feedbacks</a>
          </ul>
        </div>
        <form class="newsletter">
          <h3>SUSCRIBETE</h3>
          <p>¡Unete a nuestra Newsletter!</p>
          <input type="text" class="newsletter-input" placeholder="Correo electrónico"/>
          <button class="newsletter-button">Suscribirse</button>
        </form>
      </div>
      <p class="copyright">© 2025, All rights reserved</p>
    </footer>
</body>
<script src="../src/js/tienda-admin.js"></script>
</html>
