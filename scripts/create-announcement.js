
// Get the modal
const modal = document.getElementById("create-announcement");
        // Get the button that opens the modal
        const updateButton = document.getElementById("create-announcement-btn");
        // Get the <span> element that closes the modal
        const closeButton = document.getElementById("close-create-announcement-btn");

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
        

        document.querySelector('.create-announcement-form').addEventListener('submit', async function (event) {
            event.preventDefault(); // Prevent default form submission behavior
        
            const titleInput = document.getElementById('announcement-title');
            const descriptionInput = document.getElementById('announcement-description');
            const modal = document.getElementById("create-announcement");
        
            const title = titleInput.value;
            const description = descriptionInput.value;
        
            if (!description || !title) {
                console.error('Description or title is missing.');
                return;
            }
        
            // Prepare form data
            const formData = new FormData();
            formData.append('title', title);
            formData.append('description', description);
        
            try {
                const response = await fetch('/create-announcement.php', {
                    method: 'POST',
                    body: formData,
                });
        
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
        
                const result = await response.json(); // Parse the JSON response
                console.log('Announcement Title:', result.announcementTitle);
                console.log('Announcement Description:', result.announcementDescription);
        
                // Refresh the announcements
                fetchAnnouncements();
            } catch (error) {
                console.error('Error creating announcement:', error);
        
                // Additional debugging for non-JSON responses
                if (response.headers.get('Content-Type') !== 'application/json') {
                    const text = await response.text();
                    console.error('Non-JSON response:', text);
                }
            } finally {
                modal.style.display = "none";
            }
        });
        
        