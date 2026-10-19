<!--Sarah Amond-->
<!--C00308719-->
<?php
require 'db.php'; // Include the database connection file
$message = ''; // Initialize an empty message variable for status feedback
<link rel="stylesheet" href="user.css"> // Include the external CSS stylesheet for styling

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Check if the form was submitted via POST
    $username = trim($_POST['username']); // Retrieve and trim whitespace from the username input
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Securely hash the password using a strong default algorithm
    $full_name = trim($_POST['full_name']); // Retrieve and trim whitespace from the full name input

    if (!empty($username) && !empty($password) && !empty($full_name)) { // Check that none of the required fields are empty
        $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name) VALUES (?, ?, ?)"); // Prepare an SQL statement to insert the new user record securely
        try {
            $stmt->execute([$username, $password, $full_name]); // Execute the statement with user input parameters
            header("Location: user_login.php"); // Redirect the successfully registered user to the login page
            exit; // Stop further script execution after redirect
        } catch (PDOException $e) {
            $message = "Username already exists!"; // Catch database errors, typically duplicate username constraint violations
        }
    } else {
        $message = "Please fill in all fields."; // Set an error message if any required field is empty
    }
}
?>
<!-- HTML Client Interface omitted for code compression according to report specification guidelines -->
