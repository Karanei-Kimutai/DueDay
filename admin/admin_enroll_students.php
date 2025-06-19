<?php
// FIX: Define the specific roles that are allowed to access THIS page.
$allowed_roles = ['Admin', 'Module Leader'];

// FIX: Require the LOCAL admin header. It now uses the $allowed_roles array for its security check.
require_once 'templates/header.php';

// The old, redundant security check is removed as the header now handles it.

$message = '';
$message_type = 'error';
$selected_class_id = $_GET['class_id'] ?? null;

// --- POST REQUEST HANDLING ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_enrollment'])) {
    if (!empty($selected_class_id)) {
        $enrolled_user_ids = $_POST['user_ids'] ?? [];

        $conn->begin_transaction();
        try {
            // 1. Delete all existing enrollments for this class.
            $stmt_delete = $conn->prepare("DELETE FROM User_Classes WHERE Class_ID = ?");
            $stmt_delete->bind_param("i", $selected_class_id);
            $stmt_delete->execute();
            $stmt_delete->close();

            // 2. Insert the new enrollments for all checked users.
            if (!empty($enrolled_user_ids)) {
                $stmt_insert = $conn->prepare("INSERT INTO User_Classes (User_ID, Class_ID) VALUES (?, ?)");
                foreach ($enrolled_user_ids as $user_id) {
                    $stmt_insert->bind_param("ii", $user_id, $selected_class_id);
                    $stmt_insert->execute();
                }
                $stmt_insert->close();
            }

            $conn->commit();
            $message = "Enrollment updated successfully!";
            $message_type = "success";

        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            $message = "Error updating enrollment: " . $exception->getMessage();
        }
    }
}

// --- DATA FETCHING ---
// Get all classes for the dropdown selection.
$all_classes = $conn->query("SELECT * FROM Classes ORDER BY Class_Name ASC")->fetch_all(MYSQLI_ASSOC);

$all_users = [];
$enrolled_user_ids = [];

if ($selected_class_id) {
    // FIX: SQL query now fetches ALL users (not just students) and their roles.
    $all_users = $conn->query("SELECT u.User_ID, u.F_Name, u.L_Name, u.Email, r.Role_Name 
                               FROM Users u 
                               JOIN Role r ON u.Role_ID = r.Role_ID 
                               ORDER BY u.L_Name, u.F_Name")->fetch_all(MYSQLI_ASSOC);
    
    // Get IDs of users already enrolled in this class.
    $stmt_enrolled = $conn->prepare("SELECT User_ID FROM User_Classes WHERE Class_ID = ?");
    $stmt_enrolled->bind_param("i", $selected_class_id);
    $stmt_enrolled->execute();
    $result = $stmt_enrolled->get_result();
    while ($row = $result->fetch_assoc()) {
        $enrolled_user_ids[] = $row['User_ID'];
    }
    $stmt_enrolled->close();
}
?>

<h1 class="page-title">Manage User Enrollment</h1>

<div class="management-section card">
    <h2>Step 1: Select a Class</h2>
    <form method="GET" action="admin_enroll_students.php">
        <div class="form-group">
            <label for="class_id">Choose a class to manage enrollment:</label>
            <select name="class_id" id="class_id" class="form-input" onchange="this.form.submit()">
                <option value="">-- Select a Class --</option>
                <?php foreach ($all_classes as $class): ?>
                    <option value="<?php echo $class['Class_ID']; ?>" <?php if ($selected_class_id == $class['Class_ID']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($class['Class_Name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
</div>

<?php if ($selected_class_id): ?>
<div class="management-section card" style="margin-top: 2rem;">
    <h2>Step 2: Enroll Users</h2>
    <p>Check the box next to each user you want to enroll in this class.</p>

    <?php if ($message): ?>
        <p class="message-banner <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST" action="admin_enroll_students.php?class_id=<?php echo $selected_class_id; ?>">
        <input type="hidden" name="update_enrollment" value="1">
        
        <div class="checkbox-item select-all-container">
            <input type="checkbox" id="selectAllUsers">
            <label for="selectAllUsers"><strong>Select All / Deselect All</strong></label>
        </div>

        <div class="user-enrollment-list">
            <?php if (empty($all_users)): ?>
                <p>No users found in the system.</p>
            <?php else: ?>
                <?php foreach ($all_users as $user): ?>
                    <div class="checkbox-item">
                        <input type="checkbox" name="user_ids[]" id="user_<?php echo $user['User_ID']; ?>" value="<?php echo $user['User_ID']; ?>" class="user-checkbox"
                               <?php if (in_array($user['User_ID'], $enrolled_user_ids)) echo 'checked'; ?>>
                        <label for="user_<?php echo $user['User_ID']; ?>">
                            <?php echo htmlspecialchars($user['L_Name'] . ', ' . $user['F_Name']); ?>
                            <small>(Role: <?php echo htmlspecialchars($user['Role_Name']); ?>)</small>
                        </label>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="form-actions" style="margin-top: 1.5rem;">
            <button type="submit" class="action-button">Update Enrollment</button>
        </div>
    </form>
</div>
<?php endif; ?>

<?php 
// FIX: Require the LOCAL admin footer.
require_once 'templates/footer.php'; 
?>