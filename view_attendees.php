<?php
// ======================================================================
//  1. INITIALIZATION & SECURITY
// ======================================================================

require_once 'connection.php';
session_start();

// Security: If the user is not logged in, redirect to the login page.
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Security: This page requires a specific role (Event Coordinator or Admin).
$user_role = $_SESSION['role_name'];
if ($user_role !== 'Event Coordinator' && $user_role !== 'Admin') {
    header("Location: home.php"); // Not authorized, redirect to home.
    exit();
}

// Security: This page requires an event ID in the URL to know which list to show.
if (!isset($_GET['id'])) {
    header("Location: event.php"); // No ID provided, redirect to the event list.
    exit();
}

// Get the event ID from the URL.
$event_id = (int)$_GET['id'];
$user_fname = $_SESSION['user_fname'];

// Set the active page for dynamic navigation menu highlighting.
$active_page = 'event';


// ======================================================================
//  2. DATA FETCHING
// ======================================================================

// First, get the event's name for the page header.
$stmt_event = $conn->prepare("SELECT Event_Name FROM Events WHERE Event_ID = ?");
$stmt_event->bind_param("i", $event_id);
$stmt_event->execute();
$event = $stmt_event->get_result()->fetch_assoc();
$stmt_event->close();

// If no event was found with that ID, it's invalid. Redirect back to the list.
if (!$event) {
    header("Location: event.php");
    exit();
}

// Now, get the list of all attendees for this event by joining tables.
$attendees = [];
$sql = "SELECT u.F_Name, u.L_Name, u.Email
        FROM Users u
        JOIN Event_Attendee_Data ead ON u.User_ID = ead.User_ID
        WHERE ead.Event_ID = ?
        ORDER BY u.L_Name, u.F_Name";
$stmt_attendees = $conn->prepare($sql);
$stmt_attendees->bind_param("i", $event_id);
$stmt_attendees->execute();
$result = $stmt_attendees->get_result();
if ($result) {
    $attendees = $result->fetch_all(MYSQLI_ASSOC);
}
$stmt_attendees->close();

// Close the database connection.
$conn->close();

// ======================================================================
//  3. HTML PRESENTATION
// ======================================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendee List - DueDay</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="assets/icons/dueday.png" type="image/x-icon">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo">
                <img src="assets/icons/dueday.png" alt="DueDay Logo" class="logo-icon">
                <span>DUEDAY</span>
            </div>
            <ul class="nav-menu">
                <li class="nav-item <?php echo ($active_page === 'home') ? 'active' : ''; ?>">
                    <a href="home.php"><img src="assets/icons/home.png" alt="Home" class="nav-icon"><span>Home</span></a>
                </li>
                <li class="nav-item <?php echo ($active_page === 'assignment') ? 'active' : ''; ?>">
                    <a href="assignment.php"><img src="assets/icons/assignment.png" alt="Assignments" class="nav-icon"><span>Assignments</span></a>
                </li>
                <li class="nav-item <?php echo ($active_page === 'poll') ? 'active' : ''; ?>">
                    <a href="poll.php"><img src="assets/icons/poll.png" alt="Polls" class="nav-icon"><span>Polls</span></a>
                </li>
                <li class="nav-item <?php echo ($active_page === 'event') ? 'active' : ''; ?>">
                    <a href="event.php"><img src="assets/icons/event.png" alt="Events" class="nav-icon"><span>Events</span></a>
                </li>
                <li class="nav-item <?php echo ($active_page === 'timetable') ? 'active' : ''; ?>">
                    <a href="timetable.php"><img src="assets/icons/table.png" alt="Timetable" class="nav-icon"><span>Timetable</span></a>
                </li>
            </ul>
        </div>

        <div class="main-content">
            <div class="welcome-header">
                <div class="greeting">
                    <h1>Attendee List</h1>
                    <p>For event: <strong><?php echo htmlspecialchars($event['Event_Name']); ?></strong></p>
                </div>
            </div>

            <div class="management-table">
                <h2 class="section-title">Total RSVPs: <?php echo count($attendees); ?></h2>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($attendees)): ?>
                            <tr><td colspan="4">No one has RSVP'd to this event yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($attendees as $index => $attendee): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($attendee['F_Name']); ?></td>
                                    <td><?php echo htmlspecialchars($attendee['L_Name']); ?></td>
                                    <td><?php echo htmlspecialchars($attendee['Email']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                 <div class="page-actions" style="margin-top: 20px;">
                    <a href="event.php" class="action-button secondary" style="text-decoration:none;">Back to Events</a>
                </div>
            </div>
        </div> </div> </body>
</html>