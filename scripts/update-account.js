document
  .querySelector("form")
  .addEventListener("submit", async function (event) {
    event.preventDefault(); // Prevent form from submitting normally

    const userData = JSON.parse(localStorage.getItem("user"));

    const firstName = document.querySelector('input[name="firstname"]').value;
    const middleName = document.querySelector('input[name="middlename"]').value;
    const lastName = document.querySelector('input[name="lastname"]').value;
    const birthDate = document.querySelector('input[name="birthdate"]').value;
    const phoneNumber = document.querySelector(
      'input[name="phonenumber"]'
    ).value;

    const formData = {
      firstName: firstName,
      middleName: middleName,
      lastName: lastName,
      birthDate: birthDate,
      phoneNumber: phoneNumber,
      userId: userData.id,
    };

    try {
      const response = await fetch("update-account.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json", // Use JSON content type
        },
        body: JSON.stringify(formData), // Send the data as JSON
      });

      if (!response.ok) {
        // Log the error if the response is not OK
        console.error("Server error:", response.status, await response.text());
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
          userData.first_name = formData.firstName;
          userData.middle_name = formData.middleName;
          userData.last_name = formData.lastName;
          userData.birth_date = formData.birthDate;
          userData.phone_number = formData.phoneNumber;
          localStorage.setItem("user", JSON.stringify(userData));
          alert("User updated successfully!");
          window.location.reload(); // Example redirect after success
        } else {
          alert(data.message || "An error occurred. Please try again.");
        }
      } else {
        console.error("Unexpected response type:", responseText);
      }
    } catch (error) {
      console.error("Error:", error);
      alert("An error occurred. Please try again.");
    } finally {
      const modal = document.getElementById("updateModal");
      if (modal) {
        modal.style.display = "none";
      }
    }
  });

// Get the modal
const modal = document.getElementById("updateModal");
// Get the button that opens the modal
const updateButton = document.getElementById("updateButton");
// Get the <span> element that closes the modal
const closeButton = document.getElementById("closeModal");

// When the user clicks the button, open the modal
updateButton.onclick = function () {
  modal.style.display = "block";
};

// When the user clicks on <span> (x), close the modal
closeButton.onclick = function () {
  modal.style.display = "none";
};

// When the user clicks anywhere outside the modal, close it
window.onclick = function (event) {
  if (event.target === modal) {
    modal.style.display = "none";
  }
};

window.onload = function () {
  // Check if the user data exists in localStorage
  const userData = JSON.parse(localStorage.getItem("user"));

  // If user data exists, populate the form fields
  if (userData) {
    document.getElementById("firstname").value = userData.first_name || "";
    document.getElementById("middlename").value = userData.middle_name || "";
    document.getElementById("lastname").value = userData.last_name || "";
    document.getElementById("birthdate").value = userData.birth_date || "";
    document.getElementById("phonenumber").value = userData.phone_number || "";
    document.getElementById("email").value = userData.email || "";

    document.getElementById("fullname").textContent =
      userData.first_name === "admin"
        ? userData.first_name
        : userData.first_name + " " + userData.last_name;
    document.getElementById("birthday").textContent = userData.birth_date || "";
    document.getElementById("gender").textContent = userData.gender || "";
    document.getElementById("address").textContent = userData.address || "";
    document.getElementById("phone-number").textContent =
      userData.phone_number || "";
    document.getElementById("email").textContent = userData.email || "";

    // Set verification status
    const statusDiv = document.getElementById("status");
    const verifyAccountBtn = document.getElementById("verify-btn");
    if (userData.is_verified === 1) {
      statusDiv.className = "verified";
      statusDiv.innerHTML = "<span>verified</span>";
    } else {
      statusDiv.className = "not-verified";
      statusDiv.innerHTML = "<span>not-verified</span>";
      verifyAccountBtn.style.display = "block";
    }
  }

  const user = JSON.parse(localStorage.getItem("user"));
  const fullName =
    user.first_name === "admin"
      ? `${user.first_name}`
      : `${user.first_name} ${user.last_name}`;
  const profileImgUrl = user.img_url ? user.img_url : "images/profile.png";

  const accountsLink = document.getElementById("account-page-link");
  accountsLink.style.display = parseInt(user.isAdmin) === 1 ? "block" : "none";

  const fullNameContainer = document.getElementById("full-name");
  const profileImage = document.getElementById("profile-img");
  const accountSettingsImage = document.getElementById(
    "profile-img-account-settings"
  );
  profileImage.src = profileImgUrl;
  accountSettingsImage.src = profileImgUrl ?? "images/profile.png";
  fullNameContainer.textContent = fullName;

  const dashboardLink = document.getElementById("dashboard-tab");
  user.isAdmin === 1 ? "" : (dashboardLink.style.display = "block");
};
// Get the modal
const profileModal = document.getElementById("update-profile-picture");
// Get the button that opens the modal
const profileButton = document.getElementById("update-profile-picture-btn");
// Get the <span> element that closes the modal
const closeButtonProfile = document.getElementById(
  "close-button-profile-picture"
);

// When the user clicks the button, open the modal
profileButton.onclick = function () {
  profileModal.style.display = "block";
};

