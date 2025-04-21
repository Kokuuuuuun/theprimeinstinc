/** @format */

document.querySelector('form').addEventListener('submit', function (event) {
  event.preventDefault(); // Evita el envío del formulario hasta que se valide
  let isValid = true;

  function showError(input, message) {
    input.style.border = '2px solid red';
    let errorText = input.nextElementSibling;
    if (!errorText || !errorText.classList.contains('error-text')) {
      errorText = document.createElement('p');
      errorText.classList.add('error-text');
      errorText.style.color = 'red';
      errorText.style.fontSize = '12px';
      input.parentNode.insertBefore(errorText, input.nextSibling);
    }
    errorText.textContent = message;
  }

  function clearError(input) {
    input.style.border = '';
    let errorText = input.nextElementSibling;
    if (errorText && errorText.classList.contains('error-text')) {
      errorText.remove();
    }
  }

  let fields = ['name','email','password','confirmPassword'];
  fields.forEach((id) => {
    let input = document.getElementById(id);
    if (input.value === '') {
      showError(input, 'Este campo es obligatorio');
      isValid = false;
    } else {
      clearError(input);
    }
  });

  let email = document.getElementById('email');
  let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email.value)) {
    showError(email, 'Ingrese un correo válido');
    isValid = false;
  }

  let password = document.getElementById('password');
  if (password.value.length < 6) {
    showError(password, 'La contraseña debe tener al menos 6 caracteres');
    isValid = false;
  }

  let confirmPassword = document.getElementById('confirmPassword');
  if (confirmPassword.value !== password.value) {
    showError(confirmPassword, 'Las contraseñas no coinciden');
    isValid = false;
  }

  if (isValid) {
    this.submit();
  }
});

document.getElementById('email').addEventListener('blur', async function() {
    const email = this.value;
    if (email) {
        const response = await fetch('check_email_ajax.php?email=' + encodeURIComponent(email));
        const data = await response.json();
        
        if (data.exists) {
            alert('Este correo electrónico ya está registrado');
            this.value = '';
            this.focus();
        }
    }
});
