/** @format */

document.addEventListener('DOMContentLoaded', () => {
  // Menu toggle functionality
  document
    .getElementById('menu-toggle')
    ?.addEventListener('click', function () {
      document.getElementById('menu-links')?.classList.toggle('active');
      this.classList.toggle('m-active');
    });

  // Star rating functionality
  const stars = document.querySelectorAll('.star');
  let selectedRating = 0;

  stars.forEach((star, index) => {
    star.addEventListener('click', () => {
      stars.forEach((s) => s.classList.remove('active'));
      for (let i = 0; i <= index; i++) {
        stars[i].classList.add('active');
      }
      selectedRating = index + 1;
    });
  });

  // Submit opinion
  document.querySelector('.send-icon').addEventListener('click', () => {
    const commentText = document.querySelector('#input-review').value;
    if (!commentText.trim()) {
      alert('Por favor escribe una opinión');
      return;
    }
    if (selectedRating === 0) {
      alert('Por favor selecciona una calificación');
      return;
    }

    // Create and submit form
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'save_opinion.php';

    const opinionInput = document.createElement('input');
    opinionInput.type = 'hidden';
    opinionInput.name = 'comment';
    opinionInput.value = commentText;

    const ratingInput = document.createElement('input');
    ratingInput.type = 'hidden';
    ratingInput.name = 'rating';
    ratingInput.value = selectedRating;

    const usernameInput = document.createElement('input');
    usernameInput.type = 'hidden';
    usernameInput.name = 'username';
    usernameInput.value = document.querySelector('.username').textContent; // Get logged-in username

    form.appendChild(opinionInput);
    form.appendChild(ratingInput);
    form.appendChild(usernameInput);
    document.body.appendChild(form);
    form.submit();
  });
});

function deleteComment(commentId) {
  if (confirm('¿Estás seguro de que deseas eliminar este comentario?')) {
    fetch('delete_comment.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'id=' + commentId,
    })
      .then((response) => response.text())
      .then((data) => {
        if (data === 'success') {
          const commentElement = document.querySelector(
            `[data-comment-id="${commentId}"]`
          );
          if (commentElement) {
            commentElement.remove();
          }
        }
      })
      .catch((error) => {
        console.error('Error:', error);
        alert('Comentario eliminado');
      });
  }
}

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
