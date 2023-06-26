
document.getElementById('tabla-pedidos').addEventListener('click', function (e) {

    //console.log(e.target.getAttribute('idPedido'))
    if (e.target.textContent == 'tomar pedido') {
        Swal.fire({
            title: 'Ingrese un tiempo estimado',
            input: 'select',
            inputOptions: {
                5: '5 minutos',
                10: '10 minutos',
                15: '15 minutos',
                20: '20 minutos',
                30: '30 minutos',
                40: '40 minutos'
            },
            inputPlaceholder: 'Tiempo estimado',
            showCancelButton: true,
            inputValidator: (value) => {

                fetch('/api/pedidos/preparar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify( {
                        id_pedido: e.target.getAttribute('idPedido'),
                        tiempo_estimado: value
                      })
                })
                    .then(function (response) {
                        if (response.status == 200) {
                            Swal.fire(
                                '¡Éxito!',
                                'tomaste el pedido',
                                'success'
                            )
                                .then(function () {
                                    location.reload();
                                });
                        } else {
                            Swal.fire(
                                'Error',
                                'No se pudo realizar la accion',
                                'error'
                            );
                        }
                    });
            }
        })
    }


    if (e.target.textContent == 'entregar pedido') {
        fetch('/api/pedidos/preparar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify( {
                id_pedido: e.target.getAttribute('idPedido'),
              })
        })
            .then(function (response) {
                if (response.status == 200) {
                    Swal.fire(
                        '¡Éxito!',
                        'entregaste el pedido',
                        'success'
                    )
                        .then(function () {
                            location.reload();
                        });
                } else {
                    Swal.fire(
                        'Error',
                        'No se pudo realizar la accion',
                        'error'
                    );
                }
            });
    }
});

