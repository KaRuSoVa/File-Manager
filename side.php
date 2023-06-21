<?php
    include "database.php";

    if (isset($_SESSION['user_id']) && isset($_SESSION['user_name']) && isset($_SESSION['user_email']) && isset($_SESSION['user_role'])) {
        // Oturum değişkenlerini alın
        $user_id = $_SESSION['user_id'];
        $user_name = $_SESSION['user_name'];
        $user_email = $_SESSION['user_email'];
        $user_role = $_SESSION['user_role'];
    } else {
        echo "User session not found.";
    }
    // kullanıcı giriş yapmış mı kontrol ediyoruz
    if (!isset($_SESSION["user_id"]) && !isset($_SESSION["user_name"]) && !isset($_SESSION["user_email"])) {
    header("Location: login.php");
    exit();
    }
    ?>
<!doctype html>
<html lang="en">
  <head>
  	<title>Sidebar 05</title>
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
  .iframe-container iframe {
    width: 100%;
    height: 100%;
    border: none;
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
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">x</button>
			</div>
			<div class="modal-body" id="alertMessage"></div>
			<div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
		  		<h1><a href="index.php" class="logo"><img src="img/logo.png"  width="40" height="50"> <span>Feurst Document</span></a></h1>
	        <ul class="list-unstyled components mb-5">
	          <li class="active">
	            <a href="#"><span class="fa fa-home mr-3"></span> Home</a>
	          </li>
            <li>
            <nav class="navbar" style="
    padding-left: 0px;
">
    <ul class="navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="#" id="navbarDropdown" role="button" data-toggle="collapse" data-target="#documentSubmenu" aria-haspopup="true" aria-expanded="false">
          <i class="far fa-folder-open mr-3"></i> Document <i class="fas fa-caret-down mr-3"></i>
        </a>
        <div class="collapse" id="documentSubmenu">
          <div class="mt-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Arama yap...">
          </div>
          <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small" id="documentList">
            <!-- Dinamik olarak buraya eklenen öğeler gelecek -->
          </ul>
        </div>
      </li>
    </ul>
  </nav>

        <a href="#" id="createFolder"><span class="fas fa-folder-plus mr-3"></span> Create Folder</a>
    </li>
    <li>
    <a href="#" data-toggle="modal" data-target="#fileUploadModal" id="addFileMenuItem"><span class="fas fa-file-upload mr-3"></span> Add File</a>
</li>

	        </ul>

	       

	        <div class="footer">
	        	
	        </div>

	      </div>
    	</nav>

        <!-- Page Content  -->
        <div id="content" class="p-4 p-md-5 pt-5" style="max-height: calc(100vh - 100px); overflow-y: auto;">
  <h2 class="mb-4">Sidebar #05</h2>
  <div class="iframe-container">
    <iframe src="http://localhost/dms/tinyfilemanager.php?p=deneme" frameborder="0"></iframe>
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
<div class="modal fade" id="fileUploadModal" tabindex="-1" role="dialog" aria-labelledby="fileUploadModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="fileUploadModalLabel">Dosya Yükle</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="upload.php" class="dropzone" id="myDropzone">
        <div class="modal-body">
          <div class="form-group">
            <label for="uploadFolderPath">Dosya Yolu:</label>
            <select class="form-control" id="uploadFolderPath" name="folderPath">
              <option selected="true">Please Select Folder</option>
              <!-- Optionları PHP ile doldurun -->
            </select>
          </div>
          <div id="myDropzoneContainer">
            <!-- Dropzone burada olacak -->
          </div>
        </div>
      </form>
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