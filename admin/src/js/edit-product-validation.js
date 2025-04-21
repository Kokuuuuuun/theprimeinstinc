/** @format */

document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('productForm');
  const nombre = document.getElementById('nombre');
  const descripcion = document.getElementById('descripcion');
  const precio = document.getElementById('precio');

  form.addEventListener('submit', function (e) {
    let isValid = true;

    // Reset styles
    resetStyles(nombre);
    resetStyles(descripcion);
    resetStyles(precio);

    // Validate nombre
    if (nombre.value.trim() === '') {
      showError(nombre, 'El nombre del producto es obligatorio');
      isValid = false;
    }

    // Validate descripcion
    if (descripcion.value.trim() === '') {
      showError(descripcion, 'La descripción es obligatoria');
      isValid = false;
    }

    // Validate precio
    if (precio.value.trim() === '' || parseFloat(precio.value) <= 0) {
      showError(precio, 'El precio debe ser mayor que 0');
      isValid = false;
    }

    if (!isValid) {
      e.preventDefault();
    }
  });

  function showError(element, message) {
    element.style.borderColor = '#ff0000';
    const errorDiv = element.nextElementSibling;
    errorDiv.style.display = 'block';
    errorDiv.style.color = '#ff0000';
    errorDiv.textContent = message;
  }

  function resetStyles(element) {
    element.style.borderColor = '';
    const errorDiv = element.nextElementSibling;
    errorDiv.style.display = 'none';
  }
});
