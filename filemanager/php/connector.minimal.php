<?php
session_start(); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "database.php";

$kullanici_id = '';
if (isset($_SESSION["id"])) {
    $kullanici_id = $_SESSION["id"];
}

$query = "SELECT role FROM users WHERE id = $kullanici_id";
$result = mysqli_query($conn, $query);
$rol = mysqli_fetch_assoc($result)['role'];

$query = "SELECT username FROM users WHERE id = $kullanici_id";
$result = mysqli_query($conn, $query);
$username = mysqli_fetch_assoc($result)['username'];

function getDirectorySize($dir) {
    $size = 0;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file) {
        $size += $file->getSize();
    }
    return $size;
}

function getUserQuota($user_id, $conn) {
    $query = $conn->prepare("SELECT quota FROM users WHERE id = ?");
    $query->bind_param("i", $user_id);
    $query->execute();
    $result = $query->get_result();
    $row = $result->fetch_assoc();
    return $row['quota'] * 1024 * 1024; 
}

function isDiskFull($kullanici_id, $conn, $dosya_boyutu = 0) {
    // Kullanıcının rolünü sorgulayalım:
    $query = "SELECT role FROM users WHERE id = $kullanici_id";
    $result = mysqli_query($conn, $query);
    $rol = mysqli_fetch_assoc($result)['role'];
    
    // Kullanıcının rolüne göre klasör yolunu belirleyelim:
    if ($rol == 'admin') {
        $user_folder = '../../upload_directory/';
    } else {
        $user_folder = '../../upload_directory/'.$kullanici_id;
    }

    $user_quota = getUserQuota($kullanici_id, $conn);
    $folder_size = getDirectorySize($user_folder) + $dosya_boyutu;
    return $folder_size >= $user_quota;
}



function preCheckDiskFull($cmd, $args, $elfinder, $volume) {
    global $kullanici_id, $conn;
    $GLOBALS['prevFull'] = isDiskFull($kullanici_id, $conn);
}

function checkDiskFull($cmd, $result, $args, $elfinder, $volume) {
    global $kullanici_id, $conn;
    $dosya_boyutu = isset($args['added'][0]['size']) ? $args['added'][0]['size'] : 0;
    $isFull = isDiskFull($kullanici_id, $conn, $dosya_boyutu);
    if ($isFull) {
        return true;
    }
}
function checkUploadSize($cmd, $args, $elfinder, $volume) {
    error_log('checkUploadSize is called.');
        global $kullanici_id, $conn;
    $dosya_boyutu = $args['FILES']['upload']['size'][0];
    $isFull = isDiskFull($kullanici_id, $conn, $dosya_boyutu);
    if ($isFull) {
        return array('preventexec' => true, 'results' => array('error' => 'Disk kotası aşıldı.'));
        return true;
    }
}


function checkUploadQuotaExceed($cmd, $result, $args, $elfinder, $volume) {
    global $kullanici_id, $conn;
    $quotaExceed = isExceedQuota($args['size'], $kullanici_id, $conn);
    if ($quotaExceed) {
        // Yükleme geri alınıyor
        $result['removed'] = true;
        $result['error'] = 'Disk kotası aşıldı.';
    }
    return $result;
}
function testFunction($cmd, $result, $args, $elfinder, $volume) {
    $message = 'Upload event has been triggered.';
    $writeResult = error_log($message, 3, 'log1.txt');
    if ($writeResult === false) {
        die('Error writing to log file');
    }
    return $result;
}
function myPreCheck($cmd, &$args, $elfinder, $volume)
{
    if ($cmd === 'upload') {
        $dosya_boyutu = $args['FILES']['upload']['size'][0];
        $isFull = isDiskFull($dosya_boyutu);
        if ($isFull) {
            // prevent uploading...
            $args['FILES'] = array();
        }
    }
}



