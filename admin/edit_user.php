<?php
require_once 'templates/header.php'; // The admin header

$user_id_to_edit = $_GET['id'] ?? null;
if (!$user_id_to_edit) { header("Location: admin_users.php"); exit(); }

$role_message = '';
$password_message = '';
$error_message = '';

// --- POST HANDLING FOR MULTIPLE ACTIONS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // --- ACTION 1: UPDATE USER ROLE ---
    if ($action === 'update_role') {
        // You can't change your own role to prevent self-lockout
        if ($user_id_to_edit != $_SESSION['user_id']) {
            $stmt = $conn->prepare("UPDATE Users SET Role_ID = ? WHERE User_ID = ?");
            $stmt->bind_param("ii", $_POST['new_role_id'], $user_id_to_edit);
            if ($stmt->execute()) {
                $role_message = "User role updated successfully!";
            } else {
                $error_message = "Error: Could not update role.";
            }
            $stmt->close();
        } else {
            $error_message = "You cannot change your own role.";
        }
    }

    // --- ACTION 2: UPDATE USER PASSWORD ---
    if ($action === 'update_password') {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (!empty($new_password)) {
            if ($new_password === $confirm_password) {
                 if (strlen($new_password) >= 8) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE Users SET Password = ? WHERE User_ID = ?");
                    $stmt->bind_param("si", $hashed_password, $user_id_to_edit);
                    if ($stmt->execute()) {
                        $password_message = "User password updated successfully!";
                    } else {
                        $error_message = "Error updating password.";
                    }
                    $stmt->close();
                } else {
                    $error_message = "New password must be at least 8 characters long.";
                }
            } else {
                $error_message = "Passwords do not match.";
            }
        } else {
            $error_message = "Password cannot be empty.";
        }
    }
}

// --- DATA FETCHING FOR DISPLAY ---
$stmt_user = $conn->prepare("SELECT User_ID, F_Name, L_Name, Email, Role_ID FROM Users WHERE User_ID = ?");
$stmt_user->bind_param("i", $user_id_to_edit);
$stmt_user->execute();
$user_to_edit = $stmt_user->get_result()->fetch_assoc();
$stmt_user->close();

$roles = $conn->query("SELECT * FROM Role")->fetch_all(MYSQLI_ASSOC);

if (!$user_to_edit) { header("Location: admin_users.php"); exit(); }
?>

<h1 class="page-title">Edit User: <?php echo htmlspecialchars($user_to_edit['F_Name'] . ' ' . $user_to_edit['L_Name']); ?></h1>

<div class="management-section">
    <h2>User Role</h2>
    <?php if ($role_message): ?><p class="message-banner success"><?php echo $role_message; ?></p><?php endif; ?>
    
    <form method="POST" action="edit_user.php?id=<?php echo $user_to_edit['User_ID']; ?>">
        <input type="hidden" name="action" value="update_role">
        <div class="form-group">
            <label>User Email:</label>
            <p><?php echo htmlspecialchars($user_to_edit['Email']); ?></p>
        </div>
        <div class="form-group">
            <label for="new_role_id">Change Role To:</label>
            <select name="new_role_id" id="new_role_id" class="form-input">
                <?php foreach ($roles as $role): ?>
                    <option value="<?php echo $role['Role_ID']; ?>" <?php if ($role['Role_ID'] == $user_to_edit['Role_ID']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($role['Role_Name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="action-button">Update Role</button>
    </form>
</div>

<div class="management-section">
    <h2>Update Password</h2>
    <p style="color: #6b7280; margin-bottom: 20px;">Force-reset the user's password. The user will not be notified.</p>
    <?php if ($password_message): ?><p class="message-banner success"><?php echo $password_message; ?></p><?php endif; ?>
    <?php if ($error_message): ?><p class="message-banner error"><?php echo $error_message; ?></p><?php endif; ?>

    <form method="POST" action="edit_user.php?id=<?php echo $user_to_edit['User_ID']; ?>">
        <input type="hidden" name="action" value="update_password">
        <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" name="new_password" id="new_password" class="form-input" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-input" required>
        </div>
        <button type="submit" class="action-button">Update Password</button>
    </form>
</div>

<a href="admin_users.php" class="action-button secondary" style="text-decoration:none; display:inline-block; margin-top: 20px;">Back to User List</a>

<style>
    /* These styles are just for the messages on this page */
    .message-banner { padding: 10px 15px; margin: 15px 0; border-radius: 5px; font-weight: 500; }
    .message-banner.success { background-color: #d1fae5; color: #065f46; }
    .message-banner.error { background-color: #fee2e2; color: #991b1b; }
</style>

<?php require_once 'templates/footer.php'; ?>