<!--Sarah Amond-->
<!--C00308719-->
<?php
session_start(); // Start or resume the PHP session to manage user login state
require 'db.php'; // Include the external database connection file
$error = ''; // Initialize an empty error message variable for failed login attempts

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Check if the form was submitted via POST
    $username = trim($_POST['username']); // Retrieve and trim whitespace from the username input
    $password = $_POST['password']; // Retrieve the submitted password input

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?"); // Prepare an SQL statement to look up the user by username
    $stmt->execute([$username]); // Execute the statement with the username parameter
    $user = $stmt->fetch(); // Fetch the user record as an associative array

    if ($user && password_verify($password, $user['password'])) { // Verify that the user exists and the password matches the stored hash
        $_SESSION['user_id'] = $user['user_id']; // Store the user's ID in the session
        $_SESSION['user_name'] = $user['full_name']; // Store the user's full name in the session
        header("Location: user_dashboard.php"); // Redirect the authenticated user to their dashboard
        exit; // Stop further script execution after redirect
    } else { $error = "Invalid user credentials."; } // Set an error message if authentication fails
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- Sets the character encoding to UTF-8 -->
    <title>User Login Portal</title> <!-- Defines the browser tab title -->
    <link rel="stylesheet" href="user.css"> <!-- Links the external stylesheet for user pages -->
</head>
<body>
    <div class="container" style="max-width: 450px;"> <!-- Main form container with a custom maximum width -->
        <h2>User Login</h2> <!-- Heading for the login page -->
        <?php if($error): ?> <div class="error-msg"><?= $error ?></div> <?php endif; ?> <!-- Display error message if authentication fails -->
        
        <form method="POST" action="user_login.php"> <!-- Form submitted via POST to process login -->
            <div class="form-group"> <!-- Form group wrapper for the username field -->
                <label>Username</label> <!-- Label for the username input -->
                <input type="text" name="username" required> <!-- Text input for entering username -->
            </div>
            <div class="form-group"> <!-- Form group wrapper for the password field -->
                <label>Password</label> <!-- Label for the password input -->
                <input type="password" name="password" required> <!-- Password input for entering password -->
            </div>
            <button type="submit" class="btn btn-secondary" style="width: 100%;">Login</button> <!-- Submit button spanning full container width -->
        </form>
        <p style="margin-top: 15px; text-align: center;">New user? <a href="register.php">Register here</a></p> <!-- Link directing new users to the registration page -->
    </div>
    <script src="validation.js"></script> <!-- Links external JavaScript for client-side form validation -->
</body>
</html>