if (!file_exists('../../upload_directory/'.$kullanici_id)) {
    mkdir('../../upload_directory/'.$kullanici_id, 0777, true);
}

 
if ($rol == 'admin') {
    $upload_path = '../../upload_directory/';
} else {
    $upload_path = '../../upload_directory/'.$kullanici_id;
}

is_readable('../../vendor/autoload.php') && require '../../vendor/autoload.php';

    require 'autoload.php';

elFinder::$netDrivers['ftp'] = 'FTP';

$uploadAllow = array(
    'image/x-ms-bmp', 
    'image/gif', 
    'image/jpeg', 
    'image/png', 
    'image/x-icon', 
    'text/plain', 
    'application/pdf',
    'application/msword',
    'application/vnd.ms-excel',
    'application/vnd.ms-powerpoint',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'audio/mpeg', 
    'audio/wav',
    'audio/ogg',
    'video/mpeg', 
    'video/mp4', 
    'video/x-msvideo', 
    'video/quicktime',
    'text/csv',
    'text/html',
    'application/json',
    'application/zip',
    'application/x-rar-compressed'
);
$uploadDeny = array('');

$disableds = isDiskFull($kullanici_id, $conn) ? array('upload', 'paste', 'duplicate', 'extract', 'mkfile', 'mkdir') : array();

$opts = array(
    'roots' => array(
        'debug' => true,

        array(
            'driver'        => 'LocalFileSystem', 
            'path'          =>  $upload_path,  
            'uploadMaxSize' => '64M', // maksimum 50 MB
            'URL' =>        dirname(dirname($_SERVER['PHP_SELF'])) . $upload_path,
            'alias'         => $username,
            'trashHash'     => 't1_Lw', 
            'winHashFix'    => DIRECTORY_SEPARATOR !== '/',
            'uploadDeny'    => $uploadDeny, 
            'uploadAllow'   => $uploadAllow, 
            'uploadOrder'   => array('deny', 'allow'),  
            'accessControl' => 'access', 
            'disabled' => $disableds,
            'bind' => array(
                'upload' => array('uploadTest', 'testFunction'),
                'upload.presave' => array('myPreCheck'),
                'rm.pre' => array('preCheckDiskFull'),
                'upload paste duplicate extract rm' => array('checkDiskFull')
            ),
            'attributes' => array(
                array(
                    'pattern' => '~/\.tmb(/.*)?$~', // .tmb klasörü ve altındaki tüm dosyalar için
                    'read' => false,
                    'write' => false,
                    'hidden' => true,
                    'locked' => true
                )
            ),
            'plugin' => array(
                'Watermark' => array(
                    'enable' => true,
                    'source' => 'logo.png',
                    'marginRight' => 5,
                    'marginBottom' => 5,
                    'quality' => 90,
                    'transparency' => 70,
                    'targetType' => IMG_GIF | IMG_JPG | IMG_PNG | IMG_WBMP,
                    'targetMinPixel' => 200
                )
            )
        ),
        array(
            'id'            => '1',
            'driver'        => 'Trash',
            'path'          => '../files/.trash/',
            'tmbURL'        => dirname($_SERVER['PHP_SELF']) . '/../files/.trash/.tmb/',
            'winHashFix'    => DIRECTORY_SEPARATOR !== '/', 
            'uploadDeny'    => array('all'), 
            'uploadAllow'   => array('image/x-ms-bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/x-icon', 'text/plain'),
            'uploadOrder'   => array('deny', 'allow'),  
            'accessControl' => 'access', 
            'attributes' => array(
                array(
                    'pattern' => '~/\.tmb(/.*)?$~', // .tmb klasörü ve altındaki tüm dosyalar için
                    'read' => false,
                    'write' => false,
                    'hidden' => true,
                    'locked' => false
                )
            ),
        ),
    ),
    'disabled' => $disableds
);

$connector = new elFinderConnector(new elFinder($opts));
$connector->run();

?>
