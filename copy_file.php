<?php
include 'database.php';

$user_id = $_SESSION['id'];
$folder = $_POST['folder'];
$userId = $_POST['userId'];
$action = isset($_POST['action']) ? $_POST['action'] : null;

$source = 'upload_directory/' . $user_id . '/' . $folder;
$dest = 'upload_directory/' . $userId . '/' . $folder;
$destFolderName = $folder; // Add this line

$response = [];

function recurse_copy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ( $file = readdir($dir))) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                recurse_copy($src . '/' . $file, $dst . '/' . $file);
            }
            else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

if (!is_dir($source)) {
    $response['status'] = 'error';
    $response['message'] = 'Source directory does not exist.';
    echo json_encode($response);
    exit();
}

if (is_dir($dest)) {
    if ($action === null) {
        $response['status'] = 'conflict';
        $response['message'] = 'Destination directory already exists.';
        echo json_encode($response);
        exit();
    }

    switch ($action) {
        case 'overwrite':
            recurse_copy($source, $dest);
            break;

        case 'rename':
            $i = 1;
            while (is_dir($dest . "($i)")) {
                $i++;
            }
            $dest .= "($i)";
            $destFolderName .= "($i)"; // Update folder name with suffix
            recurse_copy($source, $dest);
            break;

        case 'cancel':
        default:
            $response['status'] = 'cancelled';
            $response['message'] = 'Directory copy cancelled.';
            echo json_encode($response);
            exit();
    }
}
else {
    recurse_copy($source, $dest);
}

try {
    $stmt = $conn->prepare("INSERT INTO folder_shares (folder, source_user_id, destination_user_id, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sii", $destFolderName, $user_id, $userId);
    $stmt->execute();

    $response['status'] = 'success';
    $response['message'] = 'Directory copied successfully.';
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = 'Failed to copy directory: ' . $e->getMessage();
}

echo json_encode($response);
?>
