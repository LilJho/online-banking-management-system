document
  .querySelector(".create-user-form")
  .addEventListener("submit", async function (event) {
    event.preventDefault(); // Prevent default form submission behavior

    const firstname = document.getElementById("firstname").value;
    const middlename = document.getElementById("middlename").value;
    const lastname = document.getElementById("lastname").value;
    const address = document.getElementById("address").value;
    const gender = document.getElementById("gender").value;
    // const account = document.getElementById("account").value;
    // const balance = document.getElementById("balance").value;
    const birthdate = document.getElementById("birthdate").value;
    const phonenumber = document.getElementById("phonenumber").value;
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;

    // Prepare form data
    const formData = new FormData();
    formData.append("firstName", firstname);
    formData.append("middleName", middlename);
    formData.append("lastName", lastname);
    formData.append("address", address);
    formData.append("gender", gender);
    // formData.append("account", account);
    // formData.append("balance", balance);
    formData.append("birthDate", birthdate);
    formData.append("phoneNumber", phonenumber);
    formData.append("email", email);
    formData.append("password", password);

    try {
      const response = await fetch("create-user.php", {
        method: "POST",
        body: formData,
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const result = await response.json(); // Parse the JSON response
      console.log(result);

      // Refresh the announcements
      alert(result.message);
      window.location.reload();
    } catch (error) {
      console.error("Error creating announcement:", error);

      // Additional debugging for non-JSON responses
      if (error.response) {
        console.error("Error response:", error.response);
      } else {
        console.error("Error message:", error.message);
      }
    } finally {
      const modal = document.getElementById("create-user");
      if (modal) {
        modal.style.display = "none";
      }
    }
  });

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
      fetchAccounts();
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

const closedWithdrawBtn = document.getElementById("close-withdraw-btn");
const withdrawModal = document.getElementById("withdraw-savings");

closedWithdrawBtn.onclick = function () {
  withdrawModal.style.display = "none";
};

document
  .querySelector(".withdraw-form")
  .addEventListener("submit", async function (event) {
    event.preventDefault(); // Prevent default form submission behavior

    const withdrawAmount = document.getElementById("withdraw-amount").value;
    const userId = localStorage.getItem("userId");

    const formData = new FormData();
    formData.append("withdraw-amount", withdrawAmount);
    formData.append("id", userId);

    try {
      const response = await fetch("withdraw.php", {
        method: "POST",
        body: formData,
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const result = await response.json(); // Parse the JSON response
      console.log(result);

      // Refresh the announcements
      fetchAccounts();
    } catch (error) {
      console.error("Error withdraw savings:", error);

      // Additional debugging for non-JSON responses
      if (error.response) {
        console.error("Error response:", error.response);
      } else {
        console.error("Error message:", error.message);
      }
    } finally {
      const depositModal = document.getElementById("withdraw-savings");
      if (modal) {
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

    const loanAmount = document.getElementById("loan-amount").value;
    const userId = localStorage.getItem("userId");

    const formData = new FormData();
    formData.append("loan-amount", loanAmount);
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
      console.log(result);

      // Refresh the announcements
      fetchAccounts();
    } catch (error) {
      console.error("Error loan savings:", error);

      // Additional debugging for non-JSON responses
      if (error.response) {
        console.error("Error response:", error.response);
      } else {
        console.error("Error message:", error.message);
      }
    } finally {
      const depositModal = document.getElementById("loan");
      if (modal) {
        depositModal.style.display = "none";
      }
    }
  });

const closedPayLoanBtn = document.getElementById("close-pay-loan-btn");
const payLoanModal = document.getElementById("pay-loan");

closedPayLoanBtn.onclick = function () {
  payLoanModal.style.display = "none";
};

document
  .querySelector(".pay-loan-form")
  .addEventListener("submit", async function (event) {
    event.preventDefault(); // Prevent default form submission behavior

    const loanPayment = document.getElementById("pay-loan-amount").value;
    const userId = localStorage.getItem("userId");

    const formData = new FormData();
    formData.append("loan-payment", loanPayment);
    formData.append("id", userId);

    try {
      const response = await fetch("pay-loan.php", {
        method: "POST",
        body: formData,
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const result = await response.json(); // Parse the JSON response
      console.log(result);

      // Refresh the announcements
      fetchAccounts();
    } catch (error) {
      console.error("Error pay loan:", error);

      // Additional debugging for non-JSON responses
      if (error.response) {
        console.error("Error response:", error.response);
      } else {
        console.error("Error message:", error.message);
      }
    } finally {
      const depositModal = document.getElementById("pay-loan");
      if (modal) {
        depositModal.style.display = "none";
      }
    }
  });

const closedTopInvestorsTabBtn = document.getElementById(
  "close-top-investors-btn"
);
const openTopInvestorsTabBtn = document.getElementById(
  "view-top-investors-btn"
);
const topInvestorModal = document.getElementById("top-investors");

openTopInvestorsTabBtn.onclick = function () {
  topInvestorModal.style.display = "block";
};

closedTopInvestorsTabBtn.onclick = function () {
  topInvestorModal.style.display = "none";
};

const closedCreditBtn = document.getElementById("close-credit-btn");
const creditModal = document.getElementById("apply-credit");

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

const closedPayCreditBtn = document.getElementById("close-pay-credit-btn");
const payCreditModal = document.getElementById("pay-credit");

closedPayCreditBtn.onclick = function () {
  payCreditModal.style.display = "none";
};

document
  .querySelector(".pay-credit-form")
  .addEventListener("submit", async function (event) {
    event.preventDefault(); // Prevent default form submission behavior

    const creditPayment = document.getElementById("pay-credit-amount").value;
    const userId = localStorage.getItem("userId");

    const formData = new FormData();
    formData.append("credit-payment", creditPayment);
    formData.append("id", userId);

    try {
      const response = await fetch("pay-credit.php", {
        method: "POST",
        body: formData,
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const result = await response.json(); // Parse the JSON response
      console.log(result);

      // Refresh the announcements
      fetchAccounts();
    } catch (error) {
      console.error("Error pay Credit:", error);

      // Additional debugging for non-JSON responses
      if (error.response) {
        console.error("Error response:", error.response);
      } else {
        console.error("Error message:", error.message);
      }
    } finally {
      const depositModal = document.getElementById("pay-credit");
      if (modal) {
        depositModal.style.display = "none";
      }
    }
  });
