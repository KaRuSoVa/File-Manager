$(document).ready(function() {



  function loadUsers() {

      $.ajax({

          url: 'getUsers.php',

          type: 'GET',

          dataType: 'json',

          success: function(data) {

              var $userSelect = $('#userSelect');

              $userSelect.html(''); // select içeriğini temizle

              $.each(data, function(key, value) {

                  $userSelect.append('<option value="' + value.username + '">' + value.username + '</option>');

              });

              loadQuota($userSelect.val()); // İlk kullanıcının quota bilgisini yükle

          },

          error: function(jqXHR, textStatus, errorThrown) {

              console.error("AJAX Error: ", textStatus, errorThrown);

          }

      });

  }



  function loadQuota(username) {

      $.ajax({

          url: 'get_user_quota.php',

          type: 'GET',

          data: {

              username: username

          },

          dataType: 'json',

          success: function(data) {

              if (data.quota) {

                  $('#quotaInput').val(data.quota);

              } else {

                  console.error('Error fetching quota: ', data.error);

              }

          },

          error: function(jqXHR, textStatus, errorThrown) {

              console.error("AJAX Error: ", textStatus, errorThrown);

          }

      });

  }



  // Kullanıcı seçildiğinde quota bilgisini yükle

  $('#userSelect').change(function() {

      loadQuota($(this).val());

  });



  // Başlangıçta kullanıcıları yükle

  loadUsers();

  $('#saveButton').click(function() {

    var username = $('#userSelect').val();

    var quota = $('#quotaInput').val();



    if (!quota) {

      $("#alertMessage").html('Please enter a quota.');

      $('#alertModal').modal('show');

      $('#editQuotaModal').modal('hide');

        return;

    }



    $.ajax({

        url: 'update_quota.php',

        type: 'POST',

        data: {

            username: username,

            quota: quota

        },

        dataType: 'json',

        success: function(data) {

            if (data.success) {

              $("#alertMessage").html('Quota updated successfully.');

                $('#alertModal').modal('show');

                $('#editQuotaModal').modal('hide');

            } else {

                $("#alertMessage").html('You can not quota updated this demo mode.');
                $('#alertModal').modal('show');

                $('#editQuotaModal').modal('hide');
            }

        },

        error: function(jqXHR, textStatus, errorThrown) {

            console.error("AJAX Error: ", textStatus, errorThrown);

        }

    });

});

});

