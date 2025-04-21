/** @format */

document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('form');

  form.addEventListener('submit', function (e) {
    const correo = document.getElementById('correo').value;
    const contraseña = document.getElementById('contraseña').value;

    if (!correo || !contraseña) {
      e.preventDefault();
      alert('Por favor complete todos los campos');
      return;
    }

    if (!isValidEmail(correo)) {
      e.preventDefault();
      alert('Por favor ingrese un correo válido');
      return;
    }
  });

  function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }
});

document.querySelector('form').addEventListener('submit', function (event) {
  event.preventDefault();
  let isValid = true;

  function showError(input, message) {
    input.style.border = '2px solid red';
    input.style.borderRadius = '5px';
    input.style.padding = '8px';

    let errorText = input.nextElementSibling;
    if (!errorText || !errorText.classList.contains('error-text')) {
      errorText = document.createElement('p');
      errorText.classList.add('error-text');
      errorText.style.color = 'red';
      errorText.style.fontSize = '14px';
      errorText.style.margin = '5px 0 10px';
      errorText.style.textAlign = 'left';
      input.parentNode.insertBefore(errorText, input.nextSibling);
    }
    errorText.textContent = message;
  }

  function clearError(input) {
    input.style.border = '1px solid #ccc';
    let errorText = input.nextElementSibling;
    if (errorText && errorText.classList.contains('error-text')) {
      errorText.remove();
    }
  }

  let email = document.getElementById('email');
  if (email.value.trim() === '') {
    showError(email, 'Este campo es obligatorio');
    isValid = false;
  } else {
    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email.value.trim())) {
      showError(email, 'Ingrese un correo válido');
      isValid = false;
    } else {
      clearError(email);
    }
  }

  let password = document.getElementById('password');
  if (password.value.trim() === '') {
    showError(password, 'Este campo es obligatorio');
    isValid = false;
  } else if (password.value.length < 6) {
    showError(password, 'La contraseña debe tener al menos 6 caracteres');
    isValid = false;
  } else {
    clearError(password);
  }

  if (isValid) {
    this.submit();
  }
});
