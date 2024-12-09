document.querySelector('.create-user-form').addEventListener('submit', async function (event) {
    event.preventDefault(); // Prevent default form submission behavior

    const firstname = document.getElementById('firstname').value;
    const middlename = document.getElementById('middlename').value;
    const lastname = document.getElementById("lastname").value;
    const address = document.getElementById("address").value;
    const gender = document.getElementById("gender").value;
    const account = document.getElementById("account").value;
    const balance = document.getElementById("balance").value;
    const birthdate = document.getElementById("birthdate").value;
    const phonenumber = document.getElementById("phonenumber").value;
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;

    // Prepare form data
    const formData = new FormData();
    formData.append('firstName', firstname);
    formData.append('middleName', middlename);
    formData.append('lastName', lastname);
    formData.append('address', address);
    formData.append('gender', gender);
    formData.append('account', account);
    formData.append('balance', balance);
    formData.append('birthDate', birthdate);
    formData.append('phoneNumber', phonenumber);
    formData.append('email', email);
    formData.append('password', password);

    try {
        const response = await fetch('/create-user.php', {
            method: 'POST',
            body: formData,
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json(); // Parse the JSON response
        console.log(result);

        // Refresh the announcements
        fetchAccounts();
    } catch (error) {
        console.error('Error creating announcement:', error);

        // Additional debugging for non-JSON responses
        if (error.response) {
            console.error('Error response:', error.response);
        } else {
            console.error('Error message:', error.message);
        }
    } finally {
        modal.style.display = "none";
    }
});
