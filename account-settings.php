<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/styles/dashboard.css">
    <link rel="stylesheet" href="/styles/acount-settings.css">
    <?php
// Database connection
$host = "localhost";
$username = "root";
$password = "Password@29263";
$database = "online_bank_db";

// Create a new database connection
$conn = new mysqli($host, $username, $password, $database);

// Check for connection errors
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Get the user ID from the request (e.g., from GET or POST)
// $user_id = isset($_GET['id']) ? $_GET['id'] : 0; // Replace with your preferred method to retrieve ID
$user_id = 5;

// Sanitize the input to prevent SQL injection
$user_id = $conn->real_escape_string($user_id);

// Prepare the query to get the user by ID
$query = "SELECT * FROM users WHERE id = $user_id";

// Execute the query
$result = $conn->query($query);

// Check if the user was found
if ($result->num_rows > 0) {
    // Fetch the user data
    $user = $result->fetch_assoc();
    $birthday = new DateTime($user['birth_date']);
    $formattedBirthday = $birthday->format('F j, Y');
    
    $first_name = $user['first_name'];
    $middle_name = $user['middle_name'];
    $last_name = $user['last_name'];
    $birthday = $formattedBirthday;
    $gender = $user['gender'];
    $address = $user['address'];
    $phone_number = $user['phone_number'];
    $email = $user['email'];
    $is_verified = $user['is_verified'];
} else {
    // If no user found, display default info
    $first_name = "Unknown";
    $middle_name = "Unknown";
    $last_name = "User";
    $birthday = "N/A";
    $gender = "N/A";
    $address = "N/A";
    $phone_number = "N/A";
    $email = "N/A";
    $is_verified = 0;
}

// Close the database connection
$conn->close();
?>
</head>
<body>
    <nav class="nav">
        <img src="./images/bank.png" alt="bank icon">
        <div class="user-profile">
            <p>Jhonnel Garcia</p>
            <img src="/images/profile.png" alt="profile photo">
        </div>
    </nav>
    <main class="main-content">
    <section class="side-bar">
        <ul>
            <li >
                <div>
                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
  <path fill-rule="evenodd" d="M11.293 3.293a1 1 0 0 1 1.414 0l6 6 2 2a1 1 0 0 1-1.414 1.414L19 12.414V19a2 2 0 0 1-2 2h-3a1 1 0 0 1-1-1v-3h-2v3a1 1 0 0 1-1 1H7a2 2 0 0 1-2-2v-6.586l-.293.293a1 1 0 0 1-1.414-1.414l2-2 6-6Z" clip-rule="evenodd"/>
</svg>

                    
                    <p>Dashboard</p>
                </div>
            </li>
            <li>
                <div>
                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
  <path fill-rule="evenodd" d="M4 4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H4Zm10 5a1 1 0 0 1 1-1h3a1 1 0 1 1 0 2h-3a1 1 0 0 1-1-1Zm0 3a1 1 0 0 1 1-1h3a1 1 0 1 1 0 2h-3a1 1 0 0 1-1-1Zm0 3a1 1 0 0 1 1-1h3a1 1 0 1 1 0 2h-3a1 1 0 0 1-1-1Zm-8-5a3 3 0 1 1 6 0 3 3 0 0 1-6 0Zm1.942 4a3 3 0 0 0-2.847 2.051l-.044.133-.004.012c-.042.126-.055.167-.042.195.006.013.02.023.038.039.032.025.08.064.146.155A1 1 0 0 0 6 17h6a1 1 0 0 0 .811-.415.713.713 0 0 1 .146-.155c.019-.016.031-.026.038-.04.014-.027 0-.068-.042-.194l-.004-.012-.044-.133A3 3 0 0 0 10.059 14H7.942Z" clip-rule="evenodd"/>
</svg>

                    
                    <p>Card</p>
                </div>  
            </li>
            <li>
                <div>
                    <!-- <img src="/images/transaction.png" alt="transaction icon"> -->
                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16 10 3-3m0 0-3-3m3 3H5v3m3 4-3 3m0 0 3 3m-3-3h14v-3"/>
</svg>

                    <p>Transaction</p>
                </div>
            </li>
            <li>
                <div>
                    <!-- <img src="/images/chart.png" alt="chart icon"> -->
                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v15a1 1 0 0 0 1 1h15M8 16l2.5-5.5 3 3L17.273 7 20 9.667"/>
