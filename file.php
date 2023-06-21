<?php



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

  

  function checkUserQuota($user_id, $conn) {

      $user_folder = 'upload_directory/'.$user_id;

      $user_quota = getUserQuota($user_id, $conn);

      $folder_size = getDirectorySize($user_folder);

      return $folder_size > $user_quota;

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


    <script src="js/sharefolder.js"></script>

    <script src="js/editsharefile.js"></script>

    <script src="js/editquota.js"></script>

    <script src="js/edituser.js"></script>


    

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





  </head>

  <body>

    

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

            <nav class="navbar" style="

    padding-left: 0px;

">

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
<a href="#" id="editUserLink" data-toggle="modal" data-target="#editUserModal"><i class="fa-solid fa-user-pen mr-3"></i> Edit User</a>
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
 <!-- Modal User-->
 <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <label for="users">User</label>
    <select id="users" name="users" class="form-control"></select>
    <label for="roles">Role</label>
    <select id="roles" name="roles" class="form-control"></select>      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="saveChanges">Save changes</button>
      </div>
    </div>
  </div>
</div>
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







        <!-- Page Content  -->

        <div id="content" class="p-4 p-md-5 pt-5" style="max-height: calc(100vh - 100px); overflow-y: auto;">

  <h2 class="mb-4"><?php echo $_SESSION["username"];?> Documents  </h2>

  <div class="iframe-container">

 

    <iframe src="filemanager/file.php" frameborder="0"></iframe>

  

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



  </body>

</html>