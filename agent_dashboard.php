<!--Sarah Amond-->
<!--C00308719-->
<?php
session_start();
require 'db.php';// use the database
if (!isset($_SESSION['agent_id'])) { header("Location: agent_login.php"); exit; }//use this item from this location

$search = $_GET['search'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resolve_ticket'])) { // Check if the form was submitted via POST and the 'resolve_ticket' action was triggered
    $ticket_id = $_POST['ticket_id'];
    $resolution = trim($_POST['resolution_details']);
    $stmt = $pdo->prepare("UPDATE tickets SET status = 'Resolved', resolution_details = ?, assigned_agent_id = ? WHERE ticket_id = ?");// Prepare an SQL statement to update the ticket's status, resolution notes, and the agent handling it
    $stmt->execute([$resolution, $_SESSION['agent_id'], $ticket_id]);// Execute the query securely using parameterized values to prevent SQL injection
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['elevate_ticket'])) {// Check if the form was submitted using the POST method and the 'elevate_ticket' action was triggered
    $ticket_id = $_POST['ticket_id'];
    $stmt = $pdo->prepare("UPDATE tickets SET status = 'Open', description = CONCAT('[ELEVATED] ', description) WHERE ticket_id = ?");// Prepare an SQL statement to change the ticket status to 'Open' and prepend '[ELEVATED] ' to the existing description to flag the ticket
    $stmt->execute([$ticket_id]);
}

if (!empty($search)) { // Check if a search query has been provided
    $stmt = $pdo->prepare("SELECT t.*, u.full_name FROM tickets t JOIN users u ON t.user_id = u.user_id WHERE t.status = 'Open' AND (t.subject LIKE ? OR t.description LIKE ?)");// Prepare a secure query to search open tickets by subject or description
    $stmt->execute(["%$search%", "%$search%"]);
} else {// If no search query, fetch all open tickets joined with user details
    $stmt = $pdo->query("SELECT t.*, u.full_name FROM tickets t JOIN users u ON t.user_id = u.user_id WHERE t.status = 'Open'");
}
$open_tickets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Support Agent Management Console</title>
    <link rel="stylesheet" href="agent_dash.css">
</head>
<body>
    <div class="container"> 
        <div class="header-bar"> <!-- Top header bar containing title, agent name, and logout button -->
            <h1>Support Area Console</h1>
            <div>
                <span class="user-tag" style="color: #87564B;">Agent: <?= htmlspecialchars($_SESSION['agent_name']) ?></span> 
                <a href="logout.php" class="btn btn-danger" style="padding: 6px 12px; font-size: 0.85rem;">Logout</a>
            </div>
        </div>

        <!-- Search Bar Area -->
        <form method="GET" action="agent_dashboard.php" style="display: flex; gap: 10px; margin-bottom: 30px;">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search Open Tickets..." style="flex-grow: 1;">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <h3>Active Queue: Search Open Tickets</h3> <!-- Check if there are any open tickets to display -->
        <?php if(empty($open_tickets)): ?>
            <p style="margin-top: 15px;">No active open tickets match criteria.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Subject / Issue</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($open_tickets as $ticket): ?>
                        <tr>
                            <td style="width: 150px;"><strong><?= htmlspecialchars($ticket['full_name']) ?></strong></td><!-- Display the name of the user who submitted the ticket -->
                            <td>
                                <strong><?= htmlspecialchars($ticket['subject']) ?></strong>
                                <p style="font-size: 0.85rem; color: #6D322A; margin-top: 4px;"><?= htmlspecialchars($ticket['description']) ?></p>
                            </td>
                            <td><span class="badge badge-open"><?= $ticket['status'] ?></span></td>
                            <td style="width: 320px;">
                                <!-- Actions Box Layout -->
                                <form method="POST" action="agent_dashboard.php" style="margin-bottom: 6px;">
                                    <input type="hidden" name="ticket_id" value="<?= $ticket['ticket_id'] ?>">
                                    <input type="text" name="resolution_details" placeholder="Provide solution detail..." required style="padding: 6px; width: 180px; font-size: 0.85rem; margin-right: 4px;">
                                    <button type="submit" name="resolve_ticket" class="btn btn-success" style="padding: 6px 12px; font-size: 0.85rem;">Resolve</button>
                                </form>
                                <!-- Form to elevate the ticket tier -->
                                <form method="POST" action="agent_dashboard.php">
                                    <input type="hidden" name="ticket_id" value="<?= $ticket['ticket_id'] ?>">
                                    <button type="submit" name="elevate_ticket" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.85rem; width: 100%;">Elevate Ticket Tier</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
