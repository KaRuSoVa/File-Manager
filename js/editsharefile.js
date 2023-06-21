$(document).ready(function() {
    $('#editsharefile').on('click', function() {
        $('#editShareModal').modal('show');
        
        // Fetch shared folders
        $.ajax({
            url: 'get_shared_folders.php',
            type: 'get',
            dataType: 'json',
            success: function(data) {
                var sharedFilesTable = $('#sharedFilesTable tbody');
                sharedFilesTable.empty();

                $.each(data, function(index, share) {
                    var row = $('<tr></tr>');
                    row.append('<td>' + share.folder + '</td>');
                    row.append('<td>' + share.username + '</td>');  // "shared_with" yerine "username" kullanıldı.
                    row.append('<td><button class="btn btn-danger btn-sm deleteShare" data-id="' + share.id + '">Delete</button></td>');
                    
                    sharedFilesTable.append(row);
                });
                
                // Handle delete buttons
                $('.deleteShare').on('click', function() {
                    var shareId = $(this).data('id');
                    
                    // Delete share
                    $.ajax({
                        url: 'delete_share.php',
                        type: 'post',
                        dataType: 'json',
                        data: { id: shareId },
                        success: function(response) {
                            if (response.status === 'success') {
                                $('#alertMessage').html('Share deleted successfully.');
                                $('#alertModal').modal('show');

                                // Remove row from table
                                $('button.deleteShare[data-id="' + shareId + '"]').closest('tr').remove();
                            } else {
                                $('#alertMessage').html('Failed to delete share: ' + response.message);
                                $('#alertModal').modal('show');

                            }
                        },
                        error: function(xhr, status, error) {
                            $('#alertMessage').html('An error occurred: ' + error);
                            $('#alertModal').modal('show');

                        }
                    });
                });
            },
            error: function(xhr, status, error) {
                $('#alertMessage').html('An error occurred: ' + error);
                $('#alertModal').modal('show');

            }
        });
    });
});
