
document.getElementById('login-form').addEventListener('submit', function(event) {
    event.preventDefault();

    var formData = new FormData(this);
    var url = ''
    var method = ''
    
    if(event.submitter.textContent !== 'Logout'){
        return window.location.href = '/productos';
        
        
        
    }else{
        fetch('/api/usuarios/logout', {
            method: 'POST'
        })
        .then(function(response) {
            if (response.status == 200) {
                Swal.fire(
                    '¡Éxito!',
                    'seccion cerrada',
                    'success'
                )
                .then(function() {
                    window.location.href = '/usuarios/login';
                });
            } else {
                Swal.fire(
                    'Error',
                    'No se pudo realizar el login, vuelve a intentar.',
                    'error'
                );
            }
        });
    }

});
