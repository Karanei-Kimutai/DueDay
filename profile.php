<?php
require_once 'templates/header.php'; // Use the new header

$message = '';
$message_type = 'error';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch user's current hashed password
    $stmt = $conn->prepare("SELECT Password FROM Users WHERE User_ID = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Verify current password
    if ($result && password_verify($current_password, $result['Password'])) {
        // Check if new passwords match
        if ($new_password === $confirm_password) {
            if (strlen($new_password) >= 8) {
                // Hash the new password and update the database
                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE Users SET Password = ? WHERE User_ID = ?");
                $update_stmt->bind_param("si", $hashed_new_password, $user_id);
                if ($update_stmt->execute()) {
                    $message = "Password updated successfully!";
                    $message_type = "success";
                } else {
                    $message = "An error occurred while updating the password.";
                }
                $update_stmt->close();
            } else {
                $message = "New password must be at least 8 characters long.";
            }
        } else {
            $message = "New passwords do not match.";
        }
    } else {
        $message = "Incorrect current password.";
    }
}
$conn->close();
?>

<div class="form-container">
    <h2 class="section-title">My Profile</h2>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($user_fname); ?></p>
    <p><strong>Role:</strong> <?php echo htmlspecialchars($user_role); ?></p>
    <hr style="margin: 20px 0;">

    <h3 class="section-title" style="font-size: 1.5rem;">Change Password</h3>
    
    <?php if ($message): ?>
        <p class="message-banner <?php echo $message_type; ?>"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" action="profile.php">
        <div class="form-group">
            <label for="current_password">Current Password:</label>
            <input type="password" id="current_password" name="current_password" class="form-input" required>
        </div>
        <div class="form-group">
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" class="form-input" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-input" required>
        </div>
        <button type="submit" class="btn btn--primary">Update Password</button>
    </form>
    
    <div style="margin-top: 30px;">
         <a href="/dueday/auth/logout.php" class="btn btn--danger">Logout</a>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>