<?php
include 'database.php';

$source_user_id = $_SESSION['id'];

// This SQL query joins the 'folder_shares' table with the 'users' table to get the username of the user to whom the folder was shared.
$query = "
SELECT fs.id, fs.folder, u.username 
FROM folder_shares fs
INNER JOIN users u ON fs.destination_user_id = u.id 
WHERE fs.source_user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $source_user_id);
$stmt->execute();

$result = $stmt->get_result();
$shares = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($shares);
?>
