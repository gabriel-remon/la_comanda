<?php

include_once __DIR__.'/mesas.router.php';
include_once __DIR__.'/pedidos.router.php';
include_once __DIR__.'/productos.router.php';
include_once __DIR__.'/usuarios.router.php';
include_once __DIR__.'/../models/Producto.php';

include_once __DIR__.'/../middlewares/validarFormato.php';

class indexRouter{
    function __invoke($app) {
    // Aquí se definen las rutas dentro del grupo
    $app->group('[/]', function ($group) {
    });
    
    
    $app->group('/usuarios', function ($group) {
        
        $group->get('[/]', \routerUsuarios::class . ':TraerTodos');
        
        $group->get('/login', \routerUsuarios::class . ':TraerUno');
        
        $group->post('/singup', \routerUsuarios::class . ':CargarUno')
        ->add(\validarFormato::class . ':usuario')
        ->add(\validarFormato::class . ':sector');

        $group->put('/', \routerUsuarios::class . ':ModificarUno')
        ->add(\validarFormato::class . ':usuario')
        ->add(\validarFormato::class . ':sector');

        $group->delete('/{id}', \routerUsuarios::class . ':BorrarUno');
    });
    
    $app->group('/productos', function ($group) {
        $group->get('[/]', \routerProductos::class . ':TraerTodos');

        $group->get('/{id}', \routerProductos::class . ':TraerUno');

        $group->post('[/]', \routerProductos::class . ':CargarUno')
        ->add(\validarFormato::class . ':producto')
        ->add(\validarFormato::class . ':sector');

        $group->put('/{id}', \routerProductos::class . ':ModificarUno')
        ->add(\validarFormato::class . ':producto')
        ->add(\validarFormato::class . ':sector');

        $group->delete('/{id}', \routerProductos::class . ':BorrarUno');
    });

    $app->group('/mesas', function ($group) {
        
        $group->get('[/]', \routerMesas::class . ':TraerTodos');
        
        $group->get('/{id}', \routerMesas::class . ':TraerUno');
        
        $group->post('[/]', \routerMesas::class . ':CargarUno')
        ->add(\validarFormato::class . ':mesa');
        $group->put('/{id}', \routerMesas::class . ':ModificarUno')
        ->add(\validarFormato::class . ':mesa');
        $group->delete('/{id}', \routerMesas::class . ':BorrarUno');
    });

    $app->group('/pedidos', function ($group){
        $group->get('[/]', \routerPedidos::class . ':TraerTodos');

        $group->get('/{id}', \routerPedidos::class . ':TraerUno');
        
        $group->post('[/]', \routerPedidos::class . ':CargarUno')
        ->add(\validarFormato::class . ':pedido');

        $group->put('/{id}', \routerPedidos::class . ':ModificarUno')
        ->add(\validarFormato::class . ':pedido');

        $group->delete('/{id}', \routerPedidos::class . ':BorrarUno');
    })->add(function($req,$handler){
        $res = $handler->handle($req);
        $res->getBody()->write(' - pedidos');
        return $res;
    });
    
}
}