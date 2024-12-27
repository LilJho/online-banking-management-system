window.onload = function () {
  const user = JSON.parse(localStorage.getItem("user"));
  const fullName =
    user.first_name === "admin"
      ? `${user.first_name}`
      : `${user.first_name} ${user.last_name}`;
  const fullNameContainer = document.getElementById("full-name");
  fullNameContainer.textContent = fullName;
  const profileImage = document.getElementById("profile-img");
  const profileImgUrl = user.img_url ? user.img_url : "images/profile.png";
  profileImage.src = profileImgUrl;
  const transactionLink = document.getElementById("transaction-link");
  transactionLink.style.display =
    parseInt(user.isAdmin) === 1 ? "block" : "none";
  const accountsLink = document.getElementById("account-page-link");
  accountsLink.style.display = parseInt(user.isAdmin) === 1 ? "block" : "none";
  const dashboardLink = document.getElementById("dashboard-tab");
  user.isAdmin === 1 ? "" : (dashboardLink.style.display = "block");

  fetchAllPendingTransactions();
};

async function fetchAllPendingTransactions(page = 1, limit = 10) {
  try {
    const response = await fetch(
      `get_pending_transactions.php?page=${page}&limit=${limit}`
    );
    const data = await response.json();

    if (response.ok) {
      if (data.transactions && data.transactions.length > 0) {
        console.log(data);
        displayTransactions(data.transactions);
        updatePagination(data.pagination);
      } else {
        console(data.message || "No transactions found.");
      }
    } else {
      console(data.error || "Failed to fetch transactions.");
    }
  } catch (error) {
    console.error("Error fetching transactions:", error);
    console("An error occurred while fetching transactions.");
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
        <td>
          <button class="approve-btn">Approve</button>
        </td>
        <td>
          <button class="decline-btn">Decline</button>
        </td>
      `;

    tableBody.appendChild(row);

    // Add event listeners to the buttons
    const approveButton = row.querySelector(".approve-btn");
    const declineButton = row.querySelector(".decline-btn");

    approveButton.addEventListener("click", () => {
      updateTransaction(
        transaction.account_id,
        transaction.transaction_id,
        transaction.transaction_type,
        transaction.amount
      );
    });

    declineButton.addEventListener("click", () => {
      alert(`Decline clicked for Transaction ID: ${transaction.account_id}`);
    });
  });
}

// Update pagination controls
function updatePagination(pagination) {
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
      fetchAllPendingTransactions(i);
    };

    paginationContainer.appendChild(pageButton);
  }
}

async function updateTransaction(
  accountId,
  transactionId,
  transactionType,
  amount
) {
  try {
    const formData = new FormData();
    formData.append("account_id", accountId);
    formData.append("transaction_id", transactionId);
    formData.append("transaction_type", transactionType);
    formData.append("amount", amount);

    const response = await fetch("approve-transaction.php", {
      method: "POST",
      body: formData,
    });

    const data = await response.json();

    if (response.ok) {
      alert(data.message || "Transaction updated successfully.");
      // Reload transactions to refresh the table
      fetchAllPendingTransactions();
    } else {
      alert(data.error || "Failed to update transaction.");
    }
  } catch (error) {
    console.error("Error updating transaction:", error);
    alert("An error occurred while updating the transaction.");
  }
}
