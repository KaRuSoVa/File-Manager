<?php
// veritabanı ayarları
if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }
$sname = "localhost"; // sunucu
$unmae = "u9979192_fm"; // kullanıcı adı
$password = "Karusova691*"; // şifre

$db_name = "u9979192_fm"; // veritabanı adı 

$conn = mysqli_connect($sname, $unmae, $password, $db_name);

if (!$conn) {
    echo "connection error";
}
$conn->set_charset("utf8");

