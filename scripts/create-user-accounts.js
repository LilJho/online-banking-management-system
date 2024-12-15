window.onload = function () {
  const user = JSON.parse(localStorage.getItem("user"));
  const fullName =
    user.first_name === "admin"
      ? `${user.first_name}`
      : `${user.first_name} ${user.last_name}`;

  const fullNameContainer = document.getElementById("full-name");
  fullNameContainer.textContent = fullName;

  const accountsLink = document.getElementById("account-page-link");
  accountsLink.style.display = parseInt(user.isAdmin) === 1 ? "block" : "none";

  fetchUserAccounts(user.id);
};

async function fetchUserAccounts(userId) {
  const response = await fetch("/get-user-accounts.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `user_id=${userId}`,
  });

  const user = JSON.parse(localStorage.getItem("user"));
  const data = await response.json();

  const container = document.getElementById("dashboard-content");

  // Create savings card
  const savingsCard = document.createElement("div");
  savingsCard.classList.add("cookieCard");

  const savingsHeader = document.createElement("p");
  savingsHeader.classList.add("cookieHeading");
  savingsHeader.textContent = "Savings Account";

  const savingsDescription = document.createElement("p");
  savingsDescription.classList.add("cookieDescription");
  savingsDescription.textContent = `Your Current Savings are: ${data.savings}`;

  const savingsButton = document.createElement("button");
  savingsButton.classList.add("acceptButton");
  savingsButton.textContent = "Deposit";

  // Create loan card
  const loanCard = document.createElement("div");
  loanCard.classList.add("cookieCard");

  const loanHeader = document.createElement("p");
  loanHeader.classList.add("cookieHeading");
  loanHeader.textContent = "Loan Account";

  const loanDescription = document.createElement("p");
  loanDescription.classList.add("cookieDescription");
  loanDescription.textContent = `Your Current Loan Balance are: ${data.loan}`;

  const loanButton = document.createElement("button");
  loanButton.classList.add("acceptButton");
  loanButton.textContent = "Apply for a Loan";

  // Create credit card
  const creditCard = document.createElement("div");
  creditCard.classList.add("cookieCard");

  const creditHeader = document.createElement("p");
  creditHeader.classList.add("cookieHeading");
  creditHeader.textContent = "Credit Account";

  const creditDescription = document.createElement("p");
  creditDescription.classList.add("cookieDescription");
  creditDescription.textContent = `Your Current Credit Balance are: ${data.credit}`;

  const creditButton = document.createElement("button");
  creditButton.classList.add("acceptButton");
  creditButton.textContent = "Pay Credit";

  // Append elements to the card
  savingsCard.appendChild(savingsHeader);
  savingsCard.appendChild(savingsDescription);
  loanCard.appendChild(loanHeader);
  loanCard.appendChild(loanDescription);
  creditCard.appendChild(creditHeader);
  creditCard.appendChild(creditDescription);
  if (user.is_verified === 1) {
    savingsCard.appendChild(savingsButton);
    loanCard.appendChild(loanButton);
    creditCard.appendChild(creditButton);

    savingsButton.addEventListener("click", () => {
      localStorage.setItem("userId", user.id);
      document.getElementById("deposit-savings").style.display = "block";
    });
  }

  // Append the card to the container
  container.appendChild(savingsCard);
  container.appendChild(loanCard);
  container.appendChild(creditCard);

  const profileImage = document.getElementById("profile-img");
  const profileImgUrl = user.img_url ? user.img_url : "/images/profile.png";
  console.log(profileImgUrl);
  profileImage.src = profileImgUrl;
}

const closedDepositBtn = document.getElementById("close-deposit-btn");
const depositModal = document.getElementById("deposit-savings");

closedDepositBtn.onclick = function () {
  depositModal.style.display = "none";
};

document
  .querySelector(".deposit-form")
  .addEventListener("submit", async function (event) {
    event.preventDefault(); // Prevent default form submission behavior

    const depositAmount = document.getElementById("deposit-amount").value;
    const userId = localStorage.getItem("userId");

    const formData = new FormData();
    formData.append("deposit-amount", depositAmount);
    formData.append("id", userId);

    try {
      const response = await fetch("/deposit-savings.php", {
        method: "POST",
        body: formData,
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const result = await response.json(); // Parse the JSON response
      console.log(result);

      // Refresh the announcements
      window.location.reload();
    } catch (error) {
      console.error("Error deposit savings:", error);

      // Additional debugging for non-JSON responses
      if (error.response) {
        console.error("Error response:", error.response);
      } else {
        console.error("Error message:", error.message);
      }
    } finally {
      const depositModal = document.getElementById("deposit-savings");
      if (modal) {
        depositModal.style.display = "none";
      }
    }
  });
