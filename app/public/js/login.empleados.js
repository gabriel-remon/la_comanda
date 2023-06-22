

document.getElementById('login-form').addEventListener('submit', function(event) {
    event.preventDefault();

    var formData = new FormData(this);

    fetch('/usuarios/login/empleados', {
        method: 'POST',
        body: formData
    })
    .then(function(response) {
        if (response.status == 200) {
            Swal.fire(
                '¡Éxito!',
                'Has iniciado sesión correctamente.',
                'success'
            )
            .then(function() {
               // window.location.href = '/productos';
            });
        } else {
            Swal.fire(
                'Error',
                'No se pudo realizar el login, vuelve a intentar.',
                'error'
            );
        }
    });
});
