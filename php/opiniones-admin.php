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
    <link rel="stylesheet" href="../src/css/opinion-admin.css">
    <link rel="icon" href="../img/black-logo - copia.ico">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Opiniones</title>
</head>
<body>
    <header>
      <nav class="menu">
        <div class="logo">
          <a href="index-admin.php">
            <img src="../img/logo.png" class="logo" alt="Logo"/>
          </a>
        </div>
        <button class="menu-toggle" id="menu-toggle">☰</button>
        <div id="menu-links" class="links">
          <a class="link" href="../index.php">Inicio</a>
          <a class="link" href="../index.php#destino">Acerca</a>
          <a class="link" href="tienda-admin.php">Tienda</a>
          <a class="selected-link" href="opiniones-admin.php">Opiniones</a>
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
      </div>
      </nav>
    </header>
    <main>
        <div class="freedom"></div>
       
        <h1 class="title">Exprésate</h1>
        <div class="line"></div>

        <div class="rating-container">
          <div class="rating-header">
              <h1 class="rating-title">Opiniones <span class="title-rating-star">★</span>5.0</h1>
          </div>
        </div>
  <section class="review-section">
      <div class="comment">
          <i class='bx bxs-user-circle user-icon-input'></i>
          <textarea type="text" id="input-review" placeholder="Escribir una opinión" class="input-review"></textarea>
          <div class="star-rating">
              <i class='bx bxs-star star'></i>
              <i class='bx bxs-star star'></i>
              <i class='bx bxs-star star'></i>
              <i class='bx bxs-star star'></i>
              <i class='bx bxs-star star'></i>
          </div>
          <i class='bx bx-send send-icon'></i>
      </div>  
   
    <div class="comments-section">
    <?php
    include("conexion.php");
    $sql = "SELECT * FROM opiniones ORDER BY id DESC";
    $result = mysqli_query($connection, $sql);

    while($row = mysqli_fetch_assoc($result)) {
        $isCurrentUser = $row['username'] === $_SESSION['username'];
        ?>
        <div class="comment-container" data-comment-id="<?php echo $row['id']; ?>">
            <div class="user-info">
                <i class='bx bxs-user-circle user-icon-input'></i>
                <h2><?php echo htmlspecialchars($row['username']); ?> 
                    <?php if($isCurrentUser): ?>
                        <i class='bx bxs-crown' style="color: #FFD700;"></i>
                    <?php endif; ?>
                </h2>
                <?php if($isCurrentUser): ?>
                    <button class="delete-comment" onclick="deleteComment(<?php echo $row['id']; ?>)">
                        <i class='bx bx-trash'></i>
                    </button>
                <?php endif; ?>
            </div>
            <div class="star-rating">
                <?php
                for($i = 1; $i <= 5; $i++) {
                    if($i <= $row['rating']) {
                        echo '<i class="bx bxs-star active"></i>';
                    } else {
                        echo '<i class="bx bx-star"></i>';
                    }
                }
                ?>
            </div>
            <p class="review-text"><?php echo htmlspecialchars($row['comment']); ?></p>
            <div class="review-date">
                <i class='bx bx-calendar calendar-icon'></i>
                <span>Fecha de publicación: <strong><?php echo date('d/m/Y', strtotime($row['date'])); ?></strong></span>
            </div>
        </div>
        <?php
    }
    mysqli_close($connection);
    ?>
</div>
  </section>
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
            <h3>SUSCRIBTETE</h3>
            <p>¡Unete a nuestra Newsletter!</p>
            <input type="text" class="newsletter-input" placeholder="Correo electrónico"/>
            <button class="newsletter-button">Suscribirse</button>
          </form>
        </div>
        <p class="copyright">© 2025, All rights reserved</p>
      </footer>
</body>

<script src="../src/js/opinion-admin.js"></script>
</html>