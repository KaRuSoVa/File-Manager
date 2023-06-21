<?php
session_start();
include "database.php";

$user_id = $_SESSION['id']; // Make sure 'user_id' is the correct key

// Get the data from the request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['folderName'])) {
        $folderPath=$_POST['folderPath'];
        $folderName = $_POST['folderName'];
        $dir = 'upload_directory/' . $user_id;
        
        error_log("Directory: " . $dir . "\n", 3, "log.txt");

        // Check if directory exists, if not create it
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            error_log("Main directory does not exist, creating it.\n", 3, "log.txt");
        }

        // Full path of the new folder
        $fullFolderPath = $dir . '/' . $folderPath.'/'.  $folderName;

        // Create the new folder
        if (!is_dir($fullFolderPath)) {
            if (mkdir($fullFolderPath, 0777, true)) { // Recursive parameter set to true
                echo "New folder created successfully";
                error_log("New folder created successfully at: " . $fullFolderPath . "\n", 3, "log.txt");
            } else {
                echo "Error: Unable to create folder";
                error_log("Error: Unable to create folder at: " . $fullFolderPath . "\n", 3, "log.txt");
            }
        } else {
            echo "Error: Folder already exists";
            error_log("Error: Folder already exists at: " . $fullFolderPath . "\n", 3, "log.txt");
        }
    } else {
        echo "Folder name is required";
        error_log("Error: Folder name is required\n", 3, "log.txt");
    }
}
?>
