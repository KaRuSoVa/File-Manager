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

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />
  <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="css/sidebars.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap" rel="stylesheet">
 

  <!-- JavaScript -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.full.min.js"></script>
  <script src="js/add_folder.js"></script>
  <script src="js/add_file.js"></script>
  <script src="assets/dist/js/bootstrap.bundle.min.js"></script>
<!-- Dropzone CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" rel="stylesheet">

<!-- Dropzone JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>




</head>
<body>
  <!-- Alert Modal -->
  <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="alertModalLabel">Operation Result</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="alertMessage"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
 
  <main class="d-flex flex-nowrap">
    <h1 class="visually-hidden">Sidebars examples</h1>
    <div class="d-flex flex-column flex-shrink-0 p-3 text-bg-dark" style="width: 280px;">
      <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <img src="img/logo.png" class="bi pe-none me-2" width="40" height="50">
        <span class="fs-4">Feurst Document</span>
      </a>
      <hr>
      <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
          <a href="index.php" class="nav-link active" aria-current="page">
            <i class="fas fa-home"></i> Home
          </a>
        </li>
        <li class="nav-item">
  <div class="accordion" id="documentMenu">
    <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" data-bs-toggle="collapse" data-bs-target="#documentSubmenu" aria-expanded="false">
      <i class="far fa-folder-open"></i> Document
    </a>
    
    <div id="documentSubmenu" class="collapse" data-bs-parent="#documentMenu">
    <div class="mt-3">
  <input type="text" id="searchInput" class="form-control" placeholder="Arama yap...">
</div>
<hr />
    <ul id="documentList" class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
  <!-- Dinamik olarak buraya eklenen öğeler gelecek -->
</ul>


    </div>
  </div>
</li>
      </ul>
      <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
        <a href="#" class="nav-link text-white" data-bs-toggle="modal" data-bs-target="#fileUploadModal">
    <i class="fas fa-file-upload"></i> Dosya Ekle
  </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link text-white" data-bs-toggle="modal" data-bs-target="#folderModal">
            <i class="fas fa-folder-plus"></i> Add Folder
          </a>
        </li>
      </ul>
      <hr>
    </div>
    <div class="b-example-divider b-example-vr"></div>
  </main>

<!-- Modal Add Docs-->
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
              <option selected="true"><strong>Please Select Folder</strong></option>
              <!-- Optionları PHP ile doldurun -->
            </select>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>


  <!-- Modal Add Folder-->
  <div class="modal fade" id="folderModal" tabindex="-1" aria-labelledby="folderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="folderModalLabel">Add Folder</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="addFolderForm">
            <div class="mb-3">
              <label for="folderPath" class="form-label" style="color:black;">Folder Path</label>
              <select class="form-control" id="folderPath">

              </select>
              
            </div>
            <div class="mb-3">
              <label for="folderName" class="form-label" style="color:black;">Folder Name</label>
              <input type="text" class="form-control" id="folderName" required>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="saveFolder">Save</button>
        </div>
      </div>
    </div>
  </div>
<script>
$(document).ready(function() {
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
</body>
</html>