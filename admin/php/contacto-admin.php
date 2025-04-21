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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../src/css/contact-admin.css" />
    <link rel="icon" href="../img/black-logo - copia.ico">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Contact</title>
</head>
<body>
    <header>
      <nav class="menu">
        <div class="logo">
          <a href="index.html">
            <img src="../img/logo.png" class="logo" alt="Logo" />
          </a>
        </div>
         <button class="menu-toggle" id="menu-toggle">☰</button>
        <div class="links">
          <a class="link" href="../index.php">Inicio</a>
          <a class="link" href="../index.html#destino">Acerca</a>
          <a class="link" href="tienda-admin.php">Tienda</a>
          <a class="link" href="opiniones-admin.php">Opiniones</a>
          <a class="selected-link" href="contacto-admin.php">Contacto</a>
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
      </div>
      </nav>
    </header>
    <main>
      <div class="freedom"></div>
      <div class="map">
        <h1 class="title">Nos encontramos en:</h1>
        <div class="line"></div>

        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d2249.888624621416!2d-69.77805414089626!3d18.490608290266586!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sdo!4v1740969519861!5m2!1ses-419!2sdo" width="800" height="600" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div>

      <div class="social-media-content">
        <div class="social-title">
          <h1 class="title">Nuestras redes sociales:</h1>
          <div class="line"></div>
        </div>
        <p><i class='bx bxl-gmail' ></i>primeinstinct@gmail.com</p>
        <p><i class='bx bxl-twitter' ></i>@primeinstinct</p>
        <p><i class='bx bxl-youtube' ></i>Prime - Instinct</p>
        <p><i class='bx bxl-facebook-square' ></i>PrimeInstinct</p>
        <p><i class='bx bxl-instagram-alt'></i> @PrimeInstinct</p>
        <p><i class='bx bxl-whatsapp-square' ></i>  829 - 839 - 6898 </p>
      </div>



      <div class="container-all3">
        <h1 class="title">Contáctanos:</h1>
        <div class="line"></div>
        <p class="contact-text">
          Puedes contactarnos con el siguiente formulario.
        </p>
        <div class="contact-container">
          
          <form class="contact-form" action="#" method="post">
            <h2 class="contact-subtitle">Contáctanos</h2>
            <div class="contact-line"></div>

            <input class="contact-input" type="text" name="name" placeholder="Nombre"/>
            <input class="contact-input" type="text" name="name" placeholder="Apellido"/>
            <input class="contact-input" type="email" name="email" placeholder="Correo electrónico"/>
            <textarea class="contact-textarea" name="message" placeholder="Comentario"></textarea>
            <button class="contact-button" type="button">Enviar</button>
          </form>

          <div class="contact-img">
            <img src="../img/Tenis_de_lado-removebg-preview.png" alt="">
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
          <h3>SUSCRIBTETE</h3>
          <p>¡Unete a nuestra Newsletter!</p>
          <input type="text" class="newsletter-input" placeholder="Correo electrónico"/>
          <button class="newsletter-button">Suscribirse</button>
        </form>
      </div>
      <p class="copyright">© 2025, All rights reserved</p>
    </footer>
</body>
<script src="../src/js/contact-admin.js"></script>
</html>