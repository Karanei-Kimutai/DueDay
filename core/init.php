<?php
// CORE INITIALIZATION SCRIPT
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --- 1. DATABASE CONNECTION ---
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "DueDay";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- 2. SESSION MANAGEMENT ---
session_start();

// --- 3. GLOBAL HELPER FUNCTIONS ---

/**
 * Awards an achievement to a user if they don't already have it.
 * @param mysqli $db_connection The database connection object.
 * @param int $user_id The user to award the achievement to.
 * @param int $achievement_id The ID of the achievement.
 */
function award_achievement(mysqli $db_connection, int $user_id, int $achievement_id) {
    $check_stmt = $db_connection->prepare("SELECT User_ID FROM User_Achievements WHERE User_ID = ? AND Achievement_ID = ?");
    $check_stmt->bind_param("ii", $user_id, $achievement_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $check_stmt->close();

    if ($result->num_rows == 0) {
        $insert_stmt = $db_connection->prepare("INSERT INTO User_Achievements (User_ID, Achievement_ID) VALUES (?, ?)");
        $insert_stmt->bind_param("ii", $user_id, $achievement_id);
        $insert_stmt->execute();
        $insert_stmt->close();
    }
}

// You can add other global functions like create_notification() here as well.
?>