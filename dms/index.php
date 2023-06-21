<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    include "database.php";

    if (!isset($_SESSION["id"]) && !isset($_SESSION["username"]) && !isset($_SESSION["email"])) {

      header("Location: login.php");

      exit();

  }

  $kullanici_id = '';

    if (isset($_SESSION["id"])) {

      $kullanici_id = $_SESSION["id"];

    }

    $query = "SELECT role FROM users WHERE id = $kullanici_id";

    $result = mysqli_query($conn, $query);

    $rol = mysqli_fetch_assoc($result)['role'];

 



    function countFilesAndFolders($dir, &$fileCount = 0, &$folderCount = 0, &$fileSize = 0) {

      $files = array_diff(scandir($dir), array('.', '..'));

  

      foreach ($files as $file) {

          $filePath = $dir . DIRECTORY_SEPARATOR . $file;

  

          if (is_file($filePath)) {

              $fileCount++;

              $fileSize += filesize($filePath);

          } elseif (is_dir($filePath)) {

              $folderCount++;

              countFilesAndFolders($filePath, $fileCount, $folderCount, $fileSize);

          }

      }

  }

  

  $directory =$_SERVER['DOCUMENT_ROOT'].'/upload_directory/'.$kullanici_id;

  

  $fileCount = 0;

  $folderCount = 0;

  $fileSize = 0;

  

  countFilesAndFolders($directory, $fileCount, $folderCount, $fileSize);



  $stmt = $conn->prepare("SELECT `quota` FROM `users` WHERE `id` = ?");

  $stmt->bind_param("i", $kullanici_id);  // "i" integer türünü belirtir

  $stmt->execute();

  $result = $stmt->get_result();

  $userQuota = $result->fetch_assoc()['quota'];



  $fileTypes = [];



$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));



foreach ($rii as $file) {

    if ($file->isDir()) continue;

    $fileType = pathinfo($file->getFilename(), PATHINFO_EXTENSION);

    if (!isset($fileTypes[$fileType])) {

        $fileTypes[$fileType] = 0;

    }

    $fileTypes[$fileType]++;

}



$fileTypeNames = array_keys($fileTypes);

$fileTypeCounts = array_values($fileTypes);



$fileSizes = array("0-1MB" => 0, "1-10MB" => 0, "10-100MB" => 0, "100MB+" => 0);



function get_file_size_category($size) {

    if ($size < 1024 * 1024) {

        return "0-1MB";

    } elseif ($size < 10 * 1024 * 1024) {

        return "1-10MB";

    } elseif ($size < 100 * 1024 * 1024) {

        return "10-100MB";

    } else {

        return "100MB+";

    }

}



$di = new RecursiveDirectoryIterator($directory);

foreach (new RecursiveIteratorIterator($di) as $filename => $file) {

    if ($file->isFile()) {

        $category = get_file_size_category($file->getSize());

        $fileSizes[$category]++;

    }

}



    ?>

    <?php 

function getUserDirectories($conn) {

  $directories = [];

  $stmt = $conn->prepare("SELECT `id`, `username` FROM `users`");

  $stmt->execute();

  $result = $stmt->get_result();

  while ($row = $result->fetch_assoc()) {

      $directories[$row['username']] = $_SERVER['DOCUMENT_ROOT'].'/upload_directory/'.$row['id'];

  }

  return $directories;

}



$userSpaceUsage = [];

$userDirectories = getUserDirectories($conn);

foreach ($userDirectories as $username => $directory) {

    $fileSize1 = 0;

    countFilesAndFolders($directory, $fileCount, $folderCount, $fileSize1);

    $userSpaceUsage[$username] = $fileSize1 / (1024 * 1024);  // MB cinsinden

}

    ?>

<!doctype html>

