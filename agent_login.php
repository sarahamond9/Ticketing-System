<!--Sarah Amond-->
<!--C00308719-->
<?php
session_start();// Start or resume a PHP session to manage user login state
require 'db.php';// Include the database connection file
$error = '';// Initialize an empty error message variable

// Check if the form was submitted using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);    // Retrieve and trim whitespace from the entered username
    $password = $_POST['password'];    // Retrieve the submitted password
    $stmt = $pdo->prepare("SELECT * FROM agents WHERE username = ?");   // Prepare a secure SQL query to find the agent by their username
    $stmt->execute([$username]);
        $agent = $stmt->fetch();// Fetch the agent record from the database

    // Verify that the agent exists and that the entered password matches the stored password hash
    if ($agent && password_verify($password, $agent['password'])) {
        // Store agent ID and full name in session variables to keep them logged in
        $_SESSION['agent_id'] = $agent['agent_id'];
        $_SESSION['agent_name'] = $agent['full_name'];
    
        // Redirect the authenticated agent to their dashboard
        header("Location: agent_dashboard.php");
        exit; // Stop further script execution after redirect
    } else {
        $error = "Invalid agent credentials.";// Set an error message if authentication fails

    }
}
?>