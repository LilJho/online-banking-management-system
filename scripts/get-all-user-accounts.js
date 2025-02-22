const modal = document.getElementById("create-user");
// Get the button that opens the modal
const updateButton = document.getElementById("create-user-btn");
// Get the <span> element that closes the modal
const closeButton = document.getElementById("close-create-user-btn");

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

// Fetch all offers from the server
async function fetchAccounts() {
  try {
    const response = await fetch("get-all-user-accounts.php"); // Adjust the path to your PHP file

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const accounts = await response.json();
    // console.log('Accounts:', accounts);

    // Populate the offers container
    const tablesContainer = document.querySelector(".my_table");
    tablesContainer.innerHTML = `<tr>
    <th>Bank ID No.</th>
    <th>Full Name</th>
    <th>Status</th>
    <th>Archive</th>
    <th>Block</th>
    <th>Deposit</th>
    <th>Withdraw</th>
    <th>Loan</th>
    <th>Pay Loan</th>
    <th>Apply Credit</th>
    <th>Pay Credit</th>
  </tr>`;

    accounts.forEach((account) => {
      // Create the offer card dynamically
      const tableRowCard = document.createElement("tr");

      const bankIdNo = document.createElement("td");
      bankIdNo.textContent = account.bank_id_no;

      // Create the table data for each piece of information
      const fullNameData = document.createElement("td");
      fullNameData.textContent = `${account.first_name} ${account.middle_name} ${account.last_name}`;

      const genderData = document.createElement("td");
      genderData.textContent = account.gender;

      const emailData = document.createElement("td");
      emailData.textContent = account.email;

      // const addressData = document.createElement("td");
      // addressData.textContent = account.address;

      // const phoneNumberData = document.createElement("td");
      // phoneNumberData.textContent = account.phone_number;

      // const birthdayData = document.createElement("td");
      // birthdayData.textContent = account.birth_date;

      const statusData = document.createElement("td");
      statusData.textContent =
        parseInt(account.is_verified) === 1 ? "Verified" : "Not Verified";

      // Create buttons and wrap them in separate <td> elements
      const archiveData = document.createElement("td");
      const archiveButton = document.createElement("button");
      archiveButton.textContent = "Archive";
      archiveButton.onclick = function () {
        handleAction(account.id, "archive"); // Handle archiving
      };
      archiveData.appendChild(archiveButton);

      const blockData = document.createElement("td");
      const blockButton = document.createElement("button");
      blockButton.textContent =
        parseInt(account.is_blocked) === 1 ? "Unblock" : "Block";
      blockButton.onclick = function () {
        handleAction(
          account.id,
          parseInt(account.is_blocked) === 1 ? "unblock" : "block"
        ); // Handle blocking
      };
      blockData.appendChild(blockButton);

      // Create Update Button
      const depositData = document.createElement("td");
      const depositButton = document.createElement("button");
      depositButton.textContent = "Deposit";

      depositData.appendChild(depositButton);

      depositButton.addEventListener("click", () => {
        localStorage.setItem("userId", account.id);
        document.getElementById("deposit-savings").style.display = "block";
      });

      // Create Update Button

      const withdrawData = document.createElement("td");
      const withdrawButton = document.createElement("button");
      withdrawButton.textContent = "Withdraw";

      withdrawData.appendChild(withdrawButton);

      withdrawButton.addEventListener("click", () => {
        localStorage.setItem("userId", account.id);
        document.getElementById("withdraw-savings").style.display = "block";
      });

      const loanData = document.createElement("td");
      const loanButton = document.createElement("button");
      loanButton.textContent = "Loan";

      loanData.appendChild(loanButton);

      loanButton.addEventListener("click", () => {
        localStorage.setItem("userId", account.id);
        document.getElementById("loan").style.display = "block";
      });

      const payLoanData = document.createElement("td");
      const payLoanButton = document.createElement("button");
      payLoanButton.textContent = "Pay Loan";

      payLoanData.appendChild(payLoanButton);

      payLoanButton.addEventListener("click", () => {
        localStorage.setItem("userId", account.id);
        document.getElementById("pay-loan").style.display = "block";
      });

      const applyCreditData = document.createElement("td");
      const applyCreditBtn = document.createElement("button");
      applyCreditBtn.textContent = "Apply Credit";

      applyCreditData.appendChild(applyCreditBtn);

      applyCreditBtn.addEventListener("click", () => {
        localStorage.setItem("userId", account.id);
        document.getElementById("apply-credit").style.display = "block";
      });

      const payCreditData = document.createElement("td");
      const payCreditBtn = document.createElement("button");
      payCreditBtn.textContent = "Pay Credit";

      payCreditData.appendChild(payCreditBtn);

      payCreditBtn.addEventListener("click", () => {
        localStorage.setItem("userId", account.id);
        document.getElementById("pay-credit").style.display = "block";
      });

      // Append all data and buttons to the row
      tableRowCard.appendChild(bankIdNo);
      tableRowCard.appendChild(fullNameData);
      // tableRowCard.appendChild(genderData);
      // tableRowCard.appendChild(emailData);
      // tableRowCard.appendChild(addressData);
      // tableRowCard.appendChild(phoneNumberData);
      // tableRowCard.appendChild(birthdayData);
      tableRowCard.appendChild(statusData);
      tableRowCard.appendChild(archiveData); // Archive button in its own td
      tableRowCard.appendChild(blockData); // Block button in its own td
      tableRowCard.appendChild(depositData); // Block button in its own td
      tableRowCard.appendChild(withdrawData); // Block button in its own td
      tableRowCard.appendChild(loanData); // Block button in its own td
      tableRowCard.appendChild(payLoanData); // Block button in its own td
      tableRowCard.appendChild(applyCreditData); // Block button in its own td
      tableRowCard.appendChild(payCreditData); // Block button in its own td

      // Append the offer card to the container
      tablesContainer.appendChild(tableRowCard);
    });
  } catch (error) {
    console.error("Error fetching offers:", error);
  }
}

