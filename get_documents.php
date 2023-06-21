<?php

session_start();

include "database.php";



// Arama parametresini al

$searchValue = $_POST["search"];



// user_id ile eşleşen klasörleri getir

$kullanici_id = $_SESSION['id'];

$dir = 'upload_directory/' . $kullanici_id;



// Klasör varsa içeriklerini getir

if (is_dir($dir)) {

    $files = scandir($dir);



    foreach ($files as $file) {

        // Özel dizinleri ve .tmb dosyalarını atla

        if ($file == "." || $file == ".." || $file == ".tmb") {

            continue;

        }



        $filePath = $dir . '/' . $file;

        $trimmedPath = ltrim(str_replace($dir, '', $filePath), DIRECTORY_SEPARATOR);

        $folderName = basename($file);

        

        // Eğer dosya bir klasör ise ve arama değeri dosya yolunda veya isminde bulunuyorsa HTML oluştur

        if (is_dir($filePath) && (strpos($trimmedPath, $searchValue) !== false || strpos($folderName, $searchValue) !== false)) {

            $icon = '<i class="fa-solid fa-folder-tree"></i>';



            echo '<form method="POST" action="file.php" style="display: inline;">';

            echo '<input type="hidden" name="folderPath" value="' . $trimmedPath . '" />';

            echo '<li style="display: inline-block; margin-right: 10px; color:white;">' . $icon . ' <button type="submit" class="link-light rounded" style="display:inline-block; border:none; background:none; color:white;"><span class="truncate">' . $trimmedPath . '</span></button></li>';

            echo '</form><br />';

        }

    }

} else {

    echo "Directory not found.";

}

?>









<!-- JavaScript kodu -->



<script>



function submitForm(folderPath) {



  var form = document.createElement('form');



  form.method = 'POST';



  form.action = 'file.php'; 







  var input = document.createElement('input');



  input.type = 'hidden';



  input.name = 'folderPath';



  input.value = folderPath;







  form.appendChild(input);



  document.body.appendChild(form);







  form.submit();



}



</script>