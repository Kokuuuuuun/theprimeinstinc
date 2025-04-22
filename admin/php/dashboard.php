<?php
// Conexión a la base de datos
$conexion = mysqli_connect("localhost", "root", "", "prime");

// Consultas para obtener totales
$total_usuarios = mysqli_fetch_array(mysqli_query($conexion, "SELECT COUNT(*) as total FROM usuario"))[0];
$total_productos = mysqli_fetch_array(mysqli_query($conexion, "SELECT COUNT(*) as total FROM productos"))[0];
$total_pedidos = mysqli_fetch_array(mysqli_query($conexion, "SELECT COUNT(*) as total FROM pedidos"))[0];

// Consulta para pedidos recientes
$pedidos_recientes = mysqli_query($conexion, "SELECT * FROM pedidos ORDER BY fecha DESC LIMIT 5");
?>

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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../src/css/dashboard.css"/>
    <link rel="icon" href="../img/black-logo - copia.ico">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Dashboard</title>
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
                <a class="link" href="tienda-admin.php">Tienda</a>
                <a class="link" href="opiniones-admin.php">Opiniones</a>
                <a class="link" href="contacto-admin.php">Contacto</a>
                <a class="link" href="usuario-admin.php">Usuarios</a>
                <a class="selected-link" href="dashboard.php">Dashboard</a>
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
      </div>
        </nav>
    </header>

    <div class="dashboard-container">
        <h1 class="title">Dashboard</h1>
        <div class="line-title"></div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_usuarios; ?></div>
                <div class="stat-title">Total Usuarios</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_productos; ?></div>
                <div class="stat-title">Total Productos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_pedidos; ?></div>
                <div class="stat-title">Total Pedidos</div>
            </div>
        </div>

        <div class="recent-orders">
            <h2>Pedidos más recientes</h2>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Cliente</th>
                        <th>Correo</th>
                        <th>Direccion</th>
                        <th>Teléfono</th>
                        <th>Producto</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($pedido = mysqli_fetch_assoc($pedidos_recientes)) { ?>
                        <tr>
                            <td><?php echo $pedido['id']; ?></td>
                            <td><?php echo $pedido['nombre']; ?></td>
                            <td><?php echo $pedido['email']; ?></td>
                            <td><?php echo $pedido['direccion']; ?></td>
                            <td><?php echo $pedido['telefono']; ?></td>
                            <td><?php echo $pedido['producto']; ?></td>
                            <td>$<?php echo $pedido['total']; ?></td>
                            <td><?php echo $pedido['fecha']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

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
            <h3>SUSCRIBTETE</h3>
            <p>¡Unete a nuestra Newsletter!</p>
            <input type="text" class="newsletter-input" placeholder="Correo electrónico"/>
            <button class="newsletter-button">Suscribirse</button>
          </form>
        </div>
        <p class="copyright">© 2025, All rights reserved</p>
      </footer>
    <script src="../src/js/dashboard-admin.js"></script>
</body>
</html>