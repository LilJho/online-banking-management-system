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
 
             // Append the image and title to the offer card
             offerCard.appendChild(img);
             offerCard.appendChild(title);
 
             // Append the offer card to the container
             offersContainer.appendChild(offerCard);
         });
    } catch (error) {
        console.error('Error fetching offers:', error);
    }
}

// Automatically fetch offers when the page loads
window.onload = function () {
    fetchOffers();
};
