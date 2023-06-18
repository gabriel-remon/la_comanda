<?php
require_once './interfaces/IApiUsable.php';

class routerPedidos implements IApiUsable
{  
    public function CargarUno($req, $res, $args)
    {
        $res->getBody()->write('CargarUno');
        return $res;
    }

    public function TraerUno($req, $res, $args)
    {
        $res->getBody()->write('TraerUno');
        return $res;
    }

    public function TraerTodos($req, $res, $args)
    {
        $res->getBody()->write('TraerTodos');
        return $res;
    }
    
    public function ModificarUno($req, $res, $args)
    {
        $res->getBody()->write('ModificarUno');
        return $res;
    }

    public function BorrarUno($req, $res, $args)
    {
        $res->getBody()->write('BorrarUno');
        return $res;
    }
}
