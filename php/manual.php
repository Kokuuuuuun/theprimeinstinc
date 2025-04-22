<?php
session_start();
include 'conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
     <link rel="icon" href="img/black-logo - copia.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual de Usuario - Prime Instinct</title>
    <link rel="stylesheet" href="../src/css/style-admin.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
        }

        .manual-container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 5px;
        }

        h1 {
            color: #333;
            text-align: center;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        h2 {
            color: #444;
            margin-top: 30px;
            border-left: 4px solid #444;
            padding-left: 10px;
        }

        h3 {
            color: #555;
            margin-top: 20px;
        }

        p {
            margin-bottom: 15px;
        }

        ul, ol {
            margin-bottom: 15px;
            padding-left: 30px;
        }

        li {
            margin-bottom: 8px;
        }

        .screenshot {
            max-width: 100%;
            height: auto;
            margin: 15px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }

        .note {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 15px 0;
        }

        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 15px 0;
        }

        .tip {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 15px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        img{
            height: 300px;
            width: 600px;
        }

        .faq-question {
            font-weight: bold;
            cursor: pointer;
            padding: 10px;
            background-color: #f5f5f5;
            margin-top: 10px;
            border-radius: 4px;
        }

        .faq-answer {
            padding: 15px;
            background-color: #fff;
            border-left: 2px solid #ddd;
            margin-bottom: 15px;
        }

        .nav-toc {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .nav-toc ul {
            list-style-type: none;
            padding-left: 10px;
        }

        .nav-toc a {
            text-decoration: none;
            color: #007bff;
        }

        .nav-toc a:hover {
            text-decoration: underline;
        }

        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
        }

        .back-to-top:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="manual-container">
        <h1>Manual de Usuario - PrimeInstinct</h1>

        <div class="nav-toc">
            <h3>Contenido</h3>
            <ul>
                <li><a href="#introduccion">1. Introducción al Sistema</a></li>
                <li><a href="#requisitos">2. Requisitos Mínimos</a></li>
                <li><a href="#instrucciones">3. Instrucciones de Uso</a>
                    <ul>
                        <li><a href="#registro">3.1 Registro de Usuario</a></li>
                        <li><a href="#login">3.2 Inicio de Sesión</a></li>
                        <li><a href="#recuperar">3.3 Recuperación de Contraseña</a></li>
                        <li><a href="#tienda">3.4 Navegación por la Tienda</a></li>
                        <li><a href="#carrito">3.5 Uso del Carrito de Compras</a></li>
                        <li><a href="#checkout">3.6 Proceso de Checkout</a></li>
                        <li><a href="#opiniones">3.7 Dejando Opiniones</a></li>
                        <li><a href="#contacto">3.8 Contacto con Prime Instinct</a></li>
                    </ul>
                </li>
                <li><a href="#ejemplos">4. Ejemplos de Uso</a></li>
                <li><a href="#faq">5. Preguntas Frecuentes (FAQ)</a></li>
                <li><a href="#problemas">6. Resolución de Problemas</a></li>
            </ul>
        </div>

        <section id="introduccion">
            <h2>1. Introducción al Sistema</h2>
            <p>Bienvenido a <strong>Prime Instinct</strong>, su tienda en línea especializada en calzado de alta calidad. Nuestro sistema está diseñado para proporcionar una experiencia de compra fluida e intuitiva, permitiéndole descubrir, seleccionar y adquirir el mejor calzado con facilidad.</p>

            <p>Prime Instinct le ofrece:</p>
            <ul>
                <li>Catálogo completo de productos con imágenes de alta calidad</li>
                <li>Proceso de compra intuitivo</li>
                <li>Sistema de opiniones para compartir su experiencia</li>
                <li>Sección de contacto directo con nuestro equipo</li>
            </ul>

            <p>Este manual le guiará a través de todas las funciones disponibles en nuestro sitio web, desde el registro de una cuenta hasta la finalización de una compra.</p>
        </section>

        <section id="requisitos">
            <h2>2. Requisitos Mínimos</h2>
            <p>Para utilizar el sistema de Prime Instinct de manera óptima, recomendamos:</p>

            <h3>Dispositivos:</h3>
            <ul>
                <li>Computadora de escritorio, laptop, tablet o smartphone</li>
                <li>Resolución de pantalla mínima: 320px (móvil) / 768px (escritorio)</li>
            </ul>

            <h3>Navegadores soportados:</h3>
            <ul>
                <li>Google Chrome (versión 60 o superior)</li>
                <li>Mozilla Firefox (versión 55 o superior)</li>
                <li>Safari (versión 11 o superior)</li>
                <li>Microsoft Edge (versión 79 o superior)</li>
            </ul>

            <h3>Conexión a Internet:</h3>
            <ul>
                <li>Conexión a Internet estable con velocidad mínima recomendada de 1 Mbps</li>
            </ul>

            <h3>Ajustes del navegador:</h3>
            <ul>
                <li>JavaScript habilitado</li>
                <li>Cookies habilitadas</li>
            </ul>

            <div class="note">
                <p><strong>Nota:</strong> El sistema es completamente responsivo y se adapta a diferentes tamaños de pantalla, pero se recomienda el uso en dispositivos con pantallas más grandes para una mejor experiencia visual al examinar los detalles de los productos.</p>
            </div>
        </section>

        <section id="instrucciones">
            <h2>3. Instrucciones de Uso</h2>

            <section id="registro">
                <img src="../img/register.png" alt="">
                <h3>3.1 Registro de Usuario</h3>
                <p>Para crear una cuenta en PrimeInstinct, siga estos pasos:</p>
                <ol>
                    <li>En la pantalla de inicio de sesión, haga clic en "Regístrate"</li>
                    <li>Complete el formulario con la siguiente información:
                        <ul>
                            <li>Nombre de usuario</li>
                            <li>Correo electrónico</li>
                            <li>Contraseña (mínimo 6 caracteres)</li>
                            <li>Confirmar contraseña</li>
                        </ul>
                    </li>
                    <li>Haga clic en el botón "Regístrate"</li>
                    <li>Recibirá una confirmación de registro exitoso</li>
                </ol>

                <div class="tip">
                    <p><strong>Consejo:</strong> Utilice una contraseña única y segura que combine letras mayúsculas, minúsculas, números y caracteres especiales.</p>
                </div>
            </section>

            <section id="login">
                <img src="../img/login.jpg" alt="">
                <h3>3.2 Inicio de Sesión</h3>
                <p>Para acceder a su cuenta, siga estos pasos:</p>
                <ol>
                    <li>En la página principal, haga clic en "Iniciar sesión" en la barra de navegación</li>
                    <li>Ingrese su correo electrónico</li>
                    <li>Ingrese su contraseña</li>
                    <li>Haga clic en el botón "Inicia sesión"</li>
                </ol>

                <div class="warning">
                    <p><strong>Importante:</strong> Por razones de seguridad, se recomienda cerrar sesión al terminar de usar el sistema, especialmente en dispositivos compartidos.</p>
                </div>
            </section>

            <section id="recuperar">
                <img src="../img/recuperar.png" alt="">
                <h3>3.3 Recuperación de Contraseña</h3>
                <p>Si olvidó su contraseña, puede restablecerla siguiendo estos pasos:</p>
                <ol>
                    <li>En la página de inicio de sesión, haga clic en "Recuperar contraseña"</li>
                    <li>Ingrese el correo electrónico asociado a su cuenta</li>
                    <li>Haga clic en "Enviar enlace"</li>
                    <li>Siga las instrucciones para crear una nueva contraseña</li>
                </ol>

                <div class="note">
                    <p><strong>Nota:</strong> El enlace de recuperación tiene una validez de 1 hora por razones de seguridad.</p>
                </div>
            </section>

            <section id="menu">
                <img src="../img/m2.jpg" alt="">
                
                <h3>3.3 Navegación en el menu</h3>
                <p>Si no sabes navegar por las distintas páginas siga los siguientes pasos:</p>
                <ol>
                    <li>Luego de iniciar sesión en tu cuenta llegaras a la página principal</li>
                    <li>Aqui veras un menú con el cual navegarás por las páginas del sistema</li>
                    <li>Haga clic en los enlaces para cambiar de página</li>
                </ol>

                <div class="note">
                    <p><strong>Nota:</strong> El menu se encuentra en todas las páginas del sistema.</p>
                </div>
            </section>

            <section id="tienda">
                <img src="../img/m3.jpg" alt="">
                <h3>3.4 Navegación por la Tienda</h3>
                <p>Para explorar nuestro catálogo de productos:</p>
                <ol>
                    <li>Acceda a la sección "Tienda" desde la barra de navegación</li>
                    <li>Puede navegar por las diferentes categorías de productos</li>
                    <li>Utilice los filtros disponibles para refinar su búsqueda por:
                        <ul>
                            <li>Rango de precios</li>
                        </ul>
                    </li>
                </ol>

                <div class="tip">
                    <p><strong>Consejo:</strong> Utilice la función de ordenación para ver los productos por precio (de menor a mayor o viceversa).</p>
                </div>
            </section>

            <section id="carrito">
                <img src="../img/m4.jpg" alt="">
                <h3>3.5 Uso del Carrito de Compras</h3>
                <p>Para añadir productos a su carrito:</p>
                <ol>
                    <li>En la página de detalles del producto, seleccione:
                        <ul>
                            <li>Cantidad (con repetidos clicks)</li>
                        </ul>
                    </li>
                    <li>Haga clic en "Añadir al carrito"</li>
                    <li>Para ver su carrito, haga clic en el ícono del carrito en la barra de navegación</li>
                    <li>En la página del carrito, puede:
                        <ul>
                            <li>Eliminar productos</li>
                            <li>Ver el subtotal y el total</li>
                        </ul>
                    </li>
                </ol>

                <div class="warning">
                    <p><strong>Importante:</strong> Los productos en el carrito no se guardan automáticamente en su cuenta. Si cierra sesión y vuelve a iniciar más tarde, los productos no seguirán en su carrito.</p>
                </div>
            </section>

            <section id="checkout">
                <img src="../img/m5.jpg" alt="">
                <h3>3.6 Proceso de Checkout</h3>
                <p>Para completar su compra:</p>
                <ol>
                    <li>Desde la página del carrito, haga clic en "Proceder al pago"</li>
                    <li>Complete el formulario con su información de envío:
                        <ul>
                            <li>Nombre completo</li>
                            <li>Dirección</li>
                            <li>Ciudad</li>
                            <li>Código postal</li>
                            <li>Número de teléfono</li>
                        </ul>
                    </li>
                    <li>Seleccione un método de pago:
                        <ul>
                            <li>Tarjeta de crédito/débito</li>
                            <li>Pago contra entrega (si está disponible en su ubicación)</li>
                        </ul>
                    </li>
                    <li>Revise su pedido</li>
                    <li>Haga clic en "Confirmar pedido"</li>
                    <li>Recibirá una confirmación de su pedido con un número de referencia</li>
                </ol>

                <div class="warning">
                    <p><strong>Importante:</strong> Verifique cuidadosamente toda la información antes de confirmar su pedido.</p>
                </div>
            </section>

            <section id="opiniones">
                <img src="../img/m6.jpg" alt="">
                <img src="../img/m7.png" alt="">
                <h3>3.7 Dejando Opiniones</h3>
                <p>Para dejar una opinión sobre un producto:</p>
                <ol>
                    <li>Inicie sesión en su cuenta</li>
                    <li>Navegue hasta la página del producto que desea comentar</li>
                    <li>Desplácese hasta la sección "Opiniones"</li>
                    <li>Haga clic en "Escribir una opinión"</li>
                    <li>Califique el producto (1-5 estrellas)</li>
                    <li>Escriba su comentario</li>
                    <li>Haga clic en "Enviar opinión"</li>
                </ol>

                <div class="note">
                    <p><strong>Nota:</strong> Solo puede dejar opiniones sobre productos que haya comprado previamente.</p>
                </div>
            </section>

            <section id="contacto">
                <img src="../img/m8.jpg" alt="">
                <h3>3.8 Contacto con Prime Instinct</h3>
                <p>Para ponerse en contacto con nuestro equipo de atención al cliente:</p>
                <ol>
                    <li>Acceda a la sección "Contacto" desde la barra de navegación</li>
                    <li>Complete el formulario con:
                        <ul>
                            <li>Su nombre</li>
                            <li>Correo electrónico</li>
                            <li>Asunto</li>
                            <li>Mensaje</li>
                        </ul>
                    </li>
                    <li>Haga clic en "Enviar mensaje"</li>
                </ol>

                <div class="tip">
                    <p><strong>Consejo:</strong> Para consultas relacionadas con pedidos, incluya su número de pedido para una respuesta más rápida.</p>
                </div>
            </section>
        </section>

        <section id="ejemplos">
            <h2>4. Ejemplos de Uso</h2>

            <h3>Ejemplo 1: Compra rápida de un producto</h3>
            <ol>
                <li>Iniciar sesión con su cuenta</li>
                <li>Ir directamente a la tienda</li>
                <li>Buscar el producto deseado</li>
                <li>Hacer clic en el producto</li>
                <li>Seleccionar cantidad</li>
                <li>Añadir al carrito</li>
                <li>Proceder al pago</li>
                <li>Confirmar la información de envío y pago</li>
                <li>Finalizar pedido</li>
            </ol>
            <p>Tiempo estimado: 5-10 minutos</p>

            <h3>Ejemplo 2: Búsqueda de un producto específico</h3>
            <ol>
                <li>Ir a la sección "Tienda"</li>
                <li>Utilizar los filtros para seleccionar la categoría deseada</li>
                <li>Aplicar filtros adicionales como rango de precio</li>
                <li>Ordenar los resultados por relevancia o precio</li>
                <li>Explorar los productos que coinciden con los criterios</li>
            </ol>
            <p>Tiempo estimado: 2-5 minutos</p>

            <h3>Ejemplo 3: Dejar una opinión después de una compra</h3>
            <ol>
                <li>Iniciar sesión con su cuenta</li>
                <li>Ir a la sección "Mis pedidos" en su perfil</li>
                <li>Seleccionar el pedido que contiene el producto a comentar</li>
                <li>Hacer clic en "Dejar opinión" junto al producto</li>
                <li>Calificar el producto y escribir su comentario</li>
                <li>Enviar la opinión</li>
            </ol>
            <p>Tiempo estimado: 3-5 minutos</p>
        </section>

        <section id="faq">
            <h2>5. Preguntas Frecuentes (FAQ)</h2>

            <div class="faq-question">¿Necesito crear una cuenta para realizar una compra?</div>
            <div class="faq-answer">
                <p>Sí, para realizar una compra en Prime Instinct es necesario tener una cuenta. Esto nos permite ofrecerle una mejor experiencia, guardar su historial de pedidos y facilitar futuras compras.</p>
            </div>



            <div class="faq-question">¿Puedo modificar un pedido después de confirmarlo?</div>
            <div class="faq-answer">
                <p>Una vez que un pedido ha sido confirmado, no es posible modificarlo directamente a través del sistema. Sin embargo, puede ponerse en contacto con nuestro equipo de atención al cliente a través de la sección "Contacto" lo antes posible, y haremos todo lo posible para ayudarle.</p>
            </div>

            <div class="faq-question">¿Qué métodos de pago aceptan?</div>
            <div class="faq-answer">
                <p>Actualmente aceptamos pagos con tarjetas de crédito y débito (Visa, MasterCard, American Express), y pagos físicos contra entrega.</p>
            </div>

            <div class="faq-question">¿Qué debo hacer si olvidé mi contraseña?</div>
            <div class="faq-answer">
                <p>Si olvidó su contraseña, haga clic en el enlace "Recuperar contraseña" en la página de inicio de sesión. Ingrese el correo electrónico asociado a su cuenta y le enviaremos un enlace para restablecer su contraseña.</p>
            </div>

            <div class="faq-question">¿Los productos en mi carrito se guardan si cierro sesión?</div>
            <div class="faq-answer">
                <p>No, los productos en su carrito no se guardaran automáticamente en su cuenta. Si cierra sesión y vuelve a iniciar más tarde, los productos no seguirán en su carrito.</p>
            </div>

            <div class="faq-question">¿Puedo cancelar un pedido?</div>
            <div class="faq-answer">
                <p>Puede solicitar la cancelación de un pedido siempre que aún no haya sido enviado. Para ello, contacte con nuestro servicio de atención al cliente lo antes posible a través de la sección "Contacto", indicando su número de pedido y el motivo de la cancelación.</p>
            </div>
        </section>

        <section id="problemas">
            <h2>6. Resolución de Problemas</h2>

            <h3>Problema: No puedo iniciar sesión</h3>
            <p><strong>Posibles soluciones:</strong></p>
            <ul>
                <li>Verifique que está introduciendo el correo electrónico y la contraseña correctos</li>
                <li>Asegúrese de que el bloqueo de mayúsculas no está activado</li>
                <li>Pruebe a restablecer su contraseña utilizando la opción "Recuperar contraseña"</li>
                <li>Compruebe su conexión a Internet</li>
                <li>Intente usar otro navegador o borrar la caché del navegador actual</li>
            </ul>

            <h3>Problema: Los productos no se añaden al carrito</h3>
            <p><strong>Posibles soluciones:</strong></p>
            <ul>
                <li>Asegúrese de haber añadido el producto</li>
                <li>Verifique que tiene sesión iniciada</li>
                <li>Actualice la página e intente nuevamente</li>
                <li>Compruebe que el producto está disponible</li>
            </ul>

            <h3>Problema: La página de pago no carga correctamente</h3>
            <p><strong>Posibles soluciones:</strong></p>
            <ul>
                <li>Verifique su conexión a Internet</li>
                <li>Intente usar otro navegador</li>
                <li>Borre la caché y las cookies de su navegador</li>
                <li>Asegúrese de que no tiene bloqueadores de scripts o de publicidad que puedan interferir</li>
            </ul>

            <h3>Problema: No puedo ver las imágenes de los productos</h3>
            <p><strong>Posibles soluciones:</strong></p>
            <ul>
                <li>Verifique su conexión a Internet</li>
                <li>Actualice la página</li>
                <li>Intente usar otro navegador</li>
                <li>Compruebe que JavaScript está habilitado en su navegador</li>
            </ul>

            <div class="note">
                <p><strong>Nota:</strong> Si encuentra algún otro problema que no esté listado aquí, no dude en ponerse en contacto con nuestro equipo de soporte a través de la sección "Contacto".</p>
            </div>
        </section>
    </div>

    <a href="login-admin.php" class="back-to-top">Volver atras</a>



    <script>
        // Script para mostrar/ocultar respuestas de preguntas frecuentes
        document.addEventListener('DOMContentLoaded', function() {
            var questions = document.querySelectorAll('.faq-question');

            questions.forEach(function(question) {
                question.addEventListener('click', function() {
                    var answer = this.nextElementSibling;
                    if (answer.style.display === 'none' || answer.style.display === '') {
                        answer.style.display = 'block';
                    } else {
                        answer.style.display = 'none';
                    }
                });
            });

            // Ocultar todas las respuestas inicialmente
            document.querySelectorAll('.faq-answer').forEach(function(answer) {
                answer.style.display = 'none';
            });
        });
    </script>
</body>
</html>
