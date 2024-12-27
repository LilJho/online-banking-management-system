let currentIndex = 0;

function showSlides() {
  const slides = document.querySelectorAll(".slide-show-card");

  slides.forEach((slide, index) => {
    slide.style.display = index === currentIndex ? "block" : "none";
  });

  currentIndex = (currentIndex + 1) % slides.length; // Loop back to the first slide
}

setInterval(showSlides, 3000); // Change image every 3 seconds

// Initialize the slideshow
showSlides();

let currentPage = 1;

// Fetch all offers from the server
async function fetchOffers(page = 1) {
  try {
    const response = await fetch(`get-all-offers.php?page=${page}`);

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const { offers, totalPages, currentPage } = await response.json();

    // Populate the offers container
    const offersContainer = document.querySelector(".offers-container");
    offersContainer.innerHTML = ""; // Clear any existing content

    offers.forEach((offer) => {
      const offerCard = document.createElement("div");
      offerCard.classList.add("offer-card");

      const img = document.createElement("img");
      img.src = offer.image_url;
      img.alt = offer.title;

      const title = document.createElement("p");
      title.textContent = offer.title;

      const buttonContainer = document.createElement("div");
      buttonContainer.classList.add("button-container");

      const updateBtn = document.createElement("button");
      updateBtn.textContent = "Update";
      updateBtn.classList.add("update-offer-btn");
      updateBtn.setAttribute("data-id", offer.id);

      const deleteBtn = document.createElement("button");
      deleteBtn.textContent = "Delete";
      deleteBtn.classList.add("delete-offer-btn");
      deleteBtn.setAttribute("data-id", offer.id);

      buttonContainer.appendChild(updateBtn);
      buttonContainer.appendChild(deleteBtn);

      offerCard.appendChild(img);
      offerCard.appendChild(title);

      // const user = JSON.parse(localStorage.getItem("user"));
      // if (user.isAdmin === 1) {
      //   offerCard.appendChild(buttonContainer);
      // }

      offersContainer.appendChild(offerCard);

      // Attach event listeners
      updateBtn.addEventListener("click", () => {
        document.getElementById("edit-offer").style.display = "block";
        document.getElementById("edit-offer-id").value = offer.id;
        document.getElementById("edit-image-preview").src = offer.image_url;
        document.getElementById("edit-image-preview").style.display = "block";
        document.getElementById("edit-image-title").value = offer.title;
        sessionStorage.setItem("offerId", offer.id);
      });

      deleteBtn.addEventListener("click", async () => {
        const formData = new FormData();
        formData.append("id", offer.id);

        try {
          const response = await fetch("delete-offer.php", {
            method: "POST",
            body: formData,
          });

          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }

          fetchOffers(currentPage); // Reload current page
        } catch (error) {
          console.error("Error deleting the image:", error);
        }
      });
    });

    // Update pagination controls
    updatePaginationControls(totalPages, currentPage);
  } catch (error) {
    console.error("Error fetching offers:", error);
  }
}

function updatePaginationControls(totalPages, currentPage) {
  const paginationContainer = document.querySelector(".pagination-container");
  paginationContainer.innerHTML = "";

  for (let i = 1; i <= totalPages; i++) {
    const pageButton = document.createElement("button");
    pageButton.textContent = i;
    pageButton.classList.add("pagination-button");
    if (i === currentPage) {
      pageButton.classList.add("active");
    }
    pageButton.addEventListener("click", () => {
      fetchOffers(i);
    });
    paginationContainer.appendChild(pageButton);
  }
}

window.onload = function () {
  fetchOffers();
};
