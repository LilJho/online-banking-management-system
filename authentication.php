<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Double Slider Sign in/up Form</title>
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css'><link rel="stylesheet" href="./styles/style.css">
  <?php
// Initialize error variable
$error = '';

/**
 * Logs data to the browser console.
 *
 * @param mixed $data The data to log.
 */
function console_log($data) {
    echo "<script>console.log(" . json_encode($data) . ");</script>";
}

/**
 * Handles form submission.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register'])) {
        // Handle registration
        $formData = [
            'firstname' => $_POST['firstname'] ?? null,
            'middlename' => $_POST['middlename'] ?? null,
            'lastname' => $_POST['lastname'] ?? null,
            'address' => $_POST['address'] ?? null,
            'gender' => $_POST['gender'] ?? null,
            'birthdate' => $_POST['birthdate'] ?? null,
            'phonenumber' => $_POST['phonenumber'] ?? null,
            'email' => $_POST['email'] ?? null,
            'password' => $_POST['password'] ?? null,
        ];

        // Log form data to the browser console (optional)
        console_log($formData);

        // Register the user
        register($formData);
    } 
}

/**
 * Registers a new user in the database.
 *
 * @param array $formData The form data containing user details.
 */
function register($formData) {
    // Database configuration
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

    console_log($formData['gender']);

    // Validate the gender field
    $gender = isset($formData['gender']) ? $formData['gender'] : '';
    if ($gender !== 'male' && $gender !== 'female') {
        echo "Invalid gender value. Please select 'male' or 'female'.";
        return;
    }

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO users (first_name, middle_name, last_name, birth_date, phone_number, email, pass, address, gender) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param(
        "sssssssss", 
        $formData['firstname'], 
        $formData['middlename'], 
        $formData['lastname'], 
        $formData['birthdate'], 
        $formData['phonenumber'], 
        $formData['email'], 
        $formData['password'],
        $formData['address'], 
        $formData['gender'],
    );

    // Execute the query
    if ($stmt->execute()) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>


</head>
<body>
<!-- partial:index.partial.html -->
<div class="container" id="container">
    <div class="form-container sign-up-container">
        <form class="create-user-form">
            <h1>Create Account</h1>
            <span>or use your email for registration</span>
            <input id="firstname" name="firstname" type="text" placeholder="First Name" required />
            <input id="middlename" name="middlename" type="text" placeholder="Middle Name" required />
            <input id="lastname" name="lastname" type="text" placeholder="Last Name" required />
            <input id="address" name="address" type="text" placeholder="Address" required />
            <select id="gender" name="gender" required>
                <option value="">Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
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
            <button name="register" type="submit">Sign Up</button>
        </form>
    </div>
    <div class="form-container sign-in-container">
    <form id="loginForm">
    <h1>Sign in</h1>
    <input name="login_email" type="email" placeholder="Email" required />
    <input name="login_pass" type="password" placeholder="Password" required />
    <a href="#">Forgot your password?</a>
    <button type="submit">Sign In</button>
</form>

    </div>
    <div class="overlay-container">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <h1>Welcome Back!</h1>
                <p>To keep connected with us please login with your personal info</p>
                <button class="ghost" id="signIn">Sign In</button>
            </div>
            <div class="overlay-panel overlay-right">
                <h1>Hello, Friend!</h1>
                <p>Enter your personal details and start journey with us</p>
                <button class="ghost" id="signUp">Sign Up</button>
            </div>
        </div>
    </div>
</div>
<!-- partial -->
  <script  src="./scripts/script.js">
  </script>
  <script src="/scripts/create-user.js"></script>
</body>
</html>