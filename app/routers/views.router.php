<?php

class ViewRouter{
    public function loginClientes($req, $res, $args)
    {
        $view = $req->getAttribute('view');
        $res->getBody()->write($view->render('login.clientes.twig',[]));
        return $res;
    }
    public function loginEmpleados($req, $res, $args)
    {
        $view = $req->getAttribute('view');
        $res->getBody()->write($view->render('login.empleados.twig',[]));
        return $res;
    }
    public function login($req, $res, $args)
    {
        $view = $req->getAttribute('view');
        $res->getBody()->write($view->render('login.twig',[]));
        return $res;
    }
    public function usuarios($req, $res, $args)
    {
        $view = $req->getAttribute('view');
        
        $res->getBody()->write($view->render('login.twig',[]));
        return $res;
    }
    public function logout($req, $res, $args)
    {
        $view = $req->getAttribute('view');
        
        $res->getBody()->write($view->render('logout.twig',[]));
        return $res;
    }
    public function pedidos($req, $handler)
    {
        $res = $handler->handle($req);
        if($res->getStatusCode() == 200){
            $view = $req->getAttribute('view');
            $body = $res->getBody();
            $pedidos = json_decode((string)$body,true);
            // Eliminar el body actual
            $body->rewind();
            $body->write('');
            //var_dump((string)$res->getBody());
            $res->getBody()->write($view->render('pedidos.twig',['data'=>$pedidos]));
        }
        return $res;
    }
    public function mostrarUsuarios($req, $handler)
    {
        $res = $handler->handle($req);
        if($res->getStatusCode() == 200){
            $view = $req->getAttribute('view');
            $body = $res->getBody();
            $usuarios = json_decode((string)$body,true);
            // Eliminar el body actual
            $body->rewind();
            $body->write('');
            $usuarios =array_map(function($element){
                $edad = floor((time() - strtotime($element['fecha_nacimiento'])) / 31556926);
                $element['edad']= $edad;
                return ($element);
            },$usuarios);

            $res->getBody()->write($view->render('usuarios.twig',['data'=>$usuarios]));
        }
        return $res;
    }
}