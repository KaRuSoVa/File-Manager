<?php
session_start();
include 'database.php';

$id = $_POST['id'];

// Get the shared folder details
$query = "SELECT folder, destination_user_id FROM folder_shares WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$share = $result->fetch_assoc();

if ($share) {
    // Delete the share from the database
    $query = "DELETE FROM folder_shares WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    // Check if share was successfully deleted
    if ($stmt->affected_rows > 0) {
        // Share deleted successfully, now delete the folder from file system
        $folderPath = 'upload_directory/' . $share['destination_user_id'] . '/' . $share['folder'];
        if (is_dir($folderPath)) {
            // Recursively delete a directory and its contents
            function recursiveDelete($dir) {
                foreach(scandir($dir) as $file) {
                    if ('.' === $file || '..' === $file) continue;
                    if (is_dir("$dir/$file")) recursiveDelete("$dir/$file");
                    else unlink("$dir/$file");
                }
                rmdir($dir);
            }

            recursiveDelete($folderPath);
            echo json_encode(['status' => 'success', 'message' => 'Share and folder deleted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Folder does not exist.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete share.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Share not found.']);
}
?>
