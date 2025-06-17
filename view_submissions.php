<?php
// ======================================================================
//  1. INITIALIZATION & SECURITY
// ======================================================================

require_once 'connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_role = $_SESSION['role_name'];
if ($user_role !== 'Module Leader' && $user_role !== 'Admin') {
    header("Location: home.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: assignment.php");
    exit();
}

$assignment_id = (int)$_GET['id'];
$message = '';
$active_page = 'assignment';

// ======================================================================
//  2. POST REQUEST HANDLING (SAVING GRADES)
// ======================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_grade') {
    $submission_id = $_POST['submission_id'];
    $grade = $_POST['grade'];
    $feedback = $_POST['feedback'];

    $stmt = $conn->prepare("UPDATE Assignment_Submission_Data SET Grade = ?, Feedback = ? WHERE Submission_ID = ?");
    $stmt->bind_param("ssi", $grade, $feedback, $submission_id);

    if ($stmt->execute()) {
        header("Location: view_submissions.php?id=" . $assignment_id . "&save=success");
        exit();
    } else {
        $message = "Error: Could not save the grade.";
    }
    $stmt->close();
}

if (isset($_GET['save']) && $_GET['save'] === 'success') {
    $message = "Grade saved successfully!";
}

// ======================================================================
//  3. DATA FETCHING FOR DISPLAY
// ======================================================================

$stmt_assignment = $conn->prepare("SELECT Assignment_Title FROM Assignments WHERE Assignment_ID = ?");
$stmt_assignment->bind_param("i", $assignment_id);
$stmt_assignment->execute();
$assignment = $stmt_assignment->get_result()->fetch_assoc();
$stmt_assignment->close();

if (!$assignment) {
    header("Location: assignment.php");
    exit();
}

$submissions = [];
$sql = "SELECT asd.*, u.F_Name, u.L_Name
        FROM Assignment_Submission_Data asd
        JOIN Users u ON asd.User_ID = u.User_ID
        WHERE asd.Assignment_ID = ?
        ORDER BY u.L_Name ASC";
$stmt_submissions = $conn->prepare($sql);
$stmt_submissions->bind_param("i", $assignment_id);
$stmt_submissions->execute();
$result = $stmt_submissions->get_result();
if ($result) {
    $submissions = $result->fetch_all(MYSQLI_ASSOC);
}
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
    <title>View Submissions - DueDay</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="assets/icons/dueday.png" type="image/x-icon">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo"><img src="assets/icons/dueday.png" alt="DueDay Logo" class="logo-icon"><span>DUEDAY</span></div>
            <ul class="nav-menu">
                <li class="nav-item <?php echo ($active_page === 'home') ? 'active' : ''; ?>"><a href="home.php"><img src="assets/icons/home.png" alt="Home" class="nav-icon"><span>Home</span></a></li>
                <li class="nav-item <?php echo ($active_page === 'assignment') ? 'active' : ''; ?>"><a href="assignment.php"><img src="assets/icons/assignment.png" alt="Assignments" class="nav-icon"><span>Assignments</span></a></li>
                <li class="nav-item <?php echo ($active_page === 'poll') ? 'active' : ''; ?>"><a href="poll.php"><img src="assets/icons/poll.png" alt="Polls" class="nav-icon"><span>Polls</span></a></li>
                <li class="nav-item <?php echo ($active_page === 'event') ? 'active' : ''; ?>"><a href="event.php"><img src="assets/icons/event.png" alt="Events" class="nav-icon"><span>Events</span></a></li>
                <li class="nav-item <?php echo ($active_page === 'timetable') ? 'active' : ''; ?>"><a href="timetable.php"><img src="assets/icons/table.png" alt="Timetable" class="nav-icon"><span>Timetable</span></a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="welcome-header">
                <div class="greeting"><h1>Submissions</h1><p>For assignment: <strong><?php echo htmlspecialchars($assignment['Assignment_Title']); ?></strong></p></div>
            </div>

            <?php if ($message): ?>
                <p class="message-banner success"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <div class="management-table">
                <form method="POST" action="view_submissions.php?id=<?php echo $assignment_id; ?>">
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Submission Date</th>
                            <th>Submitted File</th>
                            <th>Grade</th>
                            <th>Feedback</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($submissions)): ?>
                            <tr><td colspan="6">No submissions for this assignment yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($submissions as $sub): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($sub['F_Name'] . ' ' . $sub['L_Name']); ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($sub['Submission_Date'])); ?></td>
                                    <td><a href="<?php echo htmlspecialchars($sub['File_Path']); ?>" class="btn" target="_blank" download>Download</a></td>
                                    <td><input type="text" name="grade[<?php echo $sub['Submission_ID']; ?>]" value="<?php echo htmlspecialchars($sub['Grade']); ?>" class="form-input" style="max-width: 100px;"></td>
                                    <td><textarea name="feedback[<?php echo $sub['Submission_ID']; ?>]" class="form-input" rows="1"><?php echo htmlspecialchars($sub['Feedback']); ?></textarea></td>
                                    <td>
                                        <button type="submit" name="action" value="save_grade" formaction="view_submissions.php?id=<?php echo $assignment_id; ?>&submission_id=<?php echo $sub['Submission_ID']; ?>" class="btn">Save</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                </form>
                 <div class="page-actions" style="margin-top: 20px;">
                    <a href="assignment.php" class="btn btn--secondary" style="text-decoration:none;">Back to Assignments</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>