// When the user clicks on <span> (x), close the modal
closeButtonProfile.onclick = function () {
  profileModal.style.display = "none";
};

// When the user clicks anywhere outside the modal, close it
window.onclick = function (event) {
  if (event.target === modal) {
    profileModal.style.display = "none";
  }
};

document
  .getElementById("image-upload")
  .addEventListener("change", function (event) {
    const file = event.target.files[0]; // Get the uploaded file
    const preview = document.getElementById("image-preview"); // Get the preview element
    const container = document.getElementById("image-preview-container"); // Preview container

    if (file) {
      const reader = new FileReader();

      reader.onload = function (e) {
        preview.src = e.target.result; // Set the image source to the file data
        preview.style.display = "block"; // Make the image visible
      };

      reader.readAsDataURL(file); // Read the file as a data URL
    } else {
      preview.src = ""; // Clear the image source
      preview.style.display = "none"; // Hide the preview
    }
  });

// Handle the form submission (image upload)
// Handle the form submission (image upload)
document
  .querySelector(".update-profile-picture-form")
  .addEventListener("submit", async function (event) {
    event.preventDefault(); // Prevent the default form submission

    const formData = new FormData();
    const imageUpload = document.getElementById("image-upload");
    const userId = JSON.parse(localStorage.getItem("user")).id; // Get the user ID from localStorage

    if (imageUpload && imageUpload.files && imageUpload.files[0]) {
      const uploadedFile = imageUpload.files[0];
      console.log(`File size: ${uploadedFile.size} bytes`);
    } else {
      console.log("No file selected.");
    }

    // Check if a file is selected
    if (imageUpload.files.length > 0) {
      formData.append("profile_picture", imageUpload.files[0]);
      formData.append("user_id", userId); // Append the user ID to the form data

      try {
        const response = await fetch("upload-profile-picture.php", {
          method: "POST",
          body: formData,
        });

        const data = await response.json();

        if (data.success) {
          // const profileImage = document.getElementById("profile-img");
          // const accountSettingsImage = document.getElementById("profile-img-account-settings");
          // profileImage.src = data.img_url;
          // accountSettingsImage.src = data.img_url;
          const user = JSON.parse(localStorage.getItem("user"));
          console.log(data.image_url);
          const newUserImg = { ...user, img_url: data.image_url };
          localStorage.setItem("user", JSON.stringify(newUserImg));
          alert("Profile picture updated successfully!");
          window.location.reload();
          // You can update the user interface or refresh the page as needed
        } else {
          alert("Error updating profile picture.");
        }
      } catch (error) {
        console.error("Error:", error);
        alert("Error uploading image.");
      } finally {
        const profileModal = document.getElementById("update-profile-picture");
        if (profileModal) {
          profileModal.style.display = "none";
        }
      }
    }
  });

document.getElementById("verify-btn").addEventListener("click", async () => {
  // Retrieve email and user details from the DOM or localStorage
  const email = document.getElementById("email").textContent; // Replace with actual element containing email
  const user = JSON.parse(localStorage.getItem("user")); // Assume user object is stored in localStorage
  const userId = user?.id; // Extract user ID from the localStorage object

  if (!email || !userId) {
    alert("Missing email or user ID. Cannot send verification email.");
    return;
  }

  try {
    // Send POST request to the backend
    const response = await fetch("send_verification_email.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ email, user_id: userId }), // Send both email and user_id
    });

    // Parse the JSON response
    const result = await response.json();
    alert(result.message); // Display success or error message
    window.location.href = "authentication.php";
  } catch (error) {
    alert(
      "An error occurred while sending the verification email. Please try again."
    );
  }
});

document
  .querySelector(".forgot-password-form")
  .addEventListener("submit", async function (event) {
    event.preventDefault();
    // Get the email address from an input field
    const emailInput = document.getElementById("forgot-password-email");
    const email = emailInput.value.trim();

    // Validate email input
    if (!email) {
      alert("Please enter your email address.");
      return;
    }

    try {
      // Send a POST request to the PHP script
      const response = await fetch("forgot_password.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({ email }),
      });

      // Parse the response
      const result = await response.json();

      // Display appropriate messages based on the response
      if (result.status === "success") {
        alert(result.message); // e.g., "Password reset email sent."
      } else {
        alert(result.message); // e.g., "No account found with this email."
      }
    } catch (error) {
      alert("An error occurred. Please try again.");
      console.error("Error:", error);
    }
  });

const forgotEmailModal = document.getElementById("forgot-password");
const emailInput = document.getElementById("forgot-password-email");
const openForgotEmailModal = document.getElementById("forgot-password-btn");
const closeForgotEmailModal = document.getElementById(
  "close-forgot-password-btn"
);
const user = JSON.parse(localStorage.getItem("user"));

user.isAdmin === 1 ? (openForgotEmailModal.style.display = "none") : "";

openForgotEmailModal.onclick = function () {
  emailInput.value = user.email;
  forgotEmailModal.style.display = "block";
};

closeForgotEmailModal.onclick = function () {
  forgotEmailModal.style.display = "none";
};
