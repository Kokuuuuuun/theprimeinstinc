<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../src/css/register-admin.css">
    <title>Regístrate</title>
</head>
<body>
    <div class="container-all">
        <div class="container-img">
            <img class="theimg" src="../img/regisimg.jpg" alt="rimg">
        </div>
        <form action="save_user.php" method="POST" onsubmit="return validateForm()">
            <div class="container">
                <h1>Regístrate</h1>
                <input class="input-w" type="text" id="name" name="name" placeholder="Nombre de usuario" >
                <input class="input-w" type="email" id="email" name="email" placeholder="Correo" >
                <input class="input-w" type="password" id="password" name="password" placeholder="Contraseña"  minlength="6">
                <input class="input-w" type="password" id="confirmPassword" placeholder="Confirmar contraseña" name="confirmPassword" >
                <input class="r-button" type="submit" value="Regístrate">
                <p>¿Ya tienes una cuenta? <a href="login-admin.php">Inicia sesión</a></p>
            </div>
        </form>
    </div>
</body>

<script>
function validateForm() {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirmPassword');
    
    if (password.value.length < 6) {
        alert('La contraseña debe tener al menos 6 caracteres');
        password.focus();
        return false;
    }
    
    if (password.value !== confirmPassword.value) {
        alert('Las contraseñas no coinciden');
        confirmPassword.focus();
        return false;
    }
    
    return true;
}

// Remove error class on input
document.querySelectorAll('.input-w').forEach(input => {
    input.addEventListener('input', function() {
        this.classList.remove('error');
    });
});
</script>
</html>