<html lang="en">

  <head>

  	<title>Digital Chateaux File Manager</title>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">



    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">

		

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

		<link rel="stylesheet" href="css/style.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script src="js/popper.js"></script>

    <script src="js/bootstrap.min.js"></script>

    <script src="js/main.js"></script>

    <script src="js/add_folder.js"></script>

    <script src="js/add_file.js"></script>

    <script src="js/sharefolder.js"></script>

    <script src="js/editsharefile.js"></script>

    <script src="js/editquota.js"></script>



    

    <style>

    #myDropzoneContainer {

        display: none;

    } .iframe-container {

    top: 0;

    left: 0;

    width: 100%;

    height: 100%;

    z-index: 9999;

  }
  #alertModal {
    z-index: 99999 !important; 
}
  .iframe-container iframe {

    width: 100%;

    height: 100%;

    border: none;

  }

  .truncate {

  display: inline-block;

  white-space: nowrap;

  overflow: hidden;

  text-overflow: ellipsis;

  max-width: 19ch; /* 20 karakter genişliğine kadar */

}

    

</style>



    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" rel="stylesheet">



<!-- Dropzone JS -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>



    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>





  </head>

  <body>

  <div class="modal fade" id="progressBarModal" tabindex="-1" role="dialog" aria-labelledby="progressBarModalLabel" aria-hidden="true">

  <div class="modal-dialog" role="document">

    <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title" id="progressBarModalLabel">Dosya Kopyalanıyor...</h5>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">

        <div class="progress">

          <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>

        </div>

      </div>

    </div>

  </div>

</div>

     <!-- Edit Share Modal -->

<div class="modal fade" id="editShareModal" tabindex="-1" role="dialog" aria-labelledby="editShareModalLabel" aria-hidden="true">

    <div class="modal-dialog" role="document">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="editShareModalLabel">Edit Shared Files</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                </button>

            </div>

            <div class="modal-body">

                <table id="sharedFilesTable" class="table table-striped">

                    <thead>

                        <tr>

                            <th scope="col">Folder Name</th>

                            <th scope="col">Shared With</th>

                            <th scope="col">Actions</th>

                        </tr>

                    </thead>

                    <tbody>

                        <!-- Shared folders will be fetched dynamically -->

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

                        <!-- Share file confrim -->



<div class="modal fade" id="conflictModal" tabindex="-1" role="dialog" aria-labelledby="conflictModalLabel" aria-hidden="true">

  <div class="modal-dialog" role="document">

    <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title" id="conflictModalLabel">Folder with the same name exists</h5>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">

      There is a folder with the same name. What do you want to do?

      </div>

      <div class="modal-footer">

        <button type="button" class="btn btn-primary" id="overwrite">Overwrite</button>

        <button type="button" class="btn btn-secondary" id="rename">Save with a Different Name</button>

        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>

      </div>

    </div>

  </div>

</div>



      <!-- Share Modal  -->

      <div class="modal fade" id="shareFileModal" tabindex="-1" role="dialog" aria-labelledby="shareFileModalLabel" aria-hidden="true">

    <div class="modal-dialog" role="document">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="shareFileModalLabel">Share File</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                </button>

            </div>

            <div class="modal-body">

                <form id="shareFileForm">

                    <div class="form-group">

                        <label for="uploadFolderPath">Select Folder:</label>

                        <select id="uploadFolderPath" class="form-control">

                            <!-- Folders will be fetched dynamically -->

                        </select>

                    </div>

                    <div class="form-group" id="myDropzoneContainer" style="display:none;">

                        <!-- Dropzone area -->

                    </div>

                    <div class="form-group">

                        <label for="shareWithUser">Share With User:</label>

                        <select id="shareWithUser" class="form-control">

                            <!-- Users will be fetched dynamically -->

                        </select>

                    </div>

                    <button type="submit" class="btn btn-primary">Share File</button>

                </form>

            </div>

        </div>

    </div>

</div>



<div class="modal fade" id="folderModal" tabindex="-1" role="dialog" aria-labelledby="folderModalLabel" aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="folderModalLabel">Create Folder</h5>

                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">x</button>

            </div>

            <div class="modal-body">

                <form id="folderForm">

                    <div class="form-group">

                        <label for="folderPath">Folder Path</label>

                        <select id="folderPath" class="form-control">

                            <!-- Optionlar buraya gelir -->

                        </select>

                    </div>

                    <div class="form-group">

                        <label for="folderName">Folder Name</label>

                        <input type="text" id="folderName" class="form-control">

                    </div>

                </form>

            </div>

            <div class="modal-footer">

            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                <button type="button" id="saveFolder" class="btn btn-primary">Create</button>

            </div>

        </div>

    </div>

