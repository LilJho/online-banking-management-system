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
  fetchUserTransactions(user.id);
};

async function fetchUserAccounts(userId) {
  const response = await fetch("get-user-accounts.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `user_id=${userId}`,
  });

  const user = JSON.parse(localStorage.getItem("user"));
  const data = await response.json();

  const container = document.getElementById("dashboard-content");
  container.innerHTML = "";

  // Create savings card
  const savingsCard = document.createElement("div");
  savingsCard.classList.add("cookieCard");

  const savingsHeader = document.createElement("p");
  savingsHeader.classList.add("cookieHeading");
  savingsHeader.textContent = "Savings Account";

  const savingsDescription = document.createElement("p");
  savingsDescription.classList.add("cookieDescription");
  savingsDescription.innerHTML = `Your Current Savings are: ${
    data.savings
  } <br> yearly Interest is: ${data.savings * 0.2}`;

  const savingsButtonContainer = document.createElement("div");
  savingsButtonContainer.classList.add("btnContainer");

  const savingsButton = document.createElement("button");
  savingsButton.classList.add("acceptButton");
  savingsButton.textContent = "Deposit";

  const transferSavingsButton = document.createElement("button");
  transferSavingsButton.classList.add("acceptButton");
  transferSavingsButton.textContent = "Transfer";

  const toggleSavingsButton = document.createElement("button");
  toggleSavingsButton.classList.add("acceptButton");
  toggleSavingsButton.textContent =
    data.savingsStatus === "active" ? "lock" : "unlock";

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
  creditButton.textContent = "Apply for a credit";

  // Append elements to the card
  savingsCard.appendChild(savingsHeader);
  savingsCard.appendChild(savingsDescription);
  // savingsCard.appendChild(YearlyInterestDescription);
  loanCard.appendChild(loanHeader);
  loanCard.appendChild(loanDescription);
  creditCard.appendChild(creditHeader);
  creditCard.appendChild(creditDescription);
  if (user.is_verified === 1) {
    savingsButtonContainer.appendChild(savingsButton);
    if (data.savingsStatus === "active") {
      savingsButtonContainer.appendChild(transferSavingsButton);
    }
    if (data.savingsStatus !== "no account yet") {
      savingsButtonContainer.appendChild(toggleSavingsButton);

      toggleSavingsButton.addEventListener("click", async () => {
        const formData = new FormData();
        formData.append("user_id", user.id);

        try {
          // Send the request to PHP
          const response = await fetch("toggle-account-status.php", {
            method: "POST",
            body: formData,
          });

          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }

          const result = await response.json();

          if (result.error) {
            console.error("Error:", result.error);
            alert(`Error: ${result.error}`);
          } else {
            console.log(result.message);
            alert(result.message);

            window.location.reload();
          }
        } catch (error) {
          console.error("Error toggling account status:", error.message);
          alert("Failed to toggle account status. Please try again.");
        }
      });
    }
    savingsCard.appendChild(savingsButtonContainer);
    loanCard.appendChild(loanButton);
    creditCard.appendChild(creditButton);

    savingsButton.addEventListener("click", () => {
      localStorage.setItem("userId", user.id);
      document.getElementById("deposit-savings").style.display = "block";
    });

    transferSavingsButton.addEventListener("click", () => {
      localStorage.setItem("userId", user.id);
      document.getElementById("transfer-savings").style.display = "block";
    });

    loanButton.addEventListener("click", () => {
      localStorage.setItem("userId", user.id);
      document.getElementById("loan").style.display = "block";
    });

    creditCard.addEventListener("click", () => {
      localStorage.setItem("userId", user.id);
      document.getElementById("credit").style.display = "block";
    });
  }

  // Append the card to the container
  container.appendChild(savingsCard);
  container.appendChild(loanCard);
  container.appendChild(creditCard);

  const profileImage = document.getElementById("profile-img");
  const profileImgUrl = user.img_url ? user.img_url : "images/profile.png";
  console.log(profileImgUrl);
  profileImage.src = profileImgUrl;
}

