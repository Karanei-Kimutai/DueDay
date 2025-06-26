<?php
require_once 'templates/header.php';

// Handle POST actions to toggle user status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_user_status') {
    $user_id_to_toggle = $_POST['user_id'];
    $current_status = $_POST['current_status'];
    // Admin can't deactivate self
    if ($user_id_to_toggle != $_SESSION['user_id']) {
        $new_status = ($current_status === 'active') ? 'inactive' : 'active';
        $stmt = $conn->prepare("UPDATE Users SET status = ? WHERE User_ID = ?");
        $stmt->bind_param("si", $new_status, $user_id_to_toggle);
        $stmt->execute();
        $stmt->close();
    }
    // Redirect to clear the POST request, preserving the search query if it exists
    $search_query = !empty($_GET['search']) ? '?search=' . urlencode($_GET['search']) : '';
    header("Location: admin_users.php" . $search_query);
    exit();
}

// --- UPDATED: Fetch users with search functionality ---
$search_term = $_GET['search'] ?? '';

// Base SQL query
$sql = "SELECT u.User_ID, u.F_Name, u.L_Name, u.Email, u.status, r.Role_Name 
        FROM Users u 
        JOIN Role r ON u.Role_ID = r.Role_ID";

// Append search condition if a search term is provided
if (!empty($search_term)) {
    $sql .= " WHERE u.F_Name LIKE ? OR u.L_Name LIKE ? OR u.Email LIKE ?";
}
$sql .= " ORDER BY u.User_ID ASC";

$stmt = $conn->prepare($sql);

// Bind parameters if searching
if (!empty($search_term)) {
    $like_term = "%" . $search_term . "%";
    $stmt->bind_param("sss", $like_term, $like_term, $like_term);
}

$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<h1 class="page-title">User Management</h1>

<div class="widget">
    <form method="GET" action="admin_users.php" class="widget-form">
        <input type="search" name="search" class="form-input" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($search_term); ?>">
        <button type="submit" class="action-button">Search</button>
        <?php if (!empty($search_term)): ?>
            <a href="admin_users.php" class="action-button secondary" style="text-decoration:none;">Clear</a>
        <?php endif; ?>
    </form>
</div>


<div class="management-section">
    <table>
        <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr><td colspan="6">No users found<?php if(!empty($search_term)) echo ' matching your search'; ?>.</td></tr>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['User_ID']; ?></td>
                    <td><?php echo htmlspecialchars($user['F_Name'] . ' ' . $user['L_Name']); ?></td>
                    <td><?php echo htmlspecialchars($user['Email']); ?></td>
                    <td><?php echo htmlspecialchars($user['Role_Name']); ?></td>
                    <td><span class="status-<?php echo $user['status']; ?>" style="font-weight: bold; text-transform: capitalize; color: <?php echo ($user['status'] === 'active') ? 'var(--green-accent)' : 'var(--red-accent)'; ?>;"><?php echo htmlspecialchars(ucfirst($user['status'])); ?></span></td>
                    <td class="action-cell">
                        <a href="edit_user.php?id=<?php echo $user['User_ID']; ?>" class="table-action-btn edit">Edit</a>
                        <?php if ($user['User_ID'] != $_SESSION['user_id']): ?>
                        <form method="POST" action="admin_users.php?search=<?php echo urlencode($search_term); ?>" data-confirm="Are you sure you want to change this user's status?">
                            <input type="hidden" name="action" value="toggle_user_status">
                            <input type="hidden" name="user_id" value="<?php echo $user['User_ID']; ?>">
                            <input type="hidden" name="current_status" value="<?php echo $user['status']; ?>">
                            <button type="submit" class="table-action-btn <?php echo ($user['status'] === 'active') ? 'delete' : 'activate'; ?>">
                                <?php echo ($user['status'] === 'active') ? 'Deactivate' : 'Activate'; ?>
                            </button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'templates/footer.php'; ?>