async function handleAction(userId, action) {
  try {
    const response = await fetch("block-archive-user.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ userId: userId, action: action }),
    });

    const result = await response.json();

    if (result.success) {
      alert(
        `${action.charAt(0).toUpperCase() + action.slice(1)} action successful!`
      );
      location.reload(); // Reload the page to reflect the changes
    } else {
      alert(`Failed to ${action} the user.`);
    }
  } catch (error) {
    console.error("Error handling action:", error);
  }
}

// Automatically fetch offers when the page loads
window.onload = function () {
  fetchAccounts();
  fetchTopInvestorAccounts();

  const user = JSON.parse(localStorage.getItem("user"));
  const fullName =
    user.first_name === "admin"
      ? `${user.first_name}`
      : `${user.first_name} ${user.last_name}`;
  const transactionLink = document.getElementById("transaction-link");
  transactionLink.style.display =
    parseInt(user.isAdmin) === 1 ? "block" : "none";
  const accountsLink = document.getElementById("account-page-link");
  accountsLink.style.display = parseInt(user.isAdmin) === 1 ? "block" : "none";

  const profileImage = document.getElementById("profile-img");
  const profileImgUrl = user.img_url ? user.img_url : "images/profile.png";
  // console.log(profileImgUrl);
  profileImage.src = profileImgUrl;

  const fullNameContainer = document.getElementById("full-name");
  fullNameContainer.textContent = fullName;

  const dashboardLink = document.getElementById("dashboard-tab");
  user.isAdmin === 1 ? "" : (dashboardLink.style.display = "block");
};

async function fetchTopInvestorAccounts() {
  try {
    const response = await fetch("get-all-top-investors-accounts.php"); // Adjust the path to your PHP file

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const accounts = await response.json();

    console.log({ accounts });

    // Populate the offers container
    const tablesContainer = document.querySelector(".top-investors-table");
    tablesContainer.innerHTML = `<tr>
    <th>Full Name</th>
    <th>Gender</th>
    <th>Email</th>
    <th>Savings</th>
    </tr>`;

    accounts.forEach((account) => {
      // Create the offer card dynamically
      const tableRowCard = document.createElement("tr");

      // Create the table data for each piece of information
      const fullNameData = document.createElement("td");
      fullNameData.textContent = `${account.first_name} ${account.middle_name} ${account.last_name}`;

      const genderData = document.createElement("td");
      genderData.textContent = account.gender;

      const emailData = document.createElement("td");
      emailData.textContent = account.email;

      const savingsData = document.createElement("td");
      savingsData.textContent = `${account.savings}Php`;

      // Append all data and buttons to the row
      tableRowCard.appendChild(fullNameData);
      tableRowCard.appendChild(genderData);
      tableRowCard.appendChild(emailData);
      tableRowCard.appendChild(savingsData);

      // Append the offer card to the container
      tablesContainer.appendChild(tableRowCard);
    });
  } catch (error) {
    console.error("Error fetching offers:", error);
  }
}
