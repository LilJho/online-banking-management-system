// Fetch all offers from the server
async function fetchAnnouncements() {
    try {
        const response = await fetch('/get-all-announcements.php'); // Adjust the path to your PHP file

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const announcements = await response.json();
        console.log('Announcements:', announcements);

         // Populate the offers container
         const announcementsContainer = document.querySelector('.announcements-container');
         announcementsContainer.innerHTML = ''; // Clear any existing content
 
         announcements.forEach(announcement => {
             // Create the offer card dynamically
             const announcementCard = document.createElement('div');
             announcementCard.classList.add('announcement-card');
 
             // Create the title element
             const title = document.createElement('h4');
             title.textContent = announcement.title; // Use the title from the fetched data

             // Create the title element
             const description = document.createElement('p');
             description.textContent = announcement.description; // Use the title from the fetched data

             const buttonContainer = document.createElement('div');
            buttonContainer.classList.add('button-container');

             // Create Update Button
            const updateBtn = document.createElement('button');
            updateBtn.textContent = "Update";
            updateBtn.classList.add('update-announcement-btn');
            updateBtn.setAttribute('id', 'update-offer-btn');
            updateBtn.setAttribute('data-id', announcements.id);

            // Create Delete Button
            const deleteBtn = document.createElement('button');
            deleteBtn.textContent = "Delete";
            deleteBtn.classList.add('delete-announcement-btn');
            deleteBtn.setAttribute('data-id', announcements.id);

            buttonContainer.appendChild(updateBtn)
            buttonContainer.appendChild(deleteBtn)
 
             // Append the image and title to the offer card
             announcementCard.appendChild(title);
             announcementCard.appendChild(description);
             announcementCard.appendChild(buttonContainer);
 
             // Append the offer card to the container
             announcementsContainer.appendChild(announcementCard);

             updateBtn.addEventListener('click', () => {
                // console.log({announcement})
                document.getElementById('update-announcement').style.display = 'block';
                // Populate the modal with the offer details
                // document.getElementById('edit-offer-id').value = offer.id;
                document.getElementById('edit-announcement-title').value = announcement.title;
                document.getElementById('edit-announcement-description').value = announcement.description;
                sessionStorage.setItem("announcementId", announcement.id)
            });
            deleteBtn.addEventListener('click', async() => {

                const formData = new FormData();
                formData.append('id', announcement.id);
                
                    try {
                        const response = await fetch('/delete-announcement.php', { // Replace with your actual endpoint
                            method: 'POST',
                            body: formData,
                        });
                
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                
                        const result = await response.json(); // Parse the JSON response
                        // console.log(result)
                        // console.log('Image URL:', result.imageUrl);
                        // console.log('Image Title:', result.imageTitle);
                        window.location.reload
                    } catch (error) {
                        console.error('Error delete the announcement:', error);
                    } finally {
                        fetchAnnouncements();
                    }
             })
         });
    } catch (error) {
        console.error('Error fetching announcements:', error);
    }
}

// Automatically fetch offers when the page loads
window.onload = function () {
    fetchAnnouncements()

    const user = JSON.parse(localStorage.getItem("user"));
  const fullName = user.first_name === "admin" ? `${user.first_name}` : `${user.first_name} ${user.last_name}`

  const createAnnouncementBtn = document.getElementById('create-announcement-btn');
  createAnnouncementBtn.style.display = parseInt(user.isAdmin) === 1 ? "block" : 'none'

  const accountsLink = document.getElementById('account-page-link');
  accountsLink.style.display = parseInt(user.isAdmin) === 1 ? "block" : 'none'

  const fullNameContainer = document.getElementById("full-name"); 
  fullNameContainer.textContent = fullName
};
