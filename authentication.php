<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Double Slider Sign in/up Form</title>
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css'><link rel="stylesheet" href="./styles/style.css">


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
            <!-- <select id="account" name="account" required>
                <option value="">Type of Account</option>
                <option value="savings">Savings</option>
                <option value="credit">Credit</option>
                <option value="loan">Loan</option>
            </select>
            <input id="balance" name="balance" type="number" placeholder="Balance" required /> -->
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
    <button type="button" id="forgot-password-btn">Forgot your password?</button>
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

<div id="forgot-password" class="forgot-password-modal">
      <div class="forgot-password-modal-content">
        <span class="close-forgot-password-btn" id="close-forgot-password-btn">&times;</span>
        <h2>Enter Email</h2>
        <form class="forgot-password-form">
          <input
            id="forgot-password-email"
            name="forgot-password-email"
            type="email"
            placeholder="Email"
            required
          />
          <button class="forgot-password-to-db" name="forgot-password-to-db" type="submit">
            Send
          </button>
        </form>
      </div>
    </div>
<!-- partial -->
  <script  src="scripts/script.js">
  </script>
  <script src="scripts/create-user.js"></script>
</body>
</html>