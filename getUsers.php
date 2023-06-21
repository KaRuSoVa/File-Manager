<?php
include 'database.php';

$sql = "SELECT username FROM users";
$stmt = $conn->prepare($sql);
$stmt->execute();

$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($data);
?>
