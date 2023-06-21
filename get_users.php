<?php
include 'database.php';

// Get current user id from session
$current_user_id = $_SESSION['id'];

// Prepare SQL
$sql = "SELECT id, username FROM users WHERE id != '$current_user_id'";

// Execute query and get result
$result = $conn->query($sql);

// Fetch associative array and encode into JSON
$users = array();
while($row = $result->fetch_assoc()) {
    $users[] = $row;
}
echo json_encode($users);

// Close connection
$conn->close();
?>
