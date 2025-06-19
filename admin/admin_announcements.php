<?php
require_once 'templates/header.php'; // The header already includes the main <head> tag

$message = '';
$message_type = 'error'; // Default to error

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'send_global_announcement') {
    $title = $_POST['announcement_title'];
    $description = $_POST['announcement_description'];
    $notification_content = $title . ": " . $description;

    $conn->begin_transaction();
    try {
        // 1. Insert the main notification record
        $stmt_notification = $conn->prepare("INSERT INTO Notifications (Notification_Content, Notification_Date) VALUES (?, NOW())");
        $stmt_notification->bind_param("s", $notification_content);
        $stmt_notification->execute();

        // 2. Get the ID of the notification we just created
        $notification_id = $conn->insert_id;

        // 3. Get all user IDs to send the notification to
        $users_result = $conn->query("SELECT User_ID FROM Users");
        $user_ids = $users_result->fetch_all(MYSQLI_ASSOC);
        
        // 4. Prepare and execute the link for each user
        $stmt_user_notification = $conn->prepare("INSERT INTO Notification_User (Notification_ID, User_ID) VALUES (?, ?)");
        foreach ($user_ids as $user) {
            $stmt_user_notification->bind_param("ii", $notification_id, $user['User_ID']);
            $stmt_user_notification->execute();
        }

        // If everything worked, commit the changes
        $conn->commit();
        $message = "Announcement sent successfully to all users!";
        $message_type = "success";

    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        $message = "Error: Could not send announcement.";
    }
}

$priorities = $conn->query("SELECT * FROM Priority ORDER BY Priority_ID ASC")->fetch_all(MYSQLI_ASSOC);
?>

<h1 class="page-title">Global Announcements</h1>

<div class="management-section">
    <?php if ($message): ?>
        <p class="message-banner <?php echo $message_type; ?>"><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST" action="admin_announcements.php">
        <input type="hidden" name="action" value="send_global_announcement">
        <div class="form-group">
            <label for="announcement_title">Title:</label>
            <input type="text" id="announcement_title" name="announcement_title" class="form-input" required>
        </div>
        <div class="form-group">
            <label for="announcement_description">Description:</label>
            <textarea id="announcement_description" name="announcement_description" class="form-input" rows="5" required></textarea>
        </div>
        <div class="form-group">
            <label for="announcement_priority">Priority:</label>
            <select id="announcement_priority" name="announcement_priority" class="form-input" required>
                 <?php foreach ($priorities as $priority): ?>
                    <option value="<?php echo $priority['Priority_ID']; ?>"><?php echo htmlspecialchars($priority['Priority_Type']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="action-button">Send to All Users</button>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>