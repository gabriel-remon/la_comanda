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
}