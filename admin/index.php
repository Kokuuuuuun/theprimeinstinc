<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../php/login-admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../src/css/style-admin.css"/>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>

    <link rel="icon" href="img/black-logo - copia.ico">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Inicio</title>
  </head>
  <body>
    <header>
      <nav class="menu">
        <div class="logo">
          <a href="index.php">
            <img src="img/logo.png" class="logo" alt="Logo" />
          </a>
        </div>
        <button class="menu-toggle" id="menu-toggle">☰</button>
        <div id="menu-links" class="links">
          <a class="selected-link" href="index.php">Inicio</a>
          <a class="link" href="index.php#destino">Acerca</a>
          <a class="link" href="php/tienda-admin.php">Tienda</a>
          <a class="link" href="php/opiniones-admin.php">Opiniones</a>
          <a class="link" href="php/contacto-admin.php">Contacto</a>
          <a class="link" href="php/usuario-admin.php">Usuarios</a>
          <a class="link" href="php/dashboard.php">Dashboard</a>
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
    <div>
      <div class="presentation">
        <div class="text-presentation">
          <h3 class="exclusive-text">Exclusive</h3>
          <h1 class="big-title">Prime instinct</h1>
          <p class="sipno">Energía en cada paso, rendimiento sin límites.</p>
        </div>
        <div class="image-presentation">
          <img src="img/shoesbg.png" alt="" />
          <a href="tienda-admin.php">
            <button class="products-button">Productos</button>
          </a>
        </div>
      </div>

      <div class="container-all">
        <h2 id="destino" class="title">¿Quíenes somos?</h2>
        <div class="line-title"></div>
        <div class="content">
          <div class="about-content">
            <div class="about-text-content">
              <div>
                <h2 class="subtitle">Nuestra Misión</h2>
                <div class="line"></div>
              </div>
              <p class="about-text">
                Nuestra misión es ofrecer a nuestros clientes una gama excepcional de calzados deportivos que no solo satisfaga
                sus necesidades, sino que también supere sus expectativas en términos de comodidad, estilo y rendimiento.
              </p>
            </div>
            <img src="img/mision.jpg" class="about-img" alt="" />
          </div>
          <div class="about-content">
            <img src="img/vision.jpg" class="about-img" alt="" />
            <div class="about-text-content">
              <div>
                <h2 class="subtitle">Nuestra Visión</h2>
                <div class="line"></div>
              </div>
              <p class="about-text">
                Aspiramos a ser reconocido como un referente en la industria, no solo por su variedad y calidad de productos, sino
                también por su compromiso con la innovación y la experiencia del cliente. 
              </p>
            </div>
          </div>
          <div class="about-content">
            <div class="about-text-content">
              <div>
                <h2 class="subtitle">Nuestros valores</h2>
                <div class="line"></div>
              </div>
              <p class="about-text">
                 Los valores que tenemos como negocio para ofrecer a nuestros clientes, son: Creatividad, Honestidad, Respeto, Excelencia, Capacidad, Colaboración, Solidaridad, Responsabilidad, Humildad
              </p>
            </div>
            <img src="img/value.jpg" class="about-img" alt="" />
          </div>
        </div>
      </div>

      <div class="container-all2">
        <h2 class="title">Lo que dicen de nosotros</h2>
        <div class="line-title"></div>
          <p class="contact-text">
          Opiniones que nos han dejado nuestros clientes.
        </p>
      <div class="testimonials">
          <div class="testimonials-img">
            <p>Muy comodo, me encantan sus productos❤️</p></div>
          <div class="testimonials-img">
            <p>>Buena calidad 💪, sigan así </p>
          </div>
          <div class="testimonials-img">
            <p>Necesito más de esta tienda</p>
          </div>
          <div class="testimonials-img">
            <p>Seguidor fiel hasta la tumba 👍</p>
          </div>
          <div class="testimonials-img">
            <p>No entiendo que hace la gente que no compra aqui 😂</p>
          </div>
          <div class="testimonials-img">
            <p>He ganado un maraton, por tanta calidad de estos productos</p>
          </div>
        </div>
      </div>

      <div class="container-all3">
        <h2 class="title">Contáctanos</h2>
        <div class="line-title"></div>
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
            <img src="img/Tenis_de_lado-removebg-preview.png" alt="">
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
  <script src="../src/js/script-admin.js"></script>
</html>
