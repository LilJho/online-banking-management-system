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