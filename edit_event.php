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

// Security: This page requires an event ID in the URL to know what to edit.
if (!isset($_GET['id'])) {
    header("Location: event.php"); // No ID provided, redirect to the event list.
    exit();
}

// Get the event ID from the URL.
$event_id_to_edit = $_GET['id'];
$user_fname = $_SESSION['user_fname'];
$message = ''; // For displaying success or error messages.

// Set the active page for dynamic navigation menu highlighting.
$active_page = 'event';


// ======================================================================
//  2. POST REQUEST HANDLING (FORM SUBMISSION)
// ======================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get all data from the submitted form.
    $event_name = $_POST['event_name'];
    $event_description = $_POST['event_description'];
    $event_date = $_POST['event_date'];
    $venue_id = $_POST['venue_id'];

    // Prepare a secure UPDATE query to prevent SQL injection.
    $stmt = $conn->prepare("UPDATE Events SET Event_Name = ?, Event_Description = ?, Event_Date = ?, Venue_ID = ? WHERE Event_ID = ?");
    $stmt->bind_param("sssii", $event_name, $event_description, $event_date, $venue_id, $event_id_to_edit);

    // Execute the query and redirect on success.
    if ($stmt->execute()) {
        header("Location: event.php?update=success");
        exit();
    } else {
        $message = "Error: Could not update the event. Please try again.";
    }
    $stmt->close();
}


// ======================================================================
//  3. DATA FETCHING FOR FORM
// ======================================================================

// Fetch the existing data for the specific event we are editing.
$stmt_event = $conn->prepare("SELECT * FROM Events WHERE Event_ID = ?");
$stmt_event->bind_param("i", $event_id_to_edit);
$stmt_event->execute();
$event_to_edit = $stmt_event->get_result()->fetch_assoc();
$stmt_event->close();

// If no event was found with that ID, it's invalid. Redirect back to the list.
if (!$event_to_edit) {
    header("Location: event.php");
    exit();
}

// Fetch all available venues to populate the dropdown menu.
$venues = $conn->query("SELECT * FROM Venues ORDER BY Venue_Name ASC")->fetch_all(MYSQLI_ASSOC);

// Close the database connection.
$conn->close();

// ======================================================================
//  4. HTML PRESENTATION
// ======================================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - DueDay</title>
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
                    <h1>Edit Event</h1>
                    <p>Update the details for: <?php echo htmlspecialchars($event_to_edit['Event_Name']); ?></p>
                </div>
            </div>

            <div class="form-container">
                <?php if ($message): ?>
                    <p class="message error"><?php echo $message; ?></p>
                <?php endif; ?>

                <form method="POST" action="edit_event.php?id=<?php echo $event_to_edit['Event_ID']; ?>">
                    <div class="form-group">
                        <label for="event_name">Event Name:</label>
                        <input type="text" id="event_name" name="event_name" class="form-input" value="<?php echo htmlspecialchars($event_to_edit['Event_Name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="event_date">Date and Time:</label>
                        <input type="datetime-local" id="event_date" name="event_date" class="form-input" value="<?php echo date('Y-m-d\TH:i', strtotime($event_to_edit['Event_Date'])); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="venue_id">Venue:</label>
                        <select id="venue_id" name="venue_id" class="form-input" required>
                            <option value="">-- Select a Venue --</option>
                            <?php foreach ($venues as $venue): ?>
                                <option value="<?php echo $venue['Venue_ID']; ?>" <?php if ($venue['Venue_ID'] == $event_to_edit['Venue_ID']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($venue['Venue_Name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="event_description">Description:</label>
                        <textarea id="event_description" name="event_description" class="form-input" rows="4"><?php echo htmlspecialchars($event_to_edit['Event_Description']); ?></textarea>
                    </div>
                    <div class="form-actions">
                         <a href="event.php" class="action-button secondary" style="text-decoration:none;">Cancel</a>
                         <button type="submit" class="action-button">Save Changes</button>
                    </div>
                </form>
            </div>
        </div> </div> </body>
</html>