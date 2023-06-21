$(document).ready(function() {
  // Create Folder menü öğesine tıklandığında modalı aç
  $('#createFolder').click(function() {
    $('#folderModal').modal('show');
  });

  var folderPathSelect = $('#folderPath');

  folderPathSelect.select2({
    ajax: {
      url: 'get_user_folders.php',
      type: 'get',
      dataType: 'json',
      delay: 250,
      processResults: function(data) {
        var results = [];
        results.push({
          id: "",
          text: '<i class="far fa-folder-open"></i> Main Directory'
        });
        $.each(data, function(index, folder) {
          var optionText = folder;
          if (folder.indexOf('/') === -1) {
            optionText = '<i class="far fa-folder-open"></i> <strong>' + folder + '</strong>';
          } else {
            var depth = folder.split('/').length - 1;
            var indentation = ''.repeat(depth * 2);
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
    dropdownParent: $('#folderModal'), // Popup'ın üzerinde görünmesini sağlar
    escapeMarkup: function(markup) {
      return markup;
    },
    templateResult: function(data) {
      return data.text;
    },
    templateSelection: function(data) {
      return data.text;
    },
    data: [{
      id: '',
      text: '<i class="far fa-folder-open"></i> Main Directory'
    }]
  });

  $('#folderModal').on('shown.bs.modal', function () {
    folderPathSelect.val('').trigger("change");
  });


  $("#saveFolder").click(function() {
    var folderPath = $("#folderPath").val();
    var folderName = $("#folderName").val();
    // folderName alanının boş olup olmadığını kontrol eder
    if (folderName.trim() === '') {
      $("#alertMessage").html("The Folder Name field cannot be left blank! Please Try Again");
      $('#alertModal').modal('show');
      $('#folderModal').modal('hide');
      return false;
    }

    $.ajax({
      url: 'add_folder.php',
      type: 'post',
      data: {
        folderPath: folderPath,
        folderName: folderName
      },
      success: function(response) {
        // Show the alert
        $("#alertMessage").html(response);
        // Show the modal
        $('#alertModal').modal('show');
        // Close the modal
        $('#folderModal').modal('hide');
      },
      error: function() {
        // Show the alert
        $("#alertMessage").html("An error occurred");
        // Show the modal
        $('#alertModal').modal('show');
      }
    });
  });
});
  