/** @format */

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
