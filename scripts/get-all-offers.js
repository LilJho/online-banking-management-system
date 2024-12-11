// Fetch all offers from the server
async function fetchOffers() {
    try {
        const response = await fetch('/get-all-offers.php'); // Adjust the path to your PHP file

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const offers = await response.json();
        console.log('Offers:', offers);

         // Populate the offers container
         const offersContainer = document.querySelector('.offers-container');
         offersContainer.innerHTML = ''; // Clear any existing content
 
         offers.forEach(offer => {
             // Create the offer card dynamically
             const offerCard = document.createElement('div');
             offerCard.classList.add('offer-card');
 
             // Create the image element
             const img = document.createElement('img');
             img.src = offer.image_url; // Use the image URL from the fetched data
             img.alt = offer.title; // Use the title as the alt text
 
             // Create the title element
             const title = document.createElement('p');
             title.textContent = offer.title; // Use the title from the fetched data

            const buttonContainer = document.createElement('div');
            buttonContainer.classList.add('button-container');


             // Create Update Button
            const updateBtn = document.createElement('button');
            updateBtn.textContent = "Update";
            updateBtn.classList.add('update-offer-btn');
            updateBtn.setAttribute('id', 'update-offer-btn');
            updateBtn.setAttribute('data-id', offer.id);

            // Create Delete Button
            const deleteBtn = document.createElement('button');
            deleteBtn.textContent = "Delete";
            deleteBtn.classList.add('delete-offer-btn');
            deleteBtn.setAttribute('data-id', offer.id);

            buttonContainer.appendChild(updateBtn)
            buttonContainer.appendChild(deleteBtn)
 
             // Append the image and title to the offer card
             offerCard.appendChild(img);
             offerCard.appendChild(title);
             offerCard.appendChild(buttonContainer);
 
             // Append the offer card to the container
             offersContainer.appendChild(offerCard);

              // Attach event listener to open the modal
    updateBtn.addEventListener('click', () => {
        document.getElementById('edit-offer').style.display = 'block';
        // Populate the modal with the offer details
        document.getElementById('edit-offer-id').value = offer.id;
        document.getElementById('edit-image-preview').src = offer.image_url;
        document.getElementById('edit-image-preview').style.display = 'block';
        document.getElementById('edit-image-title').value = offer.title;
        sessionStorage.setItem("offerId", offer.id)
    });
         });
        //  attachEditListeners();
    } catch (error) {
        console.error('Error fetching offers:', error);
    }
}

// Automatically fetch offers when the page loads
window.onload = function () {
    fetchOffers();
    
    const user = JSON.parse(localStorage.getItem("user"));
    const fullName = user.first_name === "admin" ? `${user.first_name}` : `${user.first_name} ${user.last_name}`

    const createOfferBtn = document.getElementById('create-offer-btn');
    createOfferBtn.style.display = parseInt(user.isAdmin) === 1 ? "block" : 'none'

    const accountsLink = document.getElementById('account-page-link');
    accountsLink.style.display = parseInt(user.isAdmin) === 1 ? "block" : 'none'
  
    const fullNameContainer = document.getElementById("full-name");
    fullNameContainer.textContent = fullName
};


// function attachEditListeners() {
//     document.querySelectorAll('.edit-offer-btn').forEach((btn) => {
//         btn.addEventListener('click', async function () {
//             const offerId = this.dataset.id;
//             const response = await fetch(`/get-offer.php?id=${offerId}`);
//             const data = await response.json();

//             // Populate modal with current offer data
//             document.getElementById('edit-offer-id').value = offerId;
//             document.getElementById('edit-image-title').value = data.title;
//             document.getElementById('edit-image-preview').src = data.image_url;
//             document.getElementById('edit-image-preview').style.display = 'block';

//             // Show modal
//             document.getElementById('edit-offer').style.display = 'block';
//         });
//     });
// }

// document.querySelector('.edit-offer-form').addEventListener('submit', async function (event) {
//     event.preventDefault();
    
//     const offerId = document.getElementById('edit-offer-id').value;
//     const imageTitle = document.getElementById('edit-image-title').value;
//     const file = document.getElementById('edit-image-upload').files[0];
//     const formData = new FormData();

//     formData.append('id', offerId);
//     formData.append('image-title', imageTitle);
//     if (file) formData.append('edit-offer-image', file);

//     try {
//         const response = await fetch('/update-offer.php', {
//             method: 'POST',
//             body: formData,
//         });
//         const result = await response.json();

//         if (response.ok) {
//             alert('Offer updated successfully!');
//             window.location.reload();
//         } else {
//             console.error('Error updating the offer:', result.error);
//         }
//     } catch (error) {
//         console.error('Error:', error);
//     }
// });