$(document).ready(function() {
    $('#register-form').on('submit', function(e) {
        e.preventDefault();
        
        var name = $('#name').val();
        var email = $('#email').val();
        var password = $('#password').val();
        
        $.ajax({
            url: 'register.php',
            type: 'POST',
            data: {
                name: name,
                email: email,
                password: password
            },
            success: function(response) {
                // Başarı durumunda modalı göster
                $('#successModal').modal('show');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Hata durumunda yapılacak işlemler
                // Örneğin, kullanıcıya bir hata mesajı gösterebilirsiniz
            }
        });
    });
    
    // Modal kapatıldığında kullanıcıyı giriş sayfasına yönlendir
    $('#successModal').on('hidden.bs.modal', function (e) {
        window.location.href = 'login.html';
    });
});
    