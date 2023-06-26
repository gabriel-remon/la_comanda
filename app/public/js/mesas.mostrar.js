document.getElementById('cargarFoto')?.addEventListener('click', function(event) {
    const idComanda = this.getAttribute('idComanda');

        Swal.fire({
          title: 'Seleccionar foto',
          html: '<input type="file" id="inputFoto" accept="image/*">',
          showCancelButton: true,
          preConfirm: function() {
            var foto = document.getElementById('inputFoto').files[0];
            if (foto) {
              var formData = new FormData();
              formData.append('foto', foto);
              formData.append('idComanda', idComanda);
              console.log(foto);
              // Aquí puedes realizar la petición AJAX para enviar la foto al servidor
            }
          }
        });
     

    fetch(`/mesas/listoParaPagar/${idComanda}`, {
        method: 'POST',
    })
    .then(function(response) {
        response.text().then(
            data=>{
                if (response.status == 200) {
                    Swal.fire(
                        '¡Éxito!',
                        `comanda ${idComanda} lista para pagar`,
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

document.getElementById('tabla-mesas').addEventListener('dblclick', function(e) {
    
    if(e.target.parentNode.parentNode.tagName === "TBODY"){
        
        const idComanda = e.target.parentNode.getAttribute('idComanda');
     
        window.location.href = '/mesas/'+ idComanda;
    }

});
document.getElementById('tabla-mesas').addEventListener('click', function(e) {
    
    if(e.target.tagName === "BUTTON"){
        
        const idComanda = e.target.getAttribute('idComanda');
        
        Swal.fire({
          title: 'Seleccionar foto',
          html: '<input type="file" id="inputFoto" accept="image/*">',
          showCancelButton: true,
          preConfirm: function() {
            var foto = document.getElementById('inputFoto').files[0];
            if (foto) {
              var formData = new FormData();
              formData.append('imagen_mesa', foto);
              formData.append('idComanda', idComanda);

              fetch(`/api/mesas/cargarfoto`, {
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
                               // location.reload();
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
            }
          }
        });
    }

});
