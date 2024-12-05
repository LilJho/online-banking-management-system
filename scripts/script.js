const signUpButton = document.getElementById('signUp');
const signInButton = document.getElementById('signIn');
const container = document.getElementById('container');

signUpButton.addEventListener('click', () => {
    container.classList.add("right-panel-active");
});

signInButton.addEventListener('click', () => {
    container.classList.remove("right-panel-active");
});

// Get today's date in YYYY-MM-DD format
const today = new Date().toISOString().split('T')[0];   
  
// Set the max attribute to today's date
document.getElementById('birthdate').setAttribute('max', today);

document.querySelector('#loginForm').addEventListener('submit', async function (event) {
    event.preventDefault(); // Prevent form from submitting normally

    const email = document.querySelector('input[name="login_email"]').value;
    const password = document.querySelector('input[name="login_pass"]').value;

    const formData = {
        login_email: email,
        login_pass: password
    };

    try {
        const response = await fetch('login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams(formData)
        });

        const data = await response.json();
        console.log({ data });

        if (data.success) {
            // Handle successful login (e.g., redirect to dashboard)
            localStorage.setItem('user', JSON.stringify(data.user));
            // alert('Login successful!');
            window.location.href = 'dashboard.php';
        } else {
            // Handle login failure (show error message)
            alert(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    }
});

