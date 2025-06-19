<?php
// Define the roles allowed to access this page
$allowed_roles = ['Admin', 'Module Leader'];

// Require the local admin header, which handles security
require_once 'templates/header.php';

// POST REQUEST HANDLING
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_class_entry') {
        // Updated INSERT statement for the new table structure
        $stmt = $conn->prepare("INSERT INTO Class_Schedule (Class_ID, Venue_ID, Day_Of_Week, Start_Time, End_Time) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiss", $_POST['class_id'], $_POST['venue_id'], $_POST['day_of_week'], $_POST['start_time'], $_POST['end_time']);
        $stmt->execute();
        $stmt->close();
    }
    if ($_POST['action'] === 'delete_class_entry') {
        $stmt = $conn->prepare("DELETE FROM Class_Schedule WHERE Entry_ID = ?");
        $stmt->bind_param("i", $_POST['entry_id']);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: admin_manage_schedule.php");
    exit();
}

// DATA FETCHING - Updated to fetch new columns and order by day, then time
$master_schedule_sql = "SELECT cs.Entry_ID, cs.Day_Of_Week, cs.Start_Time, cs.End_Time, c.Class_Name, v.Venue_Name 
                        FROM Class_Schedule cs
                        JOIN Classes c ON cs.Class_ID = c.Class_ID
                        JOIN Venues v ON cs.Venue_ID = v.Venue_ID
                        ORDER BY cs.Day_Of_Week, cs.Start_Time ASC";
$master_schedule = $conn->query($master_schedule_sql)->fetch_all(MYSQLI_ASSOC);
$all_classes = $conn->query("SELECT * FROM Classes ORDER BY Class_Name ASC")->fetch_all(MYSQLI_ASSOC);
$all_venues = $conn->query("SELECT * FROM Venues ORDER BY Venue_Name")->fetch_all(MYSQLI_ASSOC);

// Helper array to convert day number to name
$days_of_week = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];
?>

<h1 class="page-title">Manage Master Schedule</h1>
<p class="page-subtitle">This is the official weekly recurring schedule for all classes.</p>

<div class="form-container card">
    <h2 class="section-title">Add Weekly Recurring Entry</h2>
    <form method="POST" action="admin_manage_schedule.php">
        <input type="hidden" name="action" value="add_class_entry">
        <div class="form-group">
            <label for="class_id">Class:</label>
            <select name="class_id" id="class_id" class="form-input" required>
                <option value="">-- Select a Class --</option>
                <?php foreach($all_classes as $class): ?>
                    <option value="<?php echo $class['Class_ID']; ?>"><?php echo htmlspecialchars($class['Class_Name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="venue_id">Venue:</label>
            <select name="venue_id" id="venue_id" class="form-input" required>
                <option value="">-- Select a Venue --</option>
                <?php foreach($all_venues as $venue): ?>
                    <option value="<?php echo $venue['Venue_ID']; ?>"><?php echo htmlspecialchars($venue['Venue_Name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="day_of_week">Day of the Week:</label>
            <select name="day_of_week" id="day_of_week" class="form-input" required>
                <option value="">-- Select a Day --</option>
                <?php foreach($days_of_week as $num => $day): ?>
                    <option value="<?php echo $num; ?>"><?php echo $day; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="start_time">Start Time:</label>
            <input type="time" id="start_time" name="start_time" class="form-input" required>
        </div>
        <div class="form-group">
            <label for="end_time">End Time:</label>
            <input type="time" id="end_time" name="end_time" class="form-input" required>
        </div>
        
        <button type="submit" class="action-button">Add to Weekly Schedule</button>
    </form>
</div>

<div class="management-table card" style="margin-top: 2rem;">
    <h2 class="section-title">Master Weekly Schedule</h2>
    <table>
        <thead>
            <tr>
                <th>Day</th>
                <th>Time Slot</th>
                <th>Class</th>
                <th>Venue</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($master_schedule)): ?>
                <tr><td colspan="5">The master schedule is empty. Add an entry using the form above.</td></tr>
            <?php else: ?>
                <?php foreach ($master_schedule as $entry): ?>
                    <tr>
                        <td><strong><?php echo $days_of_week[$entry['Day_Of_Week']]; ?></strong></td>
                        <td><?php echo date('h:i A', strtotime($entry['Start_Time'])) . ' - ' . date('h:i A', strtotime($entry['End_Time'])); ?></td>
                        <td><?php echo htmlspecialchars($entry['Class_Name']); ?></td>
                        <td><?php echo htmlspecialchars($entry['Venue_Name']); ?></td>
                        <td>
                            <form method="POST" action="admin_manage_schedule.php" onsubmit="return confirm('Are you sure you want to delete this schedule entry?');">
                                <input type="hidden" name="action" value="delete_class_entry">
                                <input type="hidden" name="entry_id" value="<?php echo $entry['Entry_ID']; ?>">
                                <button type="submit" class="table-action-btn delete">&times;</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
require_once 'templates/footer.php';
?>