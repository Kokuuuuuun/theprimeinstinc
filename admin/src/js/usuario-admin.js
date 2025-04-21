/** @format */

document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('editModal');
  const addModal = document.getElementById('addModal');
  const addBtn = document.getElementById('add-user-btn');
  const closeAddModal = document.getElementById('closeAddModal');
  const span = document.getElementsByClassName('close')[0];

  // Modal controls
  span.onclick = () => (modal.style.display = 'none');
  closeAddModal.onclick = () => (addModal.style.display = 'none');
  addBtn.onclick = () => {
    addModal.style.display = 'block';
    document.getElementById('addForm').reset();
  };

  window.onclick = (event) => {
    if (event.target == modal) modal.style.display = 'none';
    if (event.target == addModal) addModal.style.display = 'none';
  };

  // Edit user functionality
  document.querySelectorAll('.action-btn.edit').forEach((button) => {
    button.onclick = function (e) {
      e.preventDefault();
      const row = this.closest('tr');
      const id = this.closest('a').href.split('=')[1];

      document.getElementById('edit-id').value = id;
      document.getElementById('edit-nombre').value = row.cells[1].textContent;
      document.getElementById('edit-correo').value = row.cells[2].textContent;
      modal.style.display = 'block';
    };
  });

  // Add delete confirmation handler
  document.querySelectorAll('.action-btn.delete').forEach((button) => {
    button.onclick = function (e) {
      e.preventDefault();
      const row = this.closest('tr');
      const userName = row.cells[1].textContent;
      const deleteUrl = this.closest('a').href;

      if (
        confirm(`¿Estás seguro que deseas eliminar al usuario "${userName}"?`)
      ) {
        window.location.href = deleteUrl;
      }
    };
  });

  // Handle form submission
  document.getElementById('editForm').addEventListener('submit', function (e) {
    e.preventDefault();
    if (validateForm()) {
      const formData = new FormData(this);

      fetch('update_user.php', {
        method: 'POST',
        body: formData,
      })
        .then((response) => response.json()) // Changed to json()
        .then((data) => {
          if (data.success) {
            location.reload();
          } else {
            // Show specific error message
            if (data.error === 'El correo ya está registrado') {
              alert(
                'El correo electrónico ya está registrado por otro usuario'
              );
            } else {
              alert('Error al actualizar usuario: ' + data.error);
            }
          }
        })
        .catch((error) => {
          console.error('Error:', error);
          alert('Error al procesar la solicitud');
        });
    }
  });

  // Handle add form submission
  document.getElementById('addForm').addEventListener('submit', function (e) {
    e.preventDefault();
    if (validateAddForm()) {
      const formData = new FormData(this);

      fetch('add_user.php', {
        method: 'POST',
        body: formData,
      })
        .then((response) => response.json()) // Changed to json()
        .then((data) => {
          if (data.success) {
            location.reload();
          } else {
            // Show specific error message
            if (data.error === 'El correo ya está registrado') {
              alert(
                'El correo electrónico ya está registrado por otro usuario'
              );
            } else {
              alert('Error al añadir usuario: ' + data.error);
            }
          }
        })
        .catch((error) => {
          console.error('Error:', error);
          alert('Error al procesar la solicitud');
        });
    }
  });

  // Setup search functionality
  const searchInput = document.getElementById('search-input');
  searchInput.addEventListener('input', function () {
    const searchTerm = this.value.toLowerCase();
    const tableRows = document.querySelectorAll('.users-table tbody tr');

    tableRows.forEach((row) => {
      const nombre = row.cells[1].textContent.toLowerCase();
      const correo = row.cells[2].textContent.toLowerCase();
      const password = row.cells[3].textContent.toLowerCase();

      // Check if any of the fields contain the search term
      const matches =
        nombre.includes(searchTerm) ||
        correo.includes(searchTerm) ||
        password.includes(searchTerm);

      // Show/hide row based on match
      row.style.display = matches ? '' : 'none';
    });
  });

  function validateForm() {
    let isValid = true;
    const nombre = document.getElementById('edit-nombre');
    const correo = document.getElementById('edit-correo');
    const password = document.getElementById('edit-password');

    // Validate nombre
    if (!nombre.value.trim()) {
      nombre.classList.add('error');
      document.getElementById('nombre-error').classList.add('show');
      isValid = false;
    } else {
      nombre.classList.remove('error');
      document.getElementById('nombre-error').classList.remove('show');
    }

    // Validate correo
    if (!correo.value.trim()) {
      correo.classList.add('error');
      document.getElementById('correo-error').classList.add('show');
      isValid = false;
    } else if (!isValidEmail(correo.value.trim())) {
      correo.classList.add('error');
      document.getElementById('correo-error').textContent =
        'El correo no es válido';
      document.getElementById('correo-error').classList.add('show');
      isValid = false;
    } else {
      correo.classList.remove('error');
      document.getElementById('correo-error').classList.remove('show');
    }

    // Validate password if provided
    if (password.value.trim() && password.value.trim().length < 6) {
      password.classList.add('error');
      document.getElementById('password-error').classList.add('show');
      isValid = false;
    } else {
      password.classList.remove('error');
      document.getElementById('password-error').classList.remove('show');
    }

    return isValid;
  }

  function updateUser() {
    if (!validateForm()) {
      return; // Stop if validation fails
    }

    const formData = new FormData(document.getElementById('editForm'));
    const modal = document.getElementById('editModal');

    fetch('update_user.php', {
      method: 'POST',
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert('Usuario actualizado correctamente');
          modal.style.display = 'none';
          document.getElementById('editForm').reset();
          location.reload();
        } else {
          document.getElementById('editForm').reset();
          location.reload();
        }
      })
      .catch((error) => {
        document.getElementById('editForm').reset();
        location.reload();
      });
  }

  function validateAddForm() {
    let isValid = true;
    const nombre = document.getElementById('add-nombre');
    const correo = document.getElementById('add-correo');
    const password = document.getElementById('add-password');

    // Validate nombre
    if (!nombre.value.trim()) {
      nombre.classList.add('error');
      document.getElementById('add-nombre-error').classList.add('show');
      isValid = false;
    } else {
      nombre.classList.remove('error');
      document.getElementById('add-nombre-error').classList.remove('show');
    }

    // Validate correo
    if (!correo.value.trim()) {
      correo.classList.add('error');
      document.getElementById('add-correo-error').classList.add('show');
      isValid = false;
    } else if (!isValidEmail(correo.value.trim())) {
      correo.classList.add('error');
      document.getElementById('add-correo-error').textContent =
        'El correo no es válido';
      document.getElementById('add-correo-error').classList.add('show');
      isValid = false;
    } else {
      correo.classList.remove('error');
      document.getElementById('add-correo-error').classList.remove('show');
    }

    // Validate password
    if (!password.value.trim() || password.value.trim().length < 6) {
      password.classList.add('error');
      document.getElementById('add-password-error').classList.add('show');
      isValid = false;
    } else {
      password.classList.remove('error');
      document.getElementById('add-password-error').classList.remove('show');
    }

    return isValid;
  }

  function addUser() {
    const formData = new FormData(document.getElementById('addForm'));
    const addModal = document.getElementById('addModal');

    fetch('add_user.php', {
      method: 'POST',
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert('Usuario agregado correctamente');
          addModal.style.display = 'none';
          document.getElementById('addForm').reset();
          location.reload();
        } else {
          alert('Error al agregar el usuario');
        }
      })
      .catch((error) => {
        document.getElementById('addForm').reset();
        location.reload();
      });
  }

  // Add input event listeners to remove error state when user types
  document.getElementById('edit-nombre').addEventListener('input', function () {
    this.classList.remove('error');
    document.getElementById('nombre-error').classList.remove('show');
  });

  document.getElementById('edit-correo').addEventListener('input', function () {
    this.classList.remove('error');
    document.getElementById('correo-error').classList.remove('show');
  });

  document.getElementById('add-nombre').addEventListener('input', function () {
    this.classList.remove('error');
    document.getElementById('add-nombre-error').classList.remove('show');
  });

  document.getElementById('add-correo').addEventListener('input', function () {
    this.classList.remove('error');
    document.getElementById('add-correo-error').classList.remove('show');
  });

  document
    .getElementById('add-password')
    .addEventListener('input', function () {
      this.classList.remove('error');
      document.getElementById('add-password-error').classList.remove('show');
    });

  // Add user dropdown functionality
  document.addEventListener('DOMContentLoaded', function () {
    const userIcon = document.getElementById('user-icon');
    const userDropdown = document.getElementById('user-dropdown');

    // Toggle dropdown when clicking user icon
    userIcon.addEventListener('click', function (e) {
      e.stopPropagation();
      userDropdown.classList.toggle('active');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
      if (!userDropdown.contains(e.target) && !userIcon.contains(e.target)) {
        userDropdown.classList.remove('active');
      }
    });
  });

  // Email validation helper function
  function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  // Add CSS styles to show/hide error messages
  document.head.insertAdjacentHTML(
    'beforeend',
    `
    <style>
        .error-message {
            display: none;
            color: red;
            font-size: 0.8em;
            margin-top: 5px;
        }
    </style>
`
  );
});
