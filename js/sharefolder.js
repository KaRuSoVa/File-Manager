$(document).ready(function() {

    var uploadFolderPathSelect = $('#uploadFolderPath');

    var shareWithUserSelect = $('#shareWithUser');

    var progressBarModal = $('#progressBarModal');

    var progressBar = $('#progressBar');

    var interval = null;



    $('#sharefile').on('click', function() {

        $('#shareFileModal').modal('show');

    });



        $('#sharefile').on('click', function() {

            $('#shareFileModal').modal('show');

    



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

            dropdownParent: $('#shareFileModal'),

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

        shareWithUserSelect.select2({

            ajax: {

                url: 'get_users.php',

                type: 'get',

                dataType: 'json',

                delay: 250,

                processResults: function(data) {

                    var results = [];

                    $.each(data, function(index, user) {

                        results.push({

                            id: user.id,

                            text: user.username

                        });

                    });

                    return {

                        results: results

                    };

                },

                cache: true

            },

            minimumInputLength: 0,

            dropdownParent: $('#shareFileModal'),

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



    $('#shareFileForm').on('submit', function(event) {

        event.preventDefault();

    

        var selectedFolder = uploadFolderPathSelect.val();

        var selectedUser = shareWithUserSelect.val();

    

        $.ajax({

            url: 'copy_file.php',

            type: 'POST',

            dataType: 'json',

            data: { folder: selectedFolder, userId: selectedUser },

            beforeSend: function() {

                // Progres bar'ı göster

                $('#progressBarModal').modal('show');
                $('#shareFileModal').modal('hide');


            },

            success: function(response) {

                if (response.status === 'conflict') {

                    $('#progressBarModal').modal('hide');

                    setTimeout(function(){

                        $('#conflictModal').modal('show');

                        $('#shareFileModal').modal('hide');

                    }, 100); // 500 milisaniye bekle

                } else {

                    fillProgressBar(); // Çakışma yoksa, progress bar'ı doldurun.

                }

            },

            error: function(xhr, status, error) {

                // Progres bar'ı gizle

                $('#progressBarModal').modal('hide');

        

                console.log('Hata oluştu:', error);

                console.log(xhr.responseText);

                $('#alertMessage').html(xhr.responseText);

                $('#alertModal').modal('show');

                $('#shareFileModal').modal('hide');

            }

        });

    });

    

    // progress bar'ı dolduran fonksiyon

    function fillProgressBar() {

        // Progres bar'ı başlat

        var progressBar = $('#progressBar');

        progressBar.css('width', '0%');

        progressBar.attr('aria-valuenow', 0);

        var interval = setInterval(function() {

            var current = progressBar.attr('aria-valuenow');

            if (current < 100) {

                current++;

                progressBar.css('width', current + '%');

                progressBar.attr('aria-valuenow', current);

            } else {

                clearInterval(interval);

                // Doldurma işlemi tamamlandığında

                $('#progressBarModal').modal('hide');

                $('#alertMessage').html('Dosya başarıyla kopyalandı.');

                $('#alertModal').modal('show');

                $('#shareFileModal').modal('hide');

            }

        }, 100); // Bu değer ne kadar hızlı doldurulacağını kontrol eder

    }

    

    // conflictModal'da 'overwrite' veya 'rename' seçildiğinde progress bar'ı tekrar doldurun.

    $('#overwrite, #rename').on('click', function() {

        $('#conflictModal').modal('hide');

        fillProgressBar();

    });

    

    

    

    

    $('#overwrite').on('click', function() {

        var selectedFolder = uploadFolderPathSelect.val();

        var selectedUser = shareWithUserSelect.val();



        $.ajax({

            url: 'copy_file.php',

            type: 'POST',

            dataType: 'json',

            data: { folder: selectedFolder, userId: selectedUser, action: 'overwrite' },

            success: function(response) {

                $('#alertMessage').html(response.message);

                $('#alertModal').modal('show');

                $('#conflictModal').modal('hide');

                

            },

            error: function(xhr, status, error) {

                console.log('Hata oluştu:', error);

                console.log(xhr.responseText);

                $('#alertMessage').html(xhr.responseText);

                $('#alertModal').modal('show');

                $('#conflictModal').modal('hide');

            }

        });

    });



    $('#rename').on('click', function() {

        var selectedFolder = uploadFolderPathSelect.val();

        var selectedUser = shareWithUserSelect.val();



        $.ajax({

            url: 'copy_file.php',

            type: 'POST',

            dataType: 'json',

            data: { folder: selectedFolder, userId: selectedUser, action: 'rename' },

            success: function(response) {

                $('#alertMessage').html(response.message);

                $('#alertModal').modal('show');

                $('#conflictModal').modal('hide');

            },

            error: function(xhr, status, error) {

                console.log('Hata oluştu:', error);

                console.log(xhr.responseText);

                $('#alertMessage').html(xhr.responseText);

                $('#alertModal').modal('show');

                $('#conflictModal').modal('hide');

            }

        });

    });



    $('#cancel').on('click', function() {

        $('#conflictModal').modal('hide');

        $('#shareFileModal').modal('show');

    });

});