</div>

<!-- Upload Modal -->

<div class="modal fade" id="newFileUploadModal" tabindex="-1" role="dialog" aria-labelledby="newFileUploadModalLabel" aria-hidden="true">

  <div class="modal-dialog" role="document">

    <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title" id="newFileUploadModalLabel">Dosya Yükle</h5>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <form action="upload.php" class="dropzone" id="newMyDropzone">

        <div class="modal-body">

          <div class="form-group">

            <label for="newUploadFolderPath">Dosya Yolu:</label>

            <select class="form-control" id="newUploadFolderPath" name="folderPath">

              <option selected="true">Please Select Folder</option>

              <!-- Optionları PHP ile doldurun -->

            </select>

          </div>

          <div id="newMyDropzoneContainer">

            <!-- Dropzone burada olacak -->

          </div>

        </div>

      </form>

    </div>

  </div>

</div>

	<div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">

		<div class="modal-dialog">

		  <div class="modal-content">

			<div class="modal-header">

			  <h5 class="modal-title" id="alertModalLabel">Operation Result</h5>

        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close" onClick="window.location.reload();">x</button>

			</div>

			<div class="modal-body" id="alertMessage"></div>

			<div class="modal-footer">

      <button type="button" class="btn btn-secondary" data-dismiss="modal" onClick="window.location.reload();"  >Close</button>

			</div>

		  </div>

		</div>

	  </div>

	  

		<div class="wrapper d-flex align-items-stretch">

			<nav id="sidebar">

			<div class="custom-menu">

					<button type="button" id="sidebarCollapse" class="btn btn-primary">

	          <i class="fa fa-bars"></i>

	          <span class="sr-only">Toggle Menu</span>

	        </button>

        </div>

				<div class="p-4">

		  		<h1><a href="index.php" class="logo"><img src="img/logochateaux.png"  height="50"> <span>Digital Chateaux Document</span></a></h1>

	        <ul class="list-unstyled components mb-5">

	          <li class="active">

	            <a href="index.php"><span class="fa fa-home mr-3"></span> Home</a>

	          </li>

            <li>

              

            <nav class="navbar" style="padding-left: 0px;">

    <ul class="navbar-nav">

      <li class="nav-item active">

        <a class="nav-link" href="file.php" id="navbarDropdown" role="button" data-toggle="collapse" data-target="#documentSubmenu" aria-haspopup="true" aria-expanded="false">

          <i class="far fa-folder-open mr-3"></i> Document <i class="fas fa-caret-down mr-3"></i>

        </a>

        

        <div class="collapse" id="documentSubmenu">

          <div class="mt-3">

            

            <input type="text" id="searchInput" class="form-control" placeholder="Arama yap...">

          </div>

          <a href="file.php"><span class="fa-solid fa-folder-closed mr-3"></span>All File</a>



          <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small" id="documentList">

                  

            <!-- Dinamik olarak buraya eklenen öğeler gelecek -->

          </ul>

        </div>

      </li>

    </ul>

  </nav>

 



<li>

        <a href="#" id="createFolder"><span class="fas fa-folder-plus mr-3"></span> Create Folder</a>

    </li>

    <li>

    <a href="#" data-toggle="modal" data-target="#newFileUploadModal" id="addFileMenuItem"><span class="fas fa-file-upload mr-3"></span> Add File</a>

</li>

<li>

    <a href="#" id="sharefile"><i class="fa-solid fa-swatchbook mr-3"></i> Share File</a>

</li>

<li>

<a href="#" id="editsharefile"><i class="fa-solid fa-file-pen mr-3"></i> Edit Share File</a>

</li>

