$(document).ready(function(){

  // Fetch users when editUserLink is clicked
  $('#editUserLink').click(function(){
    $.ajax({
      url: 'fetch_users.php',
      type: 'get',
      dataType: 'json',
      success: function(response){
        $("#users").empty();
        for(var i = 0; i < response.length; i++){
          $("#users").append("<option value='"+response[i].id+"'>"+response[i].name+"</option>");
        }
        $('#users').trigger('change'); // Trigger change event to fetch the role of the first user
      },
      error: function(jqXHR, textStatus, errorThrown){
         console.log(textStatus, errorThrown);
      }
    });
  });

  // Fetch user role when a user is selected
  $('#users').change(function(){
    var userid = $(this).val();
    $.ajax({
      url: 'fetch_roles.php',
      type: 'get',
      data: {id: userid},
      dataType: 'json',
      success: function(response){
        console.log(response); // Log the response to console
        if(response && response['role']){
          $("#roles").empty();
          var role = response['role'];
          $("#roles").append("<option value='admin' "+(role == 'admin' ? 'selected':'')+">Admin</option>");
          $("#roles").append("<option value='normal' "+(role == 'normal' ? 'selected':'')+">Normal</option>");
        }else{
          console.error("Unexpected response");
        }
      },
      error: function(jqXHR, textStatus, errorThrown){
         console.error(textStatus, errorThrown); // Log errors to console
      }
    });
  });

  // Update user role when saveChanges button is clicked
  $('#saveChanges').click(function(){
    var userid = $('#users').val();
    var role = $('#roles').val();
    $.ajax({
      url: 'update_user_role.php',
      type: 'post',
      data: {user_id: userid, role: role},
      success: function(response){
        $("#alertMessage").html(response);
        $('#alertModal').modal('show');
        $('#editUserModal').modal('hide');

      },
      error: function(jqXHR, textStatus, errorThrown){
         console.log(textStatus, errorThrown);
      }
    });
  });

});
