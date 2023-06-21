<?php 
session_start();

include "database.php";

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_object();

if ($user && password_verify($password, $user->password)) {
    $_SESSION['user_id'] = $user->id;
    $_SESSION['user_name'] = $user->username;
    $_SESSION['user_email'] = $user->email;
    $_SESSION['user_role'] = $user->role;
    echo 'success';
} else {
    echo 'fail';
}

$conn->close();
?>
