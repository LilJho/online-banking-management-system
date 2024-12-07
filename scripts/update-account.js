document.querySelector('form').addEventListener('submit', async function (event) {
    event.preventDefault(); // Prevent form from submitting normally

    const userData = JSON.parse(localStorage.getItem('user'));

    const firstName = document.querySelector('input[name="firstname"]').value;
    const middleName = document.querySelector('input[name="middlename"]').value;
    const lastName = document.querySelector('input[name="lastname"]').value;
    const birthDate = document.querySelector('input[name="birthdate"]').value;
    const phoneNumber = document.querySelector('input[name="phonenumber"]').value;

    const formData = {
        firstName: firstName,
        middleName: middleName,
        lastName: lastName,
        birthDate: birthDate,
        phoneNumber: phoneNumber,
        userId: userData.id
    };

    try {
        const response = await fetch('update-account.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json', // Use JSON content type
            },
            body: JSON.stringify(formData) // Send the data as JSON
        });

        if (!response.ok) {
            // Log the error if the response is not OK
            console.error('Server error:', response.status, await response.text());
            return;
        }

        const contentType = response.headers.get("Content-Type");
        const responseText = await response.text(); // Read the response body as text
        console.log("Response text:", responseText); // Log the raw response for debugging

        if (contentType && contentType.includes("application/json")) {
            const data = JSON.parse(responseText); // Parse it manually
            console.log(data);

            // Handle the response (e.g., show success message or update UI)
            if (data.success) {
                userData.first_name = formData.firstName
                userData.middle_name = formData.middleName
                userData.last_name = formData.lastName
                userData.birth_date = formData.birthDate
                userData.phone_number = formData.phoneNumber
                localStorage.setItem('user', JSON.stringify(userData));
                alert('User updated successfully!');
                window.location.href = 'dashboard.php';  // Example redirect after success
            } else {
                alert(data.message || 'An error occurred. Please try again.');
            }
        } else {
            console.error("Unexpected response type:", responseText);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    }
});



// Get the modal
const modal = document.getElementById("updateModal");
        // Get the button that opens the modal
        const updateButton = document.getElementById("updateButton");
        // Get the <span> element that closes the modal
        const closeButton = document.getElementById("closeModal");

        // When the user clicks the button, open the modal
        updateButton.onclick = function() {
            modal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal
        closeButton.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside the modal, close it
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            } 
        }
        
window.onload = function() {
    // Check if the user data exists in localStorage
    const userData = JSON.parse(localStorage.getItem('user'));

    // If user data exists, populate the form fields
    if (userData) {
        document.getElementById('firstname').value = userData.first_name || '';
        document.getElementById('middlename').value = userData.middle_name || '';
        document.getElementById('lastname').value = userData.last_name || '';
        document.getElementById('birthdate').value = userData.birth_date || '';
        document.getElementById('phonenumber').value = userData.phone_number || '';
        document.getElementById('email').value = userData.email || '';

        document.getElementById('full-name').textContent = userData.first_name + ' ' + userData.last_name;
        document.getElementById('birthday').textContent = userData.birth_date || '';
        document.getElementById('gender').textContent = userData.gender || '';
        document.getElementById('address').textContent = userData.address || '';
        document.getElementById('phone-number').textContent = userData.phone_number || '';
        document.getElementById('email').textContent = userData.email || '';
        
        // Set verification status
        const statusDiv = document.getElementById('status');
        if (userData.is_verified == 1) {
            statusDiv.className = 'verified';
            statusDiv.innerHTML = '<span>verified</span>';
        } else {
            statusDiv.className = 'not-verified';
            statusDiv.innerHTML = '<span>not-verified</span>';
        }
    }
};