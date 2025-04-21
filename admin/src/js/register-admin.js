/** @format */

document.querySelector('form').addEventListener('submit', function (event) {
  event.preventDefault(); // Evita el envío del formulario hasta que se valide
  if (validateForm()) {
    this.submit();
  }
});

function validateForm() {
  let isValid = true;

  // Función para mostrar error
  function showError(inputId, message) {
    const input = document.getElementById(inputId);
    const errorSpan = document.getElementById(inputId + '-error');
    input.classList.add('error');
    errorSpan.textContent = message;
    isValid = false;
  }

  // Función para limpiar error
  function clearError(inputId) {
    const input = document.getElementById(inputId);
    const errorSpan = document.getElementById(inputId + '-error');
    input.classList.remove('error');
    errorSpan.textContent = '';
  }

  // Validar campos vacíos
  const fields = ['name', 'email', 'password', 'confirmPassword'];
  fields.forEach((field) => {
    const input = document.getElementById(field);
    if (!input.value.trim()) {
      showError(field, 'Este campo es obligatorio');
    } else {
      clearError(field);
    }
  });

  // Validar email
  const email = document.getElementById('email');
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (email.value && !emailRegex.test(email.value)) {
    showError('email', 'Ingrese un correo válido');
  }

  // Validar contraseña
  const password = document.getElementById('password');
  if (password.value && password.value.length < 6) {
    showError('password', 'La contraseña debe tener al menos 6 caracteres');
  }

  // Validar confirmación de contraseña
  const confirmPassword = document.getElementById('confirmPassword');
  if (password.value !== confirmPassword.value) {
    showError('confirm', 'Las contraseñas no coinciden');
  }

  return isValid;
}

// Event listeners para limpiar errores al escribir
document.querySelectorAll('.input-w').forEach((input) => {
  input.addEventListener('input', () => {
    const errorSpan = document.getElementById(input.id + '-error');
    input.classList.remove('error');
    errorSpan.textContent = '';
  });
});

document.getElementById('email').addEventListener('blur', async function () {
  const email = this.value;
  if (email) {
    const response = await fetch(
      'check_email_ajax.php?email=' + encodeURIComponent(email)
    );
    const data = await response.json();

    if (data.exists) {
      alert('Este correo electrónico ya está registrado');
      this.value = '';
      this.focus();
    }
  }
});
