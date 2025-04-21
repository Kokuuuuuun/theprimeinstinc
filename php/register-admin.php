<?php
// Generar nonce para CSP
$nonce = base64_encode(random_bytes(16));
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-$nonce'");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="../src/css/register-admin.css">
    <title>Regístrate</title>
</head>
<body>
    <div class="container-all">
        <div class="container-img">
            <img class="theimg" src="../img/regisimg.jpg" alt="Imagen de registro" loading="lazy">
        </div>
        <form action="save_user.php" method="POST" onsubmit="return validateForm(event)">
            <div class="container">
                <h1>Regístrate</h1>
                <input class="input-w" type="text" id="name" name="name" placeholder="Nombre de usuario" required
                       pattern="[\p{L}\s]{2,50}" title="Solo letras y espacios (2-50 caracteres)">
                <input class="input-w" type="email" id="email" name="email" placeholder="Correo" required
                       pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$">
                <input class="input-w" type="password" id="password" name="password" placeholder="Contraseña" 
                       required minlength="6" autocomplete="new-password">
                <input class="input-w" type="password" id="confirmPassword" placeholder="Confirmar contraseña" 
                       name="confirmPassword" required autocomplete="new-password">
                <input class="r-button" type="submit" value="Regístrate">
                <p>¿Ya tienes una cuenta? <a href="login-admin.php">Inicia sesión</a></p>
            </div>
        </form>
    </div>
    <a href="manual.php" class="manual-btn">Manual de Usuario</a>

    <script nonce="<?= $nonce ?>">
    function validateForm(event) {
        const form = event.target;
        const inputs = form.elements;
        
        // Validación mejorada del nombre
        const nameRegex = /^[\p{L}\s]{2,50}$/u;
        if (!nameRegex.test(inputs.name.value)) {
            alert('Nombre inválido. Solo letras y espacios (2-50 caracteres)');
            inputs.name.focus();
            return false;
        }

        // Validación de email estricta
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailRegex.test(inputs.email.value)) {
            alert('Formato de correo electrónico inválido');
            inputs.email.focus();
            return false;
        }

        // Validación de contraseña
        if (inputs.password.value.length < 6) {
            alert('La contraseña debe tener al menos 6 caracteres');
            inputs.password.focus();
            return false;
        }

        if (inputs.password.value !== inputs.confirmPassword.value) {
            alert('Las contraseñas no coinciden');
            inputs.confirmPassword.focus();
            return false;
        }

        return true;
    }

    // Limpiar errores al modificar
    document.querySelectorAll('.input-w').forEach(input => {
        input.addEventListener('input', function() {
            this.setCustomValidity('');
        });
    });
    </script>
</body>
</html>
