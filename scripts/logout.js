document.getElementById('logoutButton').addEventListener('click', function() {
    // Remove user data from localStorage
    localStorage.removeItem('user');

    // Redirect to authentication.php
    window.location.href = 'authentication.php';
});