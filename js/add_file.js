$(document).ready(function() {
  $('#newFileUploadModal').on('show.bs.modal', function() {
    var uploadFolderPathSelect = $('#newUploadFolderPath');

    uploadFolderPathSelect.select2({
      ajax: {
        url: 'get_user_folders.php',
        type: 'get',
        dataType: 'json',
        delay: 250,
        processResults: function(data) {
          var results = [];
          $.each(data, function(index, folder) {
            var optionText = folder;
            if (folder.indexOf('/') === -1) {
              optionText = '<i class="far fa-folder-open"></i> <strong>' + folder + '</strong>';
            } else {
              var depth = folder.split('/').length - 1;
              var indentation = ' '.repeat(depth * 2);
              optionText = '&nbsp;&nbsp;&nbsp;&nbsp;<i class="fas fa-folder"></i> ' + indentation + folder;
            }
            results.push({
              id: folder,
              text: optionText
            });
          });
          return {
            results: results
          };
        },
        cache: true
      },
      minimumInputLength: 0,
      dropdownParent: $('#newFileUploadModal'),
      escapeMarkup: function(markup) {
        return markup;
      },
      templateResult: function(data) {
        return data.text;
      },
      templateSelection: function(data) {
        return data.text;
      }
    });
  });

  Dropzone.options.newMyDropzone = {
    url: 'upload.php',
    paramName: "file",
    maxFilesize: 2,
    acceptedFiles: '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx',
    dictDefaultMessage: "Dosyaları buraya sürükleyin veya tıklayın",
    parallelUploads: 1,
    init: function() {
        var myDropzone = this;

        myDropzone.on("addedfile", function(file) {
            var removeButton = Dropzone.createElement("<button class='btn btn-sm btn-danger'>Sil</button>");

            removeButton.addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                myDropzone.removeFile(file);
            });

            file.previewElement.appendChild(removeButton);
        });

        myDropzone.on("sending", function(file, xhr, formData) {
          var selectedOption = $('#newUploadFolderPath').val();
          if (selectedOption === null || selectedOption === 'Please Select Folder') {
              myDropzone.removeFile(file);
              alert('Lütfen bir klasör seçin.');
          } else {
              formData.append('folderPath', selectedOption);
              formData.append('document_id', YOUR_DOCUMENT_ID); // Replace YOUR_DOCUMENT_ID with the actual document_id
          }
          for (var pair of formData.entries()) {
              console.log(pair[0]+ ', ' + pair[1]);
          }
        });

        myDropzone.on("success", function(file, response) {
            console.log(response);
            alert('Dosya başarıyla yüklendi.');
        });

        myDropzone.on("error", function(file, errorMessage) {
            alert('Dosya yüklenirken bir hata oluştu: ' + errorMessage);
        });
    }
  }

  $('#newMyDropzoneContainer').hide();
});
