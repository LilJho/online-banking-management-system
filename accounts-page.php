<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/styles/dashboard.css">
    <link rel="stylesheet" href="/styles/accounts-page.css">

</head>
<body>
    <nav class="nav">
        <img src="./images/bank.png" alt="bank icon">
        <div class="user-profile">
            <p id="full-name"></p>
            <img src="/images/profile.png" alt="profile photo">
        </div>
    </nav>
    <main class="main-content">
    <section class="side-bar">
        <ul>
            <li >
                <a href="dashboard.php">
                    <div>
                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M11.293 3.293a1 1 0 0 1 1.414 0l6 6 2 2a1 1 0 0 1-1.414 1.414L19 12.414V19a2 2 0 0 1-2 2h-3a1 1 0 0 1-1-1v-3h-2v3a1 1 0 0 1-1 1H7a2 2 0 0 1-2-2v-6.586l-.293.293a1 1 0 0 1-1.414-1.414l2-2 6-6Z" clip-rule="evenodd"/>
                    </svg>

                        
                        <p>Dashboard</p>
                    </div>
                </a>
            </li>
            <li>
            <a href="offers-page.php">
                <div>
                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20 7h-.7c.229-.467.349-.98.351-1.5a3.5 3.5 0 0 0-3.5-3.5c-1.717 0-3.215 1.2-4.331 2.481C10.4 2.842 8.949 2 7.5 2A3.5 3.5 0 0 0 4 5.5c.003.52.123 1.033.351 1.5H4a2 2 0 0 0-2 2v2a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1V9a2 2 0 0 0-2-2Zm-9.942 0H7.5a1.5 1.5 0 0 1 0-3c.9 0 2 .754 3.092 2.122-.219.337-.392.635-.534.878Zm6.1 0h-3.742c.933-1.368 2.371-3 3.739-3a1.5 1.5 0 0 1 0 3h.003ZM13 14h-2v8h2v-8Zm-4 0H4v6a2 2 0 0 0 2 2h3v-8Zm6 0v8h3a2 2 0 0 0 2-2v-6h-5Z"/>
                </svg>


                    
                    <p>Offers</p>
                </div>  </a>
            </li>
            <li>
                <a href="announcements.php">
                <div>
                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M8 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1h2a2 2 0 0 1 2 2v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h2Zm6 1h-4v2H9a1 1 0 0 0 0 2h6a1 1 0 1 0 0-2h-1V4Zm-6 8a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H9a1 1 0 0 1-1-1Zm1 3a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2H9Z" clip-rule="evenodd"/>
                </svg>
                    <p>Announcements</p>
                </div>
                </a>
            </li>
            <li class="active">
                <div>
                    <!-- <img src="/images/user.png" alt="user icon"> -->
                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z" clip-rule="evenodd"/>
                    </svg>

                    <p>Account</p>
                </div>
            </li>
        </ul>
        <ul>
        <li>
            <a href="account-settings.php">
            <div>
                    <!-- <img src="/images/settings.png" alt="settings icon"> -->
                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M17 10v1.126c.367.095.714.24 1.032.428l.796-.797 1.415 1.415-.797.796c.188.318.333.665.428 1.032H21v2h-1.126c-.095.367-.24.714-.428 1.032l.797.796-1.415 1.415-.796-.797a3.979 3.979 0 0 1-1.032.428V20h-2v-1.126a3.977 3.977 0 0 1-1.032-.428l-.796.797-1.415-1.415.797-.796A3.975 3.975 0 0 1 12.126 16H11v-2h1.126c.095-.367.24-.714.428-1.032l-.797-.796 1.415-1.415.796.797A3.977 3.977 0 0 1 15 11.126V10h2Zm.406 3.578.016.016c.354.358.574.85.578 1.392v.028a2 2 0 0 1-3.409 1.406l-.01-.012a2 2 0 0 1 2.826-2.83ZM5 8a4 4 0 1 1 7.938.703 7.029 7.029 0 0 0-3.235 3.235A4 4 0 0 1 5 8Zm4.29 5H7a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h6.101A6.979 6.979 0 0 1 9 15c0-.695.101-1.366.29-2Z" clip-rule="evenodd"/>
                    </svg>
                    <p>Settings</p>
                </div>
            </a>
            </li>
            <li>
            <button id="logoutButton">
                <div class="logout">
                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H4m12 0-4 4m4-4-4-4m3-4h2a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3h-2"/>
                    </svg>
                    <p>Logout</p>
                </button>
            </li>
        </ul>
    </section>

    <section class="main-card">
        <div class="users-container">
        <button id="create-user-btn">Create User</button>
        <table class="my_table">
  <tr>
    <th>Full Name</th>
    <th>Gender</th>
    <th>Email</th>
    <th>Address</th>
    <th>Phone Number</th>
    <th>Birthday</th>
    <th>Status</th>
    <th>Archive</th>
    <th>Block</th>
  </tr>
  
</table>
        </div>
    
    </section>

    </main>

    <!-- Modal dialog -->
    <div id="create-user" class="modal">
        <div class="modal-content">
            <span class="close-button" id="close-create-user-btn">&times;</span>
            <h2>Create Offer</h2>
        <form class="create-user-form">
                
            <div class="create-user-detail">
                <div class="create-user-tab">
                <input id="firstname" name="firstname" type="text" placeholder="First Name" required />
            <input id="middlename" name="middlename" type="text" placeholder="Middle Name" required />
            <input id="lastname" name="lastname" type="text" placeholder="Last Name" required />
            <input id="address" name="address" type="text" placeholder="Address" required />
            <select id="gender" name="gender" required>
                <option value="">Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
                </div>
                <div class="create-user-tab">
                <select id="account" name="account" required>
                <option value="">Type of Account</option>
                <option value="savings">Savings</option>
                <option value="credit">Credit</option>
                <option value="loan">Loan</option>
            </select>
            <input id="balance" name="balance" type="number" placeholder="Balance" required />
            <input id="birthdate" type="date" id="birthdate" name="birthdate" placeholder="Birthday" required>
            <input id="phonenumber" name="phonenumber" type="text" placeholder="Phone Number" required />
            <input id="email" name="email" type="email" placeholder="Email" required />
            <input id="password" name="password" type="password" placeholder="Password" required />
                </div>
            </div>
                    
           
            <button class="create-user-to-db" name="create-user-to-db" type="submit">Create</button>
        </form>
        </div>
    </div>

    

    <script src="/scripts/get-all-user-accounts.js"> </script>
    <script src="/scripts/create-user.js"> </script>
    <script src="/scripts/logout.js"></script> 
    <!-- <script src="/scripts/getUserfFromLocalStorage.js"></script> -->
</body>
</html>