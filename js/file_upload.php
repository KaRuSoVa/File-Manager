<?php
// Önce hata raporlama ayarlarını yapıyoruz
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Bu klasör yolu, dosyaların yükleneceği yerdir.
$target_dir = "uploads/";

// Dosyanın hedef yolunu oluşturma
$target_file = $target_dir . basename($_FILES["file"]["name"]);

// Dosyayı hedef konuma taşıma
if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
    echo "The file ". htmlspecialchars(basename($_FILES["file"]["name"])). " has been uploaded.";
} else {
    echo "Sorry, there was an error uploading your file.";
}
?>