<?php if ($rol == 'admin') { echo '

<li>

<a href="#" id="editquota" data-toggle="modal" data-target="#editQuotaModal"><i class="fa-solid fa-database mr-3"></i> Edit Quota</a>

</li>' ;}?>

<?php if ($rol == 'admin') { echo '

<li>

<a href="#" id="edituser" data-toggle="modal" data-target="#edituser"><i class="fa-solid fa-user-pen mr-3"></i> Edit User</a>

</li>' ;}?>





	        </ul>



	       



          <div class="footer">

    <ul class="list-unstyled components mb-5">

        <div class="dropdown">

            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">

                <span class="fa fa-user mr-3"></span> <?php echo $_SESSION["username"];?>

            </a>

            <div class="dropdown-menu">

                <a class="dropdown-item" href="logout.php">Logout</a>

            </div>

        </div>

    </ul>

</div>



    	</nav>

<!-- Modal Quate -->

<div class="modal fade" id="editQuotaModal" tabindex="-1" role="dialog" aria-labelledby="editQuotaModalLabel" aria-hidden="true">

  <div class="modal-dialog" role="document">

    <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title" id="editQuotaModalLabel">Edit Quota</h5>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">

        <form id="quotaForm">

          <div class="form-group">

            <label for="userSelect">User</label>

            <select class="form-control" id="userSelect">

              <!-- Kullanıcılar burada AJAX ile yüklenecek -->

            </select>

          </div>

          <div class="form-group">

  <label for="quotaInput">Quota (MB)</label>

  <input type="number" class="form-control" id="quotaInput" >

</div>

        </form>

      </div>

      <div class="modal-footer">

        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

        <button type="button" class="btn btn-primary" id="saveButton">Save changes</button>

      </div>

    </div>

  </div>

</div>

        <div id="content" class="p-4 p-md-5 pt-5" style="max-height: calc(100vh - 100px); overflow-y: auto;">

  <h2 class="mb-4"><?php echo $_SESSION["username"];?> Documents  </h2>

  <div class="container">

  <div class="row justify-content">

    <div class="col-lg-4">

      <div class="card">

        <div class="card-body">

          <div style="position: relative; width:100%; height:0; padding-bottom:100%; z-index:1;">

            <canvas id="sizeChart"></canvas>

            <div id="chartjs-center-message" style="position: absolute; top: 53%; left: 51%; transform: translate(-50%, -50%); font-size: 1.2em; text-align: center; z-index:-1;">

              <?php echo $userQuota; ?> Mb<br>Your Quota 

            </div>

          </div>

        </div>

        <center>You have <b><?php $kalan=$userQuota - ($fileSize / (1024 * 1024));

       echo number_format($kalan, 3);

       ?> MB</b></center>

      </div>

   

    </div>

    <div class="col-lg-4">

      <div class="card">

        <div class="card-body">

          <canvas id="countChart"></canvas>

        </div>

        <center>You have <b><?php echo $fileCount; ?></b> File and <b><?php echo  $folderCount ;?></b> Folder

      </div>

      

    </div>

    <div class="col-lg-4">

      <div class="card">

        <div class="card-body">

        <canvas id="fileTypeChart"></canvas>

        </div>

        <center>You have <b><?php $uniqueFileTypeCount = count($fileTypes);

;

echo $uniqueFileTypeCount;

 ?></b> of types file</center>

      </div>

      

    </div>

    <p></p>

    <br />

    <div class="col-lg-4" style="margin-top:20px;">

      <div class="card">

        <div class="card-body">

        <canvas id="fileSizeChart"></canvas>

        </div>

         

      </div>

      

  </div>

  <?php if ($rol == 'admin') { echo '

  <div class="col-lg-4" style="margin-top:20px;">

      <div class="card">

        <div class="card-body">

        <canvas id="userUsageChart"></canvas>

        </div>

</div>';}

?>

  </div>

  </div>

  <hr />

  <footer><center><b>This script was coded by <a href="https://digitalchateaux.com" style="color:blue;">Digital Chateaux </a></b></center></footer>





    

<script>

$(document).ready(function() {

  $('#sidebarCollapse').on('click', function() {

    $('#sidebar').toggleClass('active');

  });

  // Doküman yüklendiğinde AJAX isteği yapılacak

  loadDocuments("");



  // Arama kutusuna her karakter girildiğinde AJAX isteği yapılacak

  $("#searchInput").on("input", function() {

    var searchValue = $(this).val();

    loadDocuments(searchValue);

  });



  // AJAX isteğini yapacak fonksiyon

  function loadDocuments(search) {

    $.ajax({

      url: "get_documents.php",

      method: "POST",

      data: { search: search },

      success: function(response) {

        $("#documentList").html(response);

      }

    });

  }

});

</script>

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

<script>

window.onload = function() {

  // Get the context of the canvases

  var ctx1 = document.getElementById('sizeChart').getContext('2d');

  var ctx2 = document.getElementById('countChart').getContext('2d');



  // Define total space and used space

  var totalSpace = <?php echo $userQuota; ?>; 

var usedSpace = <?php echo $fileSize / (1024 * 1024); ?>; 



  // Calculate unused space

  var unusedSpace = totalSpace - usedSpace;



  // Create the size chart

  var sizeChart = new Chart(ctx1, {

    type: 'doughnut',

    data: {

        labels: ['Used Area', 'Unused Space'],

        datasets: [{

          data: [usedSpace.toFixed(3), unusedSpace.toFixed(3)],

            backgroundColor: ['rgb(255, 99, 132)', 'rgb(75, 192, 192)']

        }]

    },

    options: {

        responsive: true,

        plugins: {

            tooltip: {

                callbacks: {

                    label: function(context) {

                        var label = context.label || '';

                        var value = context.parsed;

                        label += ': ' + value + ' MB';

                        return label;

                    }

                }

            }

        }

    }

});



  // Create the count chart

  var countChart = new Chart(ctx2, {

      type: 'doughnut',

      data: {

          labels: ['Number of Files', 'Number of Folders'],

          datasets: [{

              data: [<?php echo $fileCount; ?>, <?php echo $folderCount; ?>],

              backgroundColor: ['rgb(255, 205, 86)', 'rgb(75, 192, 192)']

          }]

      },

      options: {

          responsive: true,

      }

  });

}

var ctx = document.getElementById('fileTypeChart').getContext('2d');



// Her bir dilim için rastgele bir RGB renk oluştur

var randomColorGenerator = function() { 

    return '#' + (Math.random().toString(16) + '0000000').slice(2, 8); 

};



var colorArray = Array(<?php echo count($fileTypeNames); ?>).fill().map(randomColorGenerator);



var fileTypeChart = new Chart(ctx, {

    type: 'doughnut',

    data: {

        labels: <?php echo json_encode($fileTypeNames); ?>,

        datasets: [{

            data: <?php echo json_encode($fileTypeCounts); ?>,

            backgroundColor: colorArray

        }]

    },

    options: {

        responsive: true,

    }

});

var colorArray = Array(Object.keys(<?php echo json_encode($fileSizes); ?>).length).fill().map(randomColorGenerator);



var ctx = document.getElementById('fileSizeChart').getContext('2d');

var chart = new Chart(ctx, {

    type: 'doughnut',

    data: {

        labels: Object.keys(<?php echo json_encode($fileSizes); ?>),

        datasets: [{

            data: Object.values(<?php echo json_encode($fileSizes); ?>),

            backgroundColor: colorArray,

            borderColor: 'rgba(75, 192, 192, 1)',

            borderWidth: 1

        }]

    },

    options: {

        responsive: true,

        plugins: {

            legend: {

                position: 'top',

            },

            title: {

                display: true,

                text: 'Distribution by File Size'

            }

        }

    }

});

var colorArray = Array(<?php echo count($userSpaceUsage); ?>).fill().map(randomColorGenerator);



var ctx = document.getElementById('userUsageChart').getContext('2d');

    var data = {

        datasets: [{

            data: <?php echo json_encode(array_values($userSpaceUsage)); ?>,

            backgroundColor:colorArray

            

        }],

        labels: <?php echo json_encode(array_keys($userSpaceUsage)); ?>

    };



    new Chart(ctx, {

        type: 'doughnut',

        data: data,

        options: {

            responsive: true,

            plugins: {

                legend: {

                    position: 'top',

                },

                title: {

                    display: true,

                    text: 'Area Used Per User'

                },

                tooltip: {

                    callbacks: {

                        label: function(context) {

                            var label = context.label || '';

                            var value = context.parsed;

                            if (label) {

                                label += ': ';

                            }

                            label += Math.round(value * 100) / 100 + ' MB';

                            return label;

                        }

                    }

                }

            },

            animation: {

                animateScale: true,

                animateRotate: true

            }

        }

    });



</script>



  </body>

</html>