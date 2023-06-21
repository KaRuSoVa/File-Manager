<?php
// Veritabanı bağlantısı
include "database.php";

// Dosya bilgilerini alma
$filePath = $_POST['filePath'];
$fileType = $_POST['fileType'];
$fileSize = $_POST['fileSize'];
$documentId = $_POST['documentId'];

// Veritabanına dosya bilgilerini ekleme
$stmt = $conn->prepare("INSERT INTO files (file_path, file_type, file_size, document_id) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", $filePath, $fileType, $fileSize, $documentId);

if ($stmt->execute()) {
  // Dosya bilgileri başarıyla veritabanına eklendi
  echo "Dosya bilgileri başarıyla kaydedildi.";
} else {
  // Dosya bilgileri veritabanına eklenirken bir hata oluştu
  echo "Dosya bilgileri kaydedilirken bir hata oluştu: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>