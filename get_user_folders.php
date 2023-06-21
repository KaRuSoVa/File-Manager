<?php

session_start();



// Oturum değişkenlerini kontrol et ve al

if (isset($_SESSION['id'])) {

    $user_id = $_SESSION['id'];

} else {

    echo "User session not found.";

    exit;

}



// Dosya dizinini belirleyin

$dir = 'upload_directory/'.$user_id;



// Dizini tarayın ve dosya/dizin adlarını bir diziye ekleyin

$folders = [];

$directory = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);

$iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::SELF_FIRST);



foreach ($iterator as $info) {

    if ($info->isDir()) {

        $filePath = $info->getPathname();

        $trimmedPath = ltrim(str_replace($dir, '', $filePath), DIRECTORY_SEPARATOR);

        // .tms ile biten dosyaları dışarıda tutun

        if (!endsWith($trimmedPath, '.tmb')) {

            $folders[] = $trimmedPath;

        }

    }

}



// Dosya adının belirli bir ek ile bitip bitmediğini kontrol eden fonksiyon

function endsWith($haystack, $needle) {

    $length = strlen($needle);

    if ($length == 0) {

        return true;

    }



    return (substr($haystack, -$length) === $needle);

}



echo json_encode($folders);

?>

