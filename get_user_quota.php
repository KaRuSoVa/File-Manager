<?php
include 'database.php';

$username = $_GET['username'];

$sql = "SELECT quota FROM users WHERE username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    echo json_encode($data);
} else {
    echo json_encode(array('error' => 'No user found with the provided username'));
}
?>
