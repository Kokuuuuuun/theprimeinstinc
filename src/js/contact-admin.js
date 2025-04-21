/** @format */

document.getElementById('menu-toggle').addEventListener('click', function () {
  document.getElementById('menu-links').classList.toggle('active');
});

document.getElementById('menu-toggle').addEventListener('click', function () {
  document.getElementById('menu-toggle').classList.toggle('m-active');
});


document.querySelector('.contact-form button')
  .addEventListener('click', function (event) {
    event.preventDefault(); // Evita el envío del formulario hasta que se valide
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

    let inputs = document.querySelectorAll('.contact-input, .contact-textarea');
    inputs.forEach((input) => {
      if (input.value.trim() === '') {
        showError(input, 'Este campo es obligatorio');
        isValid = false;
      } else {
        clearError(input);
      }
    });

    let email = document.querySelector(".contact-input[type='email']");
    if (email) {
      let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email.value.trim())) {
        showError(email, 'Ingrese un correo válido');
        isValid = false;
      }
    }

    if (isValid) {
      document.querySelector('.contact-form').submit();
    }
  });

  // Add user dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    const userIcon = document.getElementById('user-icon');
    const userDropdown = document.getElementById('user-dropdown');
    
    // Toggle dropdown when clicking user icon
    userIcon.addEventListener('click', function(e) {
        e.stopPropagation();
        userDropdown.classList.toggle('active');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!userDropdown.contains(e.target) && !userIcon.contains(e.target)) {
            userDropdown.classList.remove('active');
        }
    });
});