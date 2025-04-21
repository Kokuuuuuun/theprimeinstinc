<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login-admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../src/css/tienda-admin.css" />
    <link rel="icon" href="img/black-logo - copia.ico">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Tienda</title>
</head>
<body>
    <header>
      <nav class="menu">
        <div class="logo">
          <a href="index-admin.php">
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
              <a href="logout.php" class="logout-btn">Cerrar sesión</a>
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
      </div>


        <div class="products">
          <?php
            include("conexion.php");
            
            $sql = "SELECT * FROM productos ORDER BY id DESC";
            $resultado = mysqli_query($connection, $sql);
            
            while($producto = mysqli_fetch_assoc($resultado)) {
            ?>
                <div class="product-container">
                    <img class="product" src="<?php echo $producto['img']; ?>" alt="<?php echo $producto['nombre']; ?>">
                    <h3><?php echo $producto['nombre']; ?></h3>
                    <p><?php echo $producto['descripcion']; ?></p>
                    <p class="price">$<?php echo number_format($producto['precio'], 2); ?></p>
                    <button class="buy" onclick="addToCart('<?php echo htmlspecialchars($producto['nombre']); ?>', 
                        <?php echo $producto['precio']; ?>, 
                        '<?php echo htmlspecialchars($producto['img']); ?>')">
                        Comprar
                    </button>

                </div>
            <?php
            }
            mysqli_close($connection);
            ?>
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