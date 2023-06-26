<?php

include_once __DIR__ . '/mesas.router.php';
include_once __DIR__ . '/pedidos.router.php';
include_once __DIR__ . '/productos.router.php';
include_once __DIR__ . '/usuarios.router.php';
include_once __DIR__ . '/views.router.php';
include_once __DIR__ . '/../models/Producto.php';

include_once __DIR__ . '/../middlewares/validarFormato.php';
include_once __DIR__ . '/../middlewares/Logger.php';

class indexRouter
{
    function __invoke($app)
    {

        /**
         * funcionalidad de la api rest 
         */
        $app->group('/api', function ($group) {

            $group->group('/usuarios', function ($group) {


                $group->post('[/]', \routerUsuarios::class . ':TraerTodos')
                    ->add(\Logger::validarRoles(['admin']))
                    ->add(\Logger::class . ':validarJWTUsuario'); // creo que se puede juntar con validarRoles

                $group->post('/login/empleados', \routerUsuarios::class . ':TraerUno');

                $group->post('/login/clientes', \routerMesas::class . ':loginCliente');

                $group->post('/logout', \routerUsuarios::class . ':logout');

                $group->post('/singup', \routerUsuarios::class . ':CargarUno')
                    ->add(\validarFormato::class . ':usuario')
                    ->add(\validarFormato::class . ':sector')
                    ->add(\Logger::validarRoles(['admin']))
                    ->add(\Logger::class . ':validarJWTUsuario');

                $group->put('/', \routerUsuarios::class . ':ModificarUno')
                    ->add(\validarFormato::class . ':usuario')
                    ->add(\validarFormato::class . ':sector')
                    ->add(\Logger::validarRoles(['admin']))
                    ->add(\Logger::class . ':validarJWTUsuario');

                $group->delete('/{id}', \routerUsuarios::class . ':BorrarUno')
                    ->add(\Logger::validarRoles(['admin']))
                    ->add(\Logger::class . ':validarJWTUsuario');
            });

            $group->group('/productos', function ($group) {
                $group->get('[/]', \routerProductos::class . ':TraerTodos');

                $group->get('/{id}', \routerProductos::class . ':TraerUno');

                $group->post('[/]', \routerProductos::class . ':CargarUno')
                    ->add(\validarFormato::class . ':producto')
                    ->add(\validarFormato::class . ':sector')
                    ->add(\Logger::validarRoles(['admin']))
                    ->add(\Logger::class . ':validarJWTUsuario');

                $group->put('/{id}', \routerProductos::class . ':ModificarUno')
                    ->add(\validarFormato::class . ':producto')
                    ->add(\validarFormato::class . ':sector')
                    ->add(\Logger::validarRoles(['admin']))
                    ->add(\Logger::class . ':validarJWTUsuario');

                $group->delete('/{id}', \routerProductos::class . ':BorrarUno')
                    ->add(\Logger::validarRoles(['admin']))
                    ->add(\Logger::class . ':validarJWTUsuario');
            });

            $group->group('/mesas', function ($group) {


                $group->post('/listoParaPagar/{id}', \routerMesas::class . ':listoParaPagar')
                    ->add(\Logger::validarRoles(['admin', 'mesero']));

                $group->post('/cobrar/{id}', \routerMesas::class . ':cobrar')
                    ->add(\Logger::validarRoles(['admin']));

                $group->post('/cargar', \routerMesas::class . ':CargarUno')
                    ->add(\validarFormato::class . ':mesa')
                    ->add(\Logger::validarRoles(['admin', 'mesero']));

                $group->post('/cargarfoto', \routerMesas::class . ':CargarFoto')
                    ->add(\Logger::validarRoles(['admin', 'mesero']));

                $group->put('/{id}', \routerMesas::class . ':ModificarUno')
                    ->add(\validarFormato::class . ':mesa')
                    ->add(\Logger::validarRoles(['admin', 'mesero']));

                $group->delete('/{id}', \routerMesas::class . ':BorrarUno')
                    ->add(\Logger::validarRoles(['admin', 'mesero']));
            })
                ->add(\Logger::class . ':validarJWTUsuario');

            $group->group('/pedidos', function ($group) {

                $group->post('/', \routerPedidos::class . ':TraerTodos');

                $group->get('/{id}', \routerPedidos::class . ':TraerUno');

                $group->post('/cargarPedido', \routerPedidos::class . ':CargarUno')
                    ->add(\validarFormato::class . ':altaPedido')
                    ->add(\Logger::validarRoles(['admin', 'mesero']));

                $group->put('/{id}', \routerPedidos::class . ':ModificarUno')
                    ->add(\validarFormato::class . ':pedido')
                    ->add(\Logger::validarRoles(['admin']));

                $group->delete('/{id}', \routerPedidos::class . ':BorrarUno')
                    ->add(\Logger::validarRoles(['admin']));

                $group->post('/preparar', \routerPedidos::class . ':preparar')
                    ->add(\validarFormato::class . ':prepararPedido');
            })
                ->add(\Logger::class . ':validarJWTUsuario');
        });

        /**
         * vista del cliente para manejar de una mejor manera la api
         */
        $app->group('/', function ($group) {

            $group->get('', function ($request, $response, $args) {
                return  $response->withHeader('Location', '/productos')->withStatus(302);
            });

            $group->group('usuarios', function ($group) {
                $group->get('[/]', \routerUsuarios::class . ':TraerTodos')
                    ->add(\ViewRouter::class . ':mostrarUsuarios')
                    ->add(\Logger::validarRoles(['admin']))
                    ->add(\Logger::class . ':validarJWTUsuario'); // creo que se puede juntar con validarRoles

                $group->get('/login', \ViewRouter::class . ':login');

                $group->get('/login/empleados', \ViewRouter::class . ':loginEmpleados');

                $group->get('/login/clientes', \ViewRouter::class . ':loginClientes');

                $group->get('/logout', \ViewRouter::class . ':logout');

                $group->get('/singup', \ViewRouter::class . ':singup')
                    ->add(\Logger::validarRoles(['admin']))
                    ->add(\Logger::class . ':validarJWTUsuario');
            });

            $group->group('productos', function ($group) {
                $group->get('[/]', \routerProductos::class . ':TraerTodos')
                    ->add(\ViewRouter::class . ':mostrarProductos');
                /*mostrarProductos
            $group->get('/{id}', \routerProductos::class . ':TraerUno');
            */
            });

            $group->group('mesas', function ($group) {
                $group->get('/cargar', \ViewRouter::class . ':cargarMesa')
                    ->add(\Logger::validarRoles(['admin', 'mesero']))
                    ->add(\Logger::class . ':validarJWTUsuario');

                $group->get('[/]', \routerMesas::class . ':TraerTodos')
                    ->add(\ViewRouter::class . ':mesas')
                    ->add(\Logger::validarRoles(['admin', 'mesero']))
                    ->add(\Logger::class . ':validarJWTUsuario');

                $group->get('/{id}', \routerMesas::class . ':TraerUno')
                    ->add(\ViewRouter::class . ':unaMesas')
                    ->add(\Logger::class . ':validarJWTUsuario');
            });

            $group->group('pedidos', function ($group) {
                $group->get('/cargarPedido', \ViewRouter::class . ':CargarPedido')
                    ->add(\Logger::validarRoles(['admin', 'mesero']))
                    ->add(\Logger::class . ':validarJWTUsuario');

                $group->get('[/]', \routerPedidos::class . ':TraerTodos')
                    ->add(\ViewRouter::class . ':pedidos')
                    ->add(\Logger::class . ':validarJWTUsuario');

                $group->get('/{id}', \routerPedidos::class . ':TraerUno')
                    ->add(\Logger::class . ':validarJWTUsuario');
            });
        });


        /*

    $app->group('[/]', function ($group) {
    
        $group->get('', function ($request, $response, $args)
        {
            return  $response->withHeader('Location', '/productos')->withStatus(302);
        });
   
    });
    
    
    $app->group('/usuarios', function ($group) {
        
        //$group->get('[/]', \ViewRouter::class . ':usuarios');
        $group->get('[/]', \routerUsuarios::class . ':TraerTodos')
        ->add(\ViewRouter::class.':mostrarUsuarios')
        ->add(\Logger::validarRoles(['admin']))
        ->add(\Logger::class.':validarJWTUsuario');// creo que se puede juntar con validarRoles

        $group->post('/api[/]', \routerUsuarios::class . ':TraerTodos')
        ->add(\Logger::validarRoles(['admin']))
        ->add(\Logger::class.':validarJWTUsuario');// creo que se puede juntar con validarRoles
        
        $group->get('/login', \ViewRouter::class . ':login');

        $group->get('/login/empleados', \ViewRouter::class . ':loginEmpleados');
        $group->post('/login/empleados', \routerUsuarios::class . ':TraerUno');

        $group->get('/login/clientes', \ViewRouter::class . ':loginClientes');
        $group->post('/login/clientes', \routerMesas::class . ':loginCliente');

        $group->get('/logout', \ViewRouter::class . ':logout');
        $group->post('/logout', \routerUsuarios::class . ':logout');
        
        $group->get('/singup', \ViewRouter::class . ':singup')
        ->add(\Logger::validarRoles(['admin']))
        ->add(\Logger::class.':validarJWTUsuario');
        $group->post('/singup', \routerUsuarios::class . ':CargarUno')
        ->add(\validarFormato::class . ':usuario')
        ->add(\validarFormato::class . ':sector')
        ->add(\Logger::validarRoles(['admin']))
        ->add(\Logger::class.':validarJWTUsuario');

        $group->put('/', \routerUsuarios::class . ':ModificarUno')
        ->add(\validarFormato::class . ':usuario')
        ->add(\validarFormato::class . ':sector')
        ->add(\Logger::validarRoles(['admin']))
        ->add(\Logger::class.':validarJWTUsuario');

        $group->delete('/{id}', \routerUsuarios::class . ':BorrarUno')
        ->add(\Logger::validarRoles(['admin']))
        ->add(\Logger::class.':validarJWTUsuario');
    });
    
    $app->group('/productos', function ($group) {
        $group->get('[/]', \routerProductos::class . ':TraerTodos');

        $group->get('/{id}', \routerProductos::class . ':TraerUno');

        $group->post('[/]', \routerProductos::class . ':CargarUno')
        ->add(\validarFormato::class . ':producto')
        ->add(\validarFormato::class . ':sector')
        ->add(\Logger::validarRoles(['admin']))
        ->add(\Logger::class.':validarJWTUsuario');

        $group->put('/{id}', \routerProductos::class . ':ModificarUno')
        ->add(\validarFormato::class . ':producto')
        ->add(\validarFormato::class . ':sector')
        ->add(\Logger::validarRoles(['admin']))
        ->add(\Logger::class.':validarJWTUsuario');

        $group->delete('/{id}', \routerProductos::class . ':BorrarUno')
        ->add(\Logger::validarRoles(['admin']))
        ->add(\Logger::class.':validarJWTUsuario');
    });

    $app->group('/mesas', function ($group) {
        $group->get('/cargar', \ViewRouter::class . ':cargarMesa')
        ->add(\Logger::validarRoles(['admin','mesero']))
        ->add(\Logger::class.':validarJWTUsuario');
        
        $group->post('/listoParaPagar/{id}', \routerMesas::class . ':listoParaPagar')
        ->add(\Logger::validarRoles(['admin','mesero']))
        ->add(\Logger::class.':validarJWTUsuario');

        $group->post('/cobrar/{id}', \routerMesas::class . ':cobrar')
        ->add(\Logger::validarRoles(['admin']))
        ->add(\Logger::class.':validarJWTUsuario');

        $group->get('[/]', \routerMesas::class . ':TraerTodos')
        ->add(\ViewRouter::class . ':mesas')
        ->add(\Logger::validarRoles(['admin','mesero']))
        ->add(\Logger::class.':validarJWTUsuario');
        
        $group->get('/{id}', \routerMesas::class . ':TraerUno')
        ->add(\ViewRouter::class . ':unaMesas')
        ->add(\Logger::class.':validarJWTUsuario');

        $group->post('/cargar', \routerMesas::class . ':CargarUno')
        ->add(\validarFormato::class . ':mesa')
        ->add(\Logger::validarRoles(['admin','mesero']))
        ->add(\Logger::class.':validarJWTUsuario');

        $group->post('/cargarfoto', \routerMesas::class . ':CargarFoto')
        ->add(\Logger::validarRoles(['admin','mesero']))
        ->add(\Logger::class.':validarJWTUsuario');

        $group->put('/{id}', \routerMesas::class . ':ModificarUno')
        ->add(\validarFormato::class . ':mesa')
        ->add(\Logger::validarRoles(['admin','mesero']))
        ->add(\Logger::class.':validarJWTUsuario');

        $group->delete('/{id}', \routerMesas::class . ':BorrarUno')
        ->add(\Logger::validarRoles(['admin','mesero']))
        ->add(\Logger::class.':validarJWTUsuario');

    });

    $app->group('/pedidos', function ($group){
        $group->get('/cargarPedido', \ViewRouter::class . ':CargarPedido')
        ->add(\Logger::validarRoles(['admin','mesero']))
        ->add(\Logger::class.':validarJWTUsuario');
        $group->get('[/]', \routerPedidos::class . ':TraerTodos')
        ->add(\ViewRouter::class.':pedidos')
        ->add(\Logger::class.':validarJWTUsuario');
        $group->post('/api', \routerPedidos::class . ':TraerTodos')
        ->add(\Logger::class.':validarJWTUsuario');

        $group->get('/{id}', \routerPedidos::class . ':TraerUno');
        
        
    
        $group->post('/cargarPedido', \routerPedidos::class . ':CargarUno')
        ->add(\validarFormato::class . ':altaPedido')
        ->add(\Logger::validarRoles(['admin','mesero']))
        ->add(\Logger::class.':validarJWTUsuario');
        
        $group->put('/{id}', \routerPedidos::class . ':ModificarUno')
        ->add(\validarFormato::class . ':pedido');
        
        $group->delete('/{id}', \routerPedidos::class . ':BorrarUno');
        $group->post('/preparar', \routerPedidos::class . ':preparar')
        ->add(\validarFormato::class . ':prepararPedido')
        ->add(\Logger::class.':validarJWTUsuario');
    
    });
    */
    }
}
