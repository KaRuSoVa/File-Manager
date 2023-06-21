<?php
include "database.php";

if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    // Dosya bilgilerini al
    $fileTmpPath = $_FILES['file']['tmp_name'];
    $originalFileName = $_FILES['file']['name'];
    $fileSize = $_FILES['file']['size'];
    $fileType = $_FILES['file']['type'];
    $fileNameCmps = explode(".", $originalFileName);
    $fileExtension = strtolower(end($fileNameCmps));

    // Hedef dosya yolu
    $folderPath = $_POST['folderPath'];
    var_dump($folderPath); // Check folderPath value    
    $newFileName = md5(time() . $originalFileName) . '.' . $fileExtension;
    $uploadFileDir = './upload_directory/' . $folderPath . '/';
    $dest_path = $uploadFileDir . $newFileName;

    // Hedef klasör yoksa oluştur
    if (!file_exists($uploadFileDir)) {
        mkdir($uploadFileDir, 0777, true);
    }

    // Dosyayı hedef dizine taşı
    if(move_uploaded_file($fileTmpPath, $dest_path)) {
        $document_id = $_POST['document_id'];
        $file_path = $folderPath . '/' . $newFileName;

        // Veritabanına kayıt ekle
        $stmt = $conn->prepare("INSERT INTO files (file_name, file_path, file_type, file_size, document_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssii", $originalFileName, $file_path, $fileType, $fileSize, $document_id);

        if ($stmt->execute()) {
            echo "Dosya başarıyla yüklendi ve veritabanına kaydedildi.";
        } else {
            echo "Veritabanına kayıt eklenirken bir hata oluştu: " . $stmt->error;
        }
    } else {
        echo "Dosya yükleme hatası.";
    }
} else {
    echo "Dosya yükleme hatası.";
}
?>
