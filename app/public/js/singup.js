

document.getElementById('login-form').addEventListener('submit', function(event) {
    event.preventDefault();

    var formData = new FormData(this);

    fetch('/usuarios/singup', {
        method: 'POST',
        body: formData
    })
    .then(function(response) {
        response.text().then(
            data=>{
                if (response.status == 200) {
                    Swal.fire(
                        '¡Éxito!',
                        data,
                        'success'
                    )
                    .then(function() {
                       // window.location.href = '/productos';
                    });
                } else {
                    Swal.fire(
                        'Error',
                        data,
                        'error'
                    );
               }
            });
    });
});
