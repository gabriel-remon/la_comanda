<?php

class ViewRouter
{
    public function loginClientes($req, $res, $args)
    {
        $view = $req->getAttribute('view');

        $res->getBody()->write($view->render('usuarios/login.clientes.twig', []));

        return $res;
    }
    public function loginEmpleados($req, $res, $args)
    {
        $view = $req->getAttribute('view');
        $res->getBody()->write($view->render('usuarios/login.empleados.twig', []));
        return $res;
    }
    public function login($req, $res, $args)
    {
        $view = $req->getAttribute('view');

        $res->getBody()->write($view->render('usuarios/login.index.twig', []));
        // $res->getBody()->write($view->render('login.twig',[]));
        return $res;
    }
    public function usuarios($req, $res, $args)
    {
        $view = $req->getAttribute('view');

        $res->getBody()->write($view->render('login.twig', []));
        return $res;
    }
    public function logout($req, $res, $args)
    {
        $view = $req->getAttribute('view');

        $res->getBody()->write($view->render('usuarios/logout.twig', []));
        return $res;
    }
    public function singup($req, $res, $args)
    {
        $view = $req->getAttribute('view');

        $res->getBody()->write($view->render('usuarios/singup.twig', []));
        return $res;
    }
    public function CargarPedido($req, $res, $args)
    {

        $view = $req->getAttribute('view');
        $productos = Producto::obtenerTodos();


        $res->getBody()->write($view->render('pedidos/pedidos.cargar.twig', ['data' => $productos]));
        return $res;
    }
    public function cargarMesa($req, $res, $args)
    {

        $view = $req->getAttribute('view');
        $res->getBody()->write($view->render('mesas/cargar.twig', []));

        return $res;
    }
    public function pedidos($req, $handler)
    {

        $res = $handler->handle($req);
        $dataJwt = $req->getAttribute('jwt');
        if ($res->getStatusCode() == 200) {
            $view = $req->getAttribute('view');
            $body = $res->getBody();
            $pedidos = json_decode((string)$body, true);
            // Eliminar el body actual
            $body->rewind();
            $body->write('');
            //var_dump((string)$res->getBody());
            $res->getBody()->write($view->render('pedidos/pedidos.mostrar.twig', ['data' => $pedidos, 'sector' => $dataJwt->sector]));
        }
        return $res;
    }

    public function mostrarUsuarios($req, $handler)
    {
        $res = $handler->handle($req);
        if ($res->getStatusCode() == 200) {
            $view = $req->getAttribute('view');
            $body = $res->getBody();
            $usuarios = json_decode((string)$body, true);
            // Eliminar el body actual
            $body->rewind();
            $body->write('');
            $usuarios = array_map(function ($element) {
                $edad = floor((time() - strtotime($element['fecha_nacimiento'])) / 31556926);
                $element['edad'] = $edad;
                return ($element);
            }, $usuarios);

            $res->getBody()->write($view->render('usuarios/mostrar.todos.twig', ['data' => $usuarios]));
        }
        return $res;
    }
    public function mostrarProductos($req, $handler)
    {
        $res = $handler->handle($req);
        if ($res->getStatusCode() == 200) {
            $view = $req->getAttribute('view');
            $body = $res->getBody();
            $productos = json_decode((string)$body, true);
            // Eliminar el body actual
            $body->rewind();
            $body->write('');
            
            $res->getBody()->write($view->render('productos/mostrar.twig',['data'=>$productos]));
        
            //$res->getBody()->write($view->render('usuarios/mostrar.todos.twig', ['data' => $usuarios]));
        }
        return $res;
    }

    public function mesas($req, $handler)
    {
        $res = $handler->handle($req);
        if ($res->getStatusCode() == 200) {
            $dataJwt = $req->getAttribute('jwt');
            $view = $req->getAttribute('view');
            $body = $res->getBody();
            $mesas = json_decode((string)$body, true);
            if ($dataJwt->sector == 'mesero') {
                $mesas = array_filter($mesas, function ($mesa) {
                    if ($mesa['estado'] == 'con-cliente-esperando-pedido' || $mesa['estado'] == 'con-cliente-comiendo') {
                        return true;
                    }
                    return false;
                });
            }

            $body->rewind();
            $body->write('');
            $res->getBody()->write($view->render('mesas/mostrar.twig', ['data' => $mesas]));
        }
        return $res;
    }

    public function unaMesas($req, $handler)
    {
        $res = $handler->handle($req);
        if ($res->getStatusCode() == 200) {
            $dataJwt = $req->getAttribute('jwt');
            $view = $req->getAttribute('view');
            $body = $res->getBody();
            $cobrar = false;
            $mesa = json_decode((string)$body, true);

            if ($dataJwt->sector == 'admin' && $mesa['estado'] == 'con-cliente-pagando') {
                $cobrar = true;
            }

            
            $pedidos = Pedido::pedidosCliente($mesa['id'], null);

            $body->rewind();
            $body->write('');
            $suma = 0;
            foreach ($pedidos as $item) {
                if (isset($item['precio'])) {
                    $suma += $item['precio'];
                }
            }
            $mesa['total'] = $suma;
            $res->getBody()->write($view->render('mesas/mostrar.una.twig', ['data' => $mesa, 'pedidos' => $pedidos, 'cobrar' => $cobrar]));
        }
        return $res;
    }
}