const openTransactionsBtn = document.getElementById("view-transactions");
const closedTransactionsBtn = document.getElementById("close-transactions-btn");
const transactionsModal = document.getElementById("transactions");

openTransactionsBtn.onclick = function () {
  transactionsModal.style.display = "flex";
  transactionsModal.style.justifyContent = "center";
  transactionsModal.style.alignItems = "center";
};
closedTransactionsBtn.onclick = function () {
  transactionsModal.style.display = "none";
};

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
      const response = await fetch("deposit-savings.php", {
        method: "POST",
        body: formData,
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const result = await response.json(); // Parse the JSON response
      alert(result.message);

      // Refresh the announcements
      fetchUserAccounts(userId);
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
      if (depositModal) {
        depositModal.style.display = "none";
      }
    }
  });

const closedLoanBtn = document.getElementById("close-loan-btn");
const loadModal = document.getElementById("loan");

closedLoanBtn.onclick = function () {
  loadModal.style.display = "none";
};

document
  .querySelector(".loan-form")
  .addEventListener("submit", async function (event) {
    event.preventDefault(); // Prevent default form submission behavior

    // Get loan amount and selected interest
    const loanAmount = parseFloat(document.getElementById("loan-amount").value);
    const selectElement = document.getElementById("months");
    const interestRate = parseFloat(selectElement.value); // Get selected interest rate
    const MonthInterestRate =
      selectElement.options[selectElement.selectedIndex].textContent;
    const userId = localStorage.getItem("userId");

    if (isNaN(loanAmount) || isNaN(interestRate)) {
      console.error("Invalid loan amount or interest rate.");
      return;
    }

    // Calculate the total loan amount including interest
    const totalLoanAmount = loanAmount + loanAmount * interestRate;

    // Store values in FormData
    const formData = new FormData();
    formData.append("loan-amount", totalLoanAmount.toFixed(2)); // Add total loan amount
    formData.append("id", userId);

    try {
      const response = await fetch("loan.php", {
        method: "POST",
        body: formData,
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const result = await response.json(); // Parse the JSON response
      alert(result.message);
    } catch (error) {
      console.error("Error loan savings:", error);

      if (error.response) {
        console.error("Error response:", error.response);
      } else {
        console.error("Error message:", error.message);
      }
    } finally {
      document.getElementById("amount").textContent = loanAmount; // Dummy loan amount
      document.getElementById("duration").textContent = MonthInterestRate; // Dummy loan duration
      document.getElementById("total-interest").textContent = interestRate; // Dummy total interest
      document.getElementById("total-payment").textContent = totalLoanAmount; // Dummy total payment
      const depositModal = document.getElementById("loan-to-pay");
      if (depositModal) {
        depositModal.style.display = "block";
      }
    }
  });

const closedCreditBtn = document.getElementById("close-credit-btn");
const creditModal = document.getElementById("credit");

closedCreditBtn.onclick = function () {
  creditModal.style.display = "none";
};

document
  .querySelector(".credit-form")
  .addEventListener("submit", async function (event) {
    event.preventDefault(); // Prevent default form submission behavior

    const creditAmount = document.getElementById("credit-amount").value;
    const userId = localStorage.getItem("userId");

    const formData = new FormData();
    formData.append("credit-amount", creditAmount);
    formData.append("id", userId);

    try {
      const response = await fetch("credit.php", {
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
      console.error("Error credit savings:", error);

      // Additional debugging for non-JSON responses
      if (error.response) {
        console.error("Error response:", error.response);
      } else {
        console.error("Error message:", error.message);
      }
    } finally {
      const depositModal = document.getElementById("credit");
      if (modal) {
        depositModal.style.display = "none";
      }
    }
  });

const closedTransferBtn = document.getElementById("close-transfer-btn");
const transferModal = document.getElementById("transfer-savings");

closedTransferBtn.onclick = function () {
  transferModal.style.display = "none";
};
const closedLoanShowBtn = document.getElementById("close-loan-to-pay-btn");
const loanShowModal = document.getElementById("loan-to-pay");

closedLoanShowBtn.onclick = function () {
  loanShowModal.style.display = "none";
};

document
  .querySelector(".transfer-form")
  .addEventListener("submit", async function (event) {
    event.preventDefault(); // Prevent default form submission behavior

    const depositAmount = document.getElementById("transfer-amount").value;
    const destinationBankId = document.getElementById("transfer-bank-id").value;
    const userId = localStorage.getItem("userId");

    const formData = new FormData();
    formData.append("transfer-amount", depositAmount);
    formData.append("bank_id_no", destinationBankId);
    formData.append("id", userId);

    console.log(depositAmount);
    console.log(destinationBankId);
    console.log(userId);

    try {
      const response = await fetch("transfer-savings.php", {
        method: "POST",
        body: formData,
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const result = await response.json(); // Parse the JSON response
      alert(result.message);

      fetchUserAccounts(userId);
    } catch (error) {
      console.error("Error transfer savings:", error);

      // Additional debugging for non-JSON responses
      if (error.response) {
        console.error("Error response:", error.response);
      } else {
        console.error("Error message:", error.message);
      }
    } finally {
      const transferModal = document.getElementById("transfer-savings");
      if (transferModal) {
        transferModal.style.display = "none";
      }
    }
  });

document
  .getElementById("toggle-status-btn")
  .addEventListener("click", async function () {
    const userId = localStorage.getItem("userId"); // Assuming you fetch user_id from localStorage
    const button = document.getElementById("toggle-status-btn");

    if (!userId || isNaN(userId)) {
      console.error("Invalid User ID");
      alert("User ID is invalid or not set!");
      return;
    }

    const formData = new FormData();
    formData.append("user_id", userId);

    try {
      // Send the request to PHP
      const response = await fetch("toggle-account-status.php", {
        method: "POST",
        body: formData,
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const result = await response.json();

      if (result.error) {
        console.error("Error:", result.error);
        alert(`Error: ${result.error}`);
      } else {
        console.log(result.message);
        alert(result.message);

        // Update button text dynamically
        button.textContent =
          result.new_status === "locked" ? "Unlock Account" : "Lock Account";
      }
    } catch (error) {
      console.error("Error toggling account status:", error.message);
      alert("Failed to toggle account status. Please try again.");
    }
  });

async function fetchUserTransactions(userId, page = 1, limit = 10) {
  try {
    const response = await fetch(
      `get_transactions.php?id=${userId}&page=${page}&limit=${limit}`
    );
    const data = await response.json();

    if (response.ok) {
      if (data.transactions && data.transactions.length > 0) {
        displayTransactions(data.transactions);
        updatePagination(data.pagination, userId);
      } else {
        alert(data.message || "No transactions found.");
      }
    } else {
      alert(data.error || "Failed to fetch transactions.");
    }
  } catch (error) {
    console.error("Error fetching transactions:", error);
    alert("An error occurred while fetching transactions.");
  }
}

// Display transactions in the HTML table
function displayTransactions(transactions) {
  const tableBody = document.querySelector("#transactionsTable tbody");
  tableBody.innerHTML = ""; // Clear existing rows

  transactions.forEach((transaction) => {
    const row = document.createElement("tr");

    row.innerHTML = `
        <td>${transaction.transaction_id}</td>
        <td>${transaction.transaction_type}</td>
        <td>${transaction.amount}</td>
        <td>${transaction.transaction_date}</td>
        <td>${transaction.transaction_status}</td>
      `;

    tableBody.appendChild(row);
  });
}

// Update pagination controls
function updatePagination(pagination, userId) {
  const paginationContainer = document.getElementById("pagination");
  paginationContainer.innerHTML = ""; // Clear existing controls

  for (let i = 1; i <= pagination.total_pages; i++) {
    const pageButton = document.createElement("button");
    pageButton.textContent = i;
    pageButton.classList.add("pagination-button");
    if (i === pagination.current_page) {
      pageButton.classList.add("active");
    }

    pageButton.onclick = function () {
      fetchUserTransactions(userId, i);
    };

    paginationContainer.appendChild(pageButton);
  }
}
