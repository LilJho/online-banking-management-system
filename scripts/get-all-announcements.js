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
 
             // Append the image and title to the offer card
             announcementCard.appendChild(title);
             announcementCard.appendChild(description);
 
             // Append the offer card to the container
             announcementsContainer.appendChild(announcementCard);
             console.log({announcementsContainer})
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
