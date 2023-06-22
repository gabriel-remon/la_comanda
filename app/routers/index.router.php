<?php

include_once __DIR__.'/mesas.router.php';
include_once __DIR__.'/pedidos.router.php';
include_once __DIR__.'/productos.router.php';
include_once __DIR__.'/usuarios.router.php';
include_once __DIR__.'/views.router.php';
include_once __DIR__.'/../models/Producto.php';

include_once __DIR__.'/../middlewares/validarFormato.php';
include_once __DIR__.'/../middlewares/Logger.php';

class indexRouter{
    function __invoke($app) {
    // Aquí se definen las rutas dentro del grupo
    $app->group('[/]', function ($group) {
    /*
        $group->get('[/]', function ($request, $response, $args)
        {
            $view = $request->getAttribute('view');
            return $view->render($response, 'indez.twig', []);
        });
   */
    });
    
    
    $app->group('/usuarios', function ($group) {
        
        //$group->get('[/]', \ViewRouter::class . ':usuarios');
        $group->get('[/]', \routerUsuarios::class . ':TraerTodos')
        ->add(\Logger::validarRoles(['admin']))
        ->add(\Logger::class.':validarJWTUsuario');// creo que se puede juntar con validarRoles
        
        $group->get('/login', \ViewRouter::class . ':login');
        $group->get('/login/empleados', \ViewRouter::class . ':loginEmpleados');
        $group->post('/login/empleados', \routerUsuarios::class . ':TraerUno');
        $group->get('/login/clientes', \ViewRouter::class . ':loginClientes');
        $group->post('/login/clientes', \routerMesas::class . ':loginCliente');
        
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
        
        $group->get('[/]', \routerMesas::class . ':TraerTodos');
        
        $group->get('/{id}', \routerMesas::class . ':TraerUno');
        
        $group->post('[/]', \routerMesas::class . ':CargarUno')
        ->add(\validarFormato::class . ':mesa');
        $group->put('/{id}', \routerMesas::class . ':ModificarUno')
        ->add(\validarFormato::class . ':mesa')
        ->add(\Logger::validarRoles(['admin','mesero']))
        ->add(\Logger::class.':validarJWTUsuario');

        $group->delete('/{id}', \routerMesas::class . ':BorrarUno')
        ->add(\Logger::validarRoles(['admin','mesero']))
        ->add(\Logger::class.':validarJWTUsuario');
    });

    $app->group('/pedidos', function ($group){
        $group->get('[/]', \routerPedidos::class . ':TraerTodos')
        ->add(\Logger::class.':validarJWTUsuario');

        $group->get('/{id}', \routerPedidos::class . ':TraerUno');
        
        $group->post('[/]', \routerPedidos::class . ':CargarUno')
        ->add(\validarFormato::class . ':altaPedido');

        $group->put('/{id}', \routerPedidos::class . ':ModificarUno')
        ->add(\validarFormato::class . ':pedido');

        $group->delete('/{id}', \routerPedidos::class . ':BorrarUno');
    
    });
    
}
}