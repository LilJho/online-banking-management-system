const signUpButton = document.getElementById("signUp");
const signInButton = document.getElementById("signIn");
const container = document.getElementById("container");

signUpButton.addEventListener("click", () => {
  container.classList.add("right-panel-active");
});

signInButton.addEventListener("click", () => {
  container.classList.remove("right-panel-active");
});

// Get today's date in YYYY-MM-DD format
const today = new Date().toISOString().split("T")[0];

// Set the max attribute to today's date
document.getElementById("birthdate").setAttribute("max", today);

document
  .querySelector("#loginForm")
  .addEventListener("submit", async function (event) {
    event.preventDefault(); // Prevent form from submitting normally

    const email = document.querySelector('input[name="login_email"]').value;
    const password = document.querySelector('input[name="login_pass"]').value;

    const formData = {
      login_email: email,
      login_pass: password,
    };

    try {
      const response = await fetch("login.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams(formData),
      });

      const data = await response.json();
      console.log({ data });

      if (data.success) {
        // Handle successful login (e.g., redirect to dashboard)
        localStorage.setItem("user", JSON.stringify(data.user));
        // alert('Login successful!');

        window.location.href =
          data.user.isAdmin === 1 ? "offers-page.html" : "dashboard.html";
      } else {
        // Handle login failure (show error message)
        alert(data.message);
      }
    } catch (error) {
      console.error("Error:", error);
      alert("An error occurred. Please try again.");
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
      const response = await fetch("/forgot_password.php", {
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
const openForgotEmailModal = document.getElementById("forgot-password-btn");
const closeForgotEmailModal = document.getElementById(
  "close-forgot-password-btn"
);

openForgotEmailModal.onclick = function () {
  console.log("asdad");
  forgotEmailModal.style.display = "block";
};

closeForgotEmailModal.onclick = function () {
  forgotEmailModal.style.display = "none";
};
