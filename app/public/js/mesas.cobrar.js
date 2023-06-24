document.getElementById('login-form').addEventListener('submit', function(event) {
    event.preventDefault();


    idComanda = this.getAttribute('idComanda')

    fetch('/mesas/cobrar/'+idComanda, {
        method: 'POST',
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
                        location.reload();
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
