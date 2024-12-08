
// Get the modal
const modal = document.getElementById("create-offer");
        // Get the button that opens the modal
        const updateButton = document.getElementById("create-offer-btn");
        // Get the <span> element that closes the modal
        const closeButton = document.getElementById("close-create-offer-btn");

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

        document.getElementById('image-upload').addEventListener('change', function (event) {
            const file = event.target.files[0]; // Get the uploaded file
            const preview = document.getElementById('image-preview'); // Get the preview element
            const container = document.getElementById('image-preview-container'); // Preview container
        
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function (e) {
                    preview.src = e.target.result; // Set the image source to the file data
                    preview.style.display = 'block'; // Make the image visible
                };
        
                reader.readAsDataURL(file); // Read the file as a data URL
            } else {
                preview.src = ''; // Clear the image source
                preview.style.display = 'none'; // Hide the preview
            }
        });
        

        document.querySelector('.create-offer-form').addEventListener('submit', async function (event) {
            event.preventDefault(); // Prevent default form submission behavior
        
            const imageInput = document.getElementById('image-upload');
            const titleInput = document.getElementById('image-title');
            const modal = document.getElementById("create-offer");
        
            const file = imageInput.files[0];
            const title = titleInput.value;
        
            if (!file || !title) {
                console.error('Image or title is missing.');
                return;
            }
        
            // Prepare form data
            const formData = new FormData();
            formData.append('offer-image', file);
            formData.append('image-title', title);
        
            try {
                const response = await fetch('/create-offer.php', { // Replace with your actual endpoint
                    method: 'POST',
                    body: formData,
                });
        
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
        
                const result = await response.json(); // Parse the JSON response
                console.log('Image URL:', result.imageUrl);
                console.log('Image Title:', result.imageTitle);
                window.location.reload
            } catch (error) {
                console.error('Error uploading the image:', error);
            } finally {
               
            modal.style.display = "none";
            fetchOffers();
            }
        });
        