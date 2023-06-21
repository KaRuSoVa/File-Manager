$(document).ready(function() {
    $('.my-login-validation').on('submit', function(e) {
        e.preventDefault();
        
        var email = $('#email').val();
        var password = $('#password').val();
        
        $.ajax({
            url: 'login-check.php',
            type: 'POST',
            data: {
                email: email,
                password: password
            },
            success: function(response) {
                if(response === 'success'){
                    window.location.href = 'index.php';
                } else {
                    $('#loginErrorModal').modal('show');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('An error occurred. Please try again later.');
            }
        });
    });
});
