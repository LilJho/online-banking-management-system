// Fetch all offers from the server
async function fetchAccounts() {
    try {
        const response = await fetch('/get-all-user-accounts.php'); // Adjust the path to your PHP file

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const accounts = await response.json();
        console.log('Accounts:', accounts);

         // Populate the offers container
         const tablesContainer = document.querySelector('.my_table');
        //  offersContainer.innerHTML = ''; // Clear any existing content
 
        accounts.forEach(account => {
             // Create the offer card dynamically
             const tableRowCard = document.createElement('tr');

            // Create the table data for each piece of information
            const fullNameData = document.createElement('td');
            fullNameData.textContent = `${account.first_name} ${account.middle_name} ${account.last_name}`;

            const genderData = document.createElement('td');
            genderData.textContent = account.gender;

            const emailData = document.createElement('td');
            emailData.textContent = account.email;

            const addressData = document.createElement('td');
            addressData.textContent = account.address;

            const phoneNumberData = document.createElement('td');
            phoneNumberData.textContent = account.phone_number;

            const birthdayData = document.createElement('td');
            birthdayData.textContent = account.birth_date;

            const statusData = document.createElement('td');
            statusData.textContent = parseInt(account.is_verified) === 1 ? "Verified" : "Not Verified";

            // Create buttons and wrap them in separate <td> elements
            const archiveData = document.createElement('td');
            const archiveButton = document.createElement('button');
            archiveButton.textContent = "Archive";
            archiveData.appendChild(archiveButton);

            const blockData = document.createElement('td');
            const blockButton = document.createElement('button');
            blockButton.textContent = "Block";
            blockData.appendChild(blockButton);

            // Append all data and buttons to the row
            tableRowCard.appendChild(fullNameData);
            tableRowCard.appendChild(genderData);
            tableRowCard.appendChild(emailData);
            tableRowCard.appendChild(addressData);
            tableRowCard.appendChild(phoneNumberData);
            tableRowCard.appendChild(birthdayData);
            tableRowCard.appendChild(statusData);
            tableRowCard.appendChild(archiveData);  // Archive button in its own td
            tableRowCard.appendChild(blockData);    // Block button in its own td

 
             // Append the offer card to the container
             tablesContainer.appendChild(tableRowCard);
         });
    } catch (error) {
        console.error('Error fetching offers:', error);
    }
}

// Automatically fetch offers when the page loads
window.onload = function () {
    fetchAccounts();
};
