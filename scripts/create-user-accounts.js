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
  loanButton.textContent = "Pay Loan";

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
  if (user.is_verified) {
    savingsCard.appendChild(savingsButton);
    loanCard.appendChild(loanButton);
    creditCard.appendChild(creditButton);
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
