<?php

include 'database.php';



header('Content-Type: application/json');



if (isset($_POST['username']) && isset($_POST['quota'])) {

    $username = $_POST['username'];

    $quota = $_POST['quota'];

    $sql = "UPDATE users SET quota = ? WHERE username = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("is", );

    $stmt->execute();



    if ($stmt->affected_rows > 0) {

        echo json_encode(['success' => true]);

    } else {

        echo json_encode(['error' => 'Could not update quota.']);

    }

} else {

    echo json_encode(['error' => 'No username or quota provided.']);

}

?>

