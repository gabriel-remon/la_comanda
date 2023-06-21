<?php
include_once __DIR__.'/../interfaces/IApiUsable.php';
include_once __DIR__.'/../models/Mesa.php';

class routerMesas implements IApiUsable
{
    public function CargarUno($req, $res, $args)
    {
        
        $body = $req->getParsedBody();
        $new = new Mesa($body['nombre_cliente'], $body['numero_mesa']);
    
        $newId = $new->altaMesa();
        if( $newId > -1 ){
            $res->getBody()->write('nueva comanda creada, id: '.$newId.'password: '.$new->password);
        }else{
            $res->getBody()->write('no se creo la comanda');
            
        }
        return $res;
    }

    public function TraerUno($req, $res, $args)
    {
        $productos = Mesa::obtenerMesa($args['id']);

        $res->getBody()->write(json_encode($productos));
        return $res;
    }

    public function TraerTodos($req, $res, $args)
    {
        $productos = Mesa::obtenerTodos();

        $res->getBody()->write(json_encode($productos));
        return $res;
    }
    
    public function ModificarUno($req, $res, $args)
    {
        $body = $req->getParsedBody();
        $new = new Mesa($body['nombre_cliente'], $body['numero_mesa']);
        $new->password = isset($body['password'])?$body['password']:null;
        $new->estado = isset($body['estado'])?$body['estado']:null;
        $new->id_encuesta = isset($body['id_encuesta'])?$body['id_encuesta']:null;
        $new->id = $args['id'];

        $newId = Mesa::modificartMesa($new);
        $res->getBody()->write('mesa modificado con exito id: '.$newId);
        return $res;
    }

    public function BorrarUno($req, $res, $args)
    {
        $idProduct = Mesa::borrarMesa($args['id']);
        $res->getBody()->write('mesa eliminado con exito id: '.$idProduct);
        return $res;
    }
}
