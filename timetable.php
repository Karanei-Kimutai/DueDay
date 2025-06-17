<?php
require_once 'templates/header.php';

// POST REQUEST HANDLING
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_class_entry') {
        $stmt = $conn->prepare("INSERT INTO Class_Schedule (Class_ID, Venue_ID, Class_Time) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $_POST['class_id'], $_POST['venue_id'], $_POST['class_time']);
        $stmt->execute();
        $stmt->close();
        $stmt_link = $conn->prepare("INSERT IGNORE INTO User_Classes (User_ID, Class_ID) VALUES (?, ?)");
        $stmt_link->bind_param("ii", $user_id, $_POST['class_id']);
        $stmt_link->execute();
        $stmt_link->close();
    }
    if ($_POST['action'] === 'delete_class_entry') {
        $stmt = $conn->prepare("DELETE FROM Class_Schedule WHERE Entry_ID = ?");
        $stmt->bind_param("i", $_POST['entry_id']);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: timetable.php");
    exit();
}

// DATA FETCHING
$user_schedule_sql = "SELECT cs.Entry_ID, DAYOFWEEK(cs.Class_Time) as DayOfWeek, TIME(cs.Class_Time) as TimeOnly, c.Class_Name, v.Venue_Name FROM Class_Schedule cs JOIN Classes c ON cs.Class_ID = c.Class_ID JOIN Venues v ON cs.Venue_ID = v.Venue_ID JOIN User_Classes uc ON c.Class_ID = uc.Class_ID WHERE uc.User_ID = ? ORDER BY DayOfWeek, TimeOnly ASC";
$stmt = $conn->prepare($user_schedule_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$schedule_result = $stmt->get_result();
$schedule_by_day = [];
while ($row = $schedule_result->fetch_assoc()) {
    $schedule_by_day[$row['DayOfWeek']][] = $row;
}
$stmt->close();
$all_classes = $conn->query("SELECT * FROM Classes ORDER BY Class_Name")->fetch_all(MYSQLI_ASSOC);
$all_venues = $conn->query("SELECT * FROM Venues ORDER BY Venue_Name")->fetch_all(MYSQLI_ASSOC);
?>

<div class="page-actions"><button id="showCreateBtn" class="btn btn--primary">Add Class to Schedule</button></div>

<div class="form-container" id="createForm" style="display:none;">
    <h2 class="section-title">Add a Class to Your Timetable</h2>
    <form method="POST" action="timetable.php">
        <input type="hidden" name="action" value="add_class_entry">
        <div class="form-group"><label for="class_id">Class:</label><select name="class_id" id="class_id" class="form-input" required><option value="">-- Select a Class --</option><?php foreach($all_classes as $class): ?><option value="<?php echo $class['Class_ID']; ?>"><?php echo htmlspecialchars($class['Class_Name']); ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label for="venue_id">Venue:</label><select name="venue_id" id="venue_id" class="form-input" required><option value="">-- Select a Venue --</option><?php foreach($all_venues as $venue): ?><option value="<?php echo $venue['Venue_ID']; ?>"><?php echo htmlspecialchars($venue['Venue_Name']); ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label for="class_time">Date and Time:</label><input type="datetime-local" id="class_time" name="class_time" class="form-input" required></div>
        <button type="submit" class="btn btn--primary">Add to My Schedule</button>
    </form>
</div>

<div class="timetable-container card">
    <div class="page-actions" style="border-bottom: 1px solid var(--medium-gray); padding-bottom: 20px; margin-bottom: 20px;">
        <h2 class="section-title" style="margin-bottom: 0;">My Weekly Schedule</h2>
        <button class="btn btn--secondary" id="switchViewBtn">Day View</button>
    </div>
    
    <div class="weekly-timetable" id="weeklyView">
        <div class="day-column">
            <div class="day-header">Monday</div>
            <?php if (!empty($schedule_by_day[2])): foreach($schedule_by_day[2] as $class): ?>
                <div class="class-block"><strong><?php echo date('h:ia', strtotime($class['TimeOnly'])); ?></strong><br><?php echo htmlspecialchars($class['Class_Name']); ?><br><small><?php echo htmlspecialchars($class['Venue_Name']); ?></small></div>
            <?php endforeach; else: echo '<p class="no-classes-msg">No classes.</p>'; endif; ?>
        </div>
        <div class="day-column"><div class="day-header">Tuesday</div><?php if (!empty($schedule_by_day[3])): foreach($schedule_by_day[3] as $class): ?><div class="class-block"><strong><?php echo date('h:ia', strtotime($class['TimeOnly'])); ?></strong><br><?php echo htmlspecialchars($class['Class_Name']); ?><br><small><?php echo htmlspecialchars($class['Venue_Name']); ?></small></div><?php endforeach; else: echo '<p class="no-classes-msg">No classes.</p>'; endif; ?></div>
        <div class="day-column"><div class="day-header">Wednesday</div><?php if (!empty($schedule_by_day[4])): foreach($schedule_by_day[4] as $class): ?><div class="class-block"><strong><?php echo date('h:ia', strtotime($class['TimeOnly'])); ?></strong><br><?php echo htmlspecialchars($class['Class_Name']); ?><br><small><?php echo htmlspecialchars($class['Venue_Name']); ?></small></div><?php endforeach; else: echo '<p class="no-classes-msg">No classes.</p>'; endif; ?></div>
        <div class="day-column"><div class="day-header">Thursday</div><?php if (!empty($schedule_by_day[5])): foreach($schedule_by_day[5] as $class): ?><div class="class-block"><strong><?php echo date('h:ia', strtotime($class['TimeOnly'])); ?></strong><br><?php echo htmlspecialchars($class['Class_Name']); ?><br><small><?php echo htmlspecialchars($class['Venue_Name']); ?></small></div><?php endforeach; else: echo '<p class="no-classes-msg">No classes.</p>'; endif; ?></div>
        <div class="day-column"><div class="day-header">Friday</div><?php if (!empty($schedule_by_day[6])): foreach($schedule_by_day[6] as $class): ?><div class="class-block"><strong><?php echo date('h:ia', strtotime($class['TimeOnly'])); ?></strong><br><?php echo htmlspecialchars($class['Class_Name']); ?><br><small><?php echo htmlspecialchars($class['Venue_Name']); ?></small></div><?php endforeach; else: echo '<p class="no-classes-msg">No classes.</p>'; endif; ?></div>
    </div>

    <div class="daily-schedule" id="dailyView" style="display: none;">
        <div class="day-navigation"><button class="btn btn--secondary prev-day">&lt; Prev</button><h3 id="current-day"></h3><button class="btn btn--secondary next-day">Next &gt;</button></div>
        <div id="dailyScheduleContainer"></div>
    </div>
</div>

<script id="schedule-data" type="application/json"><?php echo json_encode($schedule_by_day); ?></script>

<?php 
require_once 'templates/footer.php';
$conn->close();
?>