</svg>

                    <p>Reporting</p>
                </div>
            </li>
            <li>
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
        <li class="active">
                <div>
                    <!-- <img src="/images/settings.png" alt="settings icon"> -->
                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
  <path fill-rule="evenodd" d="M17 10v1.126c.367.095.714.24 1.032.428l.796-.797 1.415 1.415-.797.796c.188.318.333.665.428 1.032H21v2h-1.126c-.095.367-.24.714-.428 1.032l.797.796-1.415 1.415-.796-.797a3.979 3.979 0 0 1-1.032.428V20h-2v-1.126a3.977 3.977 0 0 1-1.032-.428l-.796.797-1.415-1.415.797-.796A3.975 3.975 0 0 1 12.126 16H11v-2h1.126c.095-.367.24-.714.428-1.032l-.797-.796 1.415-1.415.796.797A3.977 3.977 0 0 1 15 11.126V10h2Zm.406 3.578.016.016c.354.358.574.85.578 1.392v.028a2 2 0 0 1-3.409 1.406l-.01-.012a2 2 0 0 1 2.826-2.83ZM5 8a4 4 0 1 1 7.938.703 7.029 7.029 0 0 0-3.235 3.235A4 4 0 0 1 5 8Zm4.29 5H7a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h6.101A6.979 6.979 0 0 1 9 15c0-.695.101-1.366.29-2Z" clip-rule="evenodd"/>
</svg>

                    <p>Settings</p>
                </div>
            </li>
            <li>
                <div class="logout">
                    <!-- <img src="/images/logout.png" alt="exit icon"> -->
                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H4m12 0-4 4m4-4-4-4m3-4h2a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3h-2"/>
</svg>

                    <p>Logout</p>
                </div>
            </li>
        </ul>
    </section>

    <section class="main-card">
        <div class="account-details-card">
            <img src="/images/profile.png" alt="profile picture">
            <div class="account-name-birthday">
                <p><?php echo $first_name . ' ' . $last_name; ?></p>
            </div>
            <div class="account-info-card">
                <ul>
                <li><p>Birthday:</p> <span><?php echo $birthday; ?></span></li>
                    <li><p>Gender:</p> <span><?php echo $gender; ?></span></li>  
                    <li><p>Address:</p> <span><?php echo $address; ?></span></li>
                    <li><p>Phone number:</p> <span><?php echo $phone_number; ?></span></li>
                    <li><p>Email:</p> <span><?php echo $email; ?></span></li>
                    <li>
                    <p>Status:</p>
                        <div class="<?php echo ($is_verified == 1) ? 'verified' : 'not-verified'; ?>">
                         <span><?php echo ($is_verified == 1) ? 'verified' : 'not-verified'; ?></span>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="update-button-container">
                <button class="update-button" id="updateButton">Update</button>
            </div>
        </div>
        
    </section>

    <!-- Modal dialog -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <span class="close-button" id="closeModal">&times;</span>
            <h2>Update Information</h2>
            <!-- <p>Modal content can go here...</p> -->
        <form action="#" method="post">
            <input name="firstname" type="text" value="<?php echo $first_name; ?>" required />
            <input name="middlename" type="text" value="<?php echo $middle_name; ?>" required />
            <input name="lastname" type="text" value="<?php echo $last_name; ?>" required />
            <input type="date" id="birthdate" name="birthdate" value="<?php echo $birthday; ?>" required>
            <input name="phonenumber" type="text" value="<?php echo $phone_number; ?>" required />
            <input name="email" type="email" value="<?php echo $email; ?>" required />
            <!-- <input name="password" type="password" placeholder="Password" required /> -->
            <button class="update-btn" name="update" type="submit">Update</button>
        </form>
        </div>
    </div>

    </main>
    

    <script>
// Get the modal
const modal = document.getElementById("updateModal");
        // Get the button that opens the modal
        const updateButton = document.getElementById("updateButton");
        // Get the <span> element that closes the modal
        const closeButton = document.getElementById("closeModal");

        // When the user clicks the button, open the modal
        updateButton.onclick = function() {
            modal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal
        closeButton.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside the modal, close it
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
</script>
</body>
</html>