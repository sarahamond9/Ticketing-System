<!--Sarah Amond-->
<!--C00308719-->
<?php
session_start(); // Start or resume the PHP session for managing user login state
require 'db.php'; // Include the external database connection file
if (!isset($_SESSION['user_id'])) { header("Location: user_login.php"); exit; } // Redirect to login if user is not authenticated

$user_id = $_SESSION['user_id']; // Store the current logged-in user's ID in a variable

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['open_ticket'])) { // Check if the form was submitted to open a new ticket via POST
    $subject = trim($_POST['subject']); // Retrieve and trim whitespace from the ticket subject input
    $description = trim($_POST['description']); // Retrieve and trim whitespace from the ticket description input
    if (!empty($subject) && !empty($description)) { // Ensure both subject and description fields are not empty
        $stmt = $pdo->prepare("INSERT INTO tickets (user_id, subject, description, status) VALUES (?, ?, ?, 'Open')"); // Prepare an SQL statement to insert a new open ticket
        $stmt->execute([$user_id, $subject, $description]); // Execute the prepared insert statement with user input parameters
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['close_ticket_id'])) { // Check if the form was submitted to close an existing ticket
    $ticket_id = $_POST['close_ticket_id']; // Retrieve the ticket ID designated for closure
    $stmt = $pdo->prepare("UPDATE tickets SET status = 'Closed' WHERE ticket_id = ? AND user_id = ?"); // Prepare an SQL statement to update the ticket status to 'Closed' securely
    $stmt->execute([$ticket_id, $user_id]); // Execute the statement ensuring the ticket belongs to the current user
}

$stmt = $pdo->prepare("SELECT * FROM tickets WHERE user_id = ? ORDER BY date_submitted DESC"); // Prepare a query to fetch all tickets belonging to the user sorted by submission date descending
$stmt->execute([$user_id]); // Execute the fetch query with the user ID parameter
$my_tickets = $stmt->fetchAll(); // Retrieve all user tickets as an associative array
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- Sets the character encoding to UTF-8 -->
    <title>User Menu Dashboard</title> <!-- Defines the browser tab title -->
    <link rel="stylesheet" href="user_dash.css"> <!-- Links the external dashboard stylesheet -->
</head>
<body>
    <div class="container"> <!-- Main wrapper container for the dashboard layout -->
        <div class="header-bar"> <!-- Header container holding title and user information -->
            <h1>User Control Panel</h1> <!-- Main page header heading -->
            <div>
                <span class="user-tag">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></span> | <!-- Greets the user safely escaping output -->
                <a href="logout.php" class="btn btn-danger" style="padding: 6px 12px; font-size: 0.85rem;">Logout</a> <!-- Logout button link -->
            </div>
        </div>

        <div class="split-view"> <!-- Grid or flex layout container dividing the view into two columns -->
            <!-- Left Column: Create Ticket Form -->
            <div>
                <h3>Open a New Support Ticket</h3> <!-- Section header for ticket creation -->
                <form method="POST" action="user_dashboard.php"> <!-- Form submitted via POST to open a ticket -->
                    <div class="form-group"> <!-- Form group container for subject input -->
                        <label>Ticket Subject</label> <!-- Label for the subject field -->
                        <input type="text" name="subject" required placeholder="Brief summary of your problem"> <!-- Text input for ticket subject -->
                    </div>
                    <div class="form-group"> <!-- Form group container for description textarea -->
                        <label>Detailed Description</label> <!-- Label for the description field -->
                        <textarea name="description" rows="6" required placeholder="Describe the problem here..."></textarea> <!-- Textarea for detailed problem description -->
                    </div>
                    <button type="submit" name="open_ticket" class="btn btn-primary">Submit Ticket</button> <!-- Submit button to finalize opening the ticket -->
                </form>
            </div>

            <!-- Right Column: List Existing Tickets -->
            <div>
                <h3>Check My Tickets</h3> <!-- Section header for existing ticket list -->
                <?php if(empty($my_tickets)): ?> <!-- Check if the user has any logged tickets -->
                    <p>No logged tickets found.</p> <!-- Fallback message when no tickets exist -->
                <?php else: ?>
                    <?php foreach($my_tickets as $ticket): ?> <!-- Loop through each ticket associated with the user -->
                        <div style="border: 1px solid #e5e8e8; padding: 15px; border-radius: 6px; margin-bottom: 15px;"> <!-- Individual ticket card wrapper style -->
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;"> <!-- Inner header row for subject and status badge -->
                                <strong><?= htmlspecialchars($ticket['subject']) ?></strong> <!-- Safely output the ticket subject -->
                                <span class="badge badge-<?= strtolower($ticket['status']) ?>"><?= $ticket['status'] ?></span> <!-- Dynamic status badge class and text -->
                            </div>
                            <p style="font-size: 0.9rem; color: #555; margin-bottom: 10px;"><?= htmlspecialchars($ticket['description']) ?></p> <!-- Safely output the ticket description -->
                            
                            <?php if($ticket['status'] === 'Resolved'): ?> <!-- Check if the ticket status is marked as Resolved -->
                                <div style="background: #f4f6f9; padding: 10px; border-radius: 4px; font-size: 0.85rem; margin-bottom: 10px;"> <!-- Resolution info message container -->
                                    <strong>Resolution:</strong> <?= htmlspecialchars($ticket['resolution_details']) ?> <!-- Safely display the agent's resolution notes -->
                                </div>
                                <form method="POST" action="user_dashboard.php"> <!-- Form to allow users to close a resolved ticket -->
                                    <input type="hidden" name="close_ticket_id" value="<?= $ticket['ticket_id'] ?>"> <!-- Hidden input containing the specific ticket ID -->
                                    <button type="submit" class="btn btn-danger" style="padding: 5px 10px; font-size: 0.8rem;">Close Ticket</button> <!-- Button to trigger ticket closure -->
                                </form>