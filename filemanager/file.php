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


function getDirectorySize1($dir) {
    $size = 0;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file) {
        $size += $file->getSize();
    }
    return $size;
}

function getUserQuota1($user_id, $conn) {
    $query = $conn->prepare("SELECT quota FROM users WHERE id = ?");
    $query->bind_param("i", $user_id);
    $query->execute();
    $result = $query->get_result();
    $row = $result->fetch_assoc();
    return $row['quota'] * 1024 * 1024; 
}

function checkUserQuota1($user_id, $conn) {
    $user_folder = '../upload_directory/'.$user_id;
    $user_quota = getUserQuota1($user_id, $conn);
    $folder_size = getDirectorySize1($user_folder);
    return $folder_size > $user_quota;
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2">
		<title>elFinder 2.1.x source version with PHP connector</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

		<script src="../js/bootstrap.min.js"></script>
		<script src="../js/popper.js"></script>
		<link rel="stylesheet" href="../css/style.css">
<style>
	.progress-bar {
    position: relative;
}

.progress-value {
    position: absolute;
    right: 0;
    left: 0;
    text-align: center;
    color: #000;
}
</style>
		<!-- Require JS (REQUIRED) -->
		<!-- Rename "main.default.js" to "main.js" and edit it if you need configure elFInder options or any things -->
		<script data-main="./main.default.js" src="//cdnjs.cloudflare.com/ajax/libs/require.js/2.3.6/require.min.js"></script>
		<script>
			define('elFinderConfig', {
				// elFinder options (REQUIRED)
				// Documentation for client options:
				// https://github.com/Studio-42/elFinder/wiki/Client-configuration-options
				defaultOpts : {
                    cssAutoLoad : ['theme/css/theme-light.css'], // Array of additional CSS URLs
					url : 'php/connector.minimal.php', // or connector.maximal.php : connector URL (REQUIRED)
					commandsOptions : {
						edit : {
							extraOptions : {
								// set API key to enable Creative Cloud image editor
								// see https://console.adobe.io/
								creativeCloudApiKey : '',
								// browsing manager URL for CKEditor, TinyMCE
								// uses self location with the empty value
								managerUrl : ''
							}
						},
						quicklook : {
							// to enable CAD-Files and 3D-Models preview with sharecad.org
							sharecadMimes : ['image/vnd.dwg', 'image/vnd.dxf', 'model/vnd.dwf', 'application/vnd.hp-hpgl', 'application/plt', 'application/step', 'model/iges', 'application/vnd.ms-pki.stl', 'application/sat', 'image/cgm', 'application/x-msmetafile'],
							// to enable preview with Google Docs Viewer
							googleDocsMimes : ['application/pdf', 'image/tiff', 'application/vnd.ms-office', 'application/msword', 'application/vnd.ms-word', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/postscript', 'application/rtf'],
							// to enable preview with Microsoft Office Online Viewer
							// these MIME types override "googleDocsMimes"
							officeOnlineMimes : ['application/vnd.ms-office', 'application/msword', 'application/vnd.ms-word', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.oasis.opendocument.presentation']
						}
					},
					// bootCalback calls at before elFinder boot up 
					bootCallback : function(fm, extraObj) {
    /* any bind functions etc. */
    fm.bind('init', function() {
        // any your code
    });
	fm.bind('upload.done', function(event) {
        // Yükleme işlemi tamamlandığında burası çalışır
        // Bildirim veya mesaj görüntüleme kodunu buraya ekleyin
        alert('Yükleme işlemi tamamlandı');
		location.reload();

    });

    // for example set document.title dynamically.
    var title = document.title;
    fm.bind('open', function() {
        var path = '',
            cwd  = fm.cwd();
        if (cwd) {
            path = fm.path(cwd.hash) || null;
        }
        document.title = path ? path + ':' + title : title;
    }).bind('destroy', function() {
        document.title = title;
    });
}
				},
				managers : {
					// 'DOM Element ID': { /* elFinder options of this DOM Element */ }
					'elfinder': {}
				}
			});
		</script>
	</head>
	<body>
	<div class="modal" id="quotaModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Warning</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <!-- Modal body -->
                    <div class="modal-body">
                        You have reached your quota limit! You can not upload any file .
                    </div>
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
	
		<!-- Element where elFinder will be created (REQUIRED) -->
		<div id="elfinder"></div>
		<div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <?php 
                if (checkUserQuota1($kullanici_id, $conn)) {
					echo '<div class="alert alert-danger" role="alert">Full Quota</div>'; 
					$progress = 100;
				} else { 
                        echo '<div class="alert alert-success" role="alert">Quota Available</div>';
						$dir = '../upload_directory/'.$kullanici_id;

						if (is_dir($dir) && is_readable($dir)) {
							$progress = getDirectorySize1($dir) / getUserQuota1($kullanici_id, $conn) * 100;
						} else {
							die('Dizin okunamadı veya bulunamadı: ' . $dir);
						}                    }
                ?>
<div class="progress" style="height: 50px;">
    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?php echo $progress; ?>%;" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100">
        <span class="progress-value"><?php echo round($progress); ?>%</span>
    </div>
	<button id="update-quota" class="btn btn-primary" style="padding:0px;";>Update Quota</button>

</div>

            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        <?php
            if (checkUserQuota1($kullanici_id, $conn)) {
                echo '$("#quotaModal").modal("show");';
            }
        ?>

        // Update quota butonuna tıklandığında
        $('#update-quota').click(function(e) {
            e.preventDefault();

            // Sayfayı yeniden yükle
            location.reload();
        });
    });
</script>
<script>
            $(document).ready(function(){
                <?php
                    if (checkUserQuota1($kullanici_id, $conn)) {
                        echo '$("#quotaModal").modal("show");';
                    }
                ?>
            });
        </script>
	</body>
</html>
