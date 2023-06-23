<?php

use Slim\Psr7\Response as ResponseMW;

class validarFormato
{

    public static function sector($req, $handler)
    {
        $res = new ResponseMW();
        $body = $req->getParsedBody();

        if (isset($body['sector'])) {
            $body['sector'] = strtolower($body['sector']);
            if (in_array($body['sector'], explode(',', $_ENV['SECTORES']))) {
                $newReq = $req->withParsedBody($body);
                $res = $handler->handle($newReq);
            } else {
                $res->getBody()->write('el sector ingresado no es valido');
                $res->statusCode = 404;
            }
        } else {
            $res->getBody()->write('no se encontro el parametro sector en el body');
            $res->statusCode = 404;
        }

        return $res;
    }



    public static function producto($req, $handler)
    {

        $res = new ResponseMW();
        $body = $req->getParsedBody();

        if (isset($body['descripcion']) && isset($body['sector']) && isset($body['precio']) && isset($body['estado'])) {
            $res = $handler->handle($req);
        } else {
            $res->getBody()->write('no se encontraron los parametros');
            $res->statusCode = 404;
        }
        return $res;
    }
    public static function usuario($req, $handler)
    {

        $res = new ResponseMW();
        $body = $req->getParsedBody();

        if (
            isset($body['email']) &&
            isset($body['password']) &&
            isset($body['nombre']) &&
            isset($body['fecha_nacimiento']) &&
            isset($body['sector']) &&
            isset($body['estado'])
        ) {
            $res = $handler->handle($req);
        } else {
            $res->getBody()->write('no se encontraron los parametros');
            $res->statusCode = 404;
        }
        return $res;
    }


    public static function mesa($req, $handler)
    {

        $res = new ResponseMW();
        $body = $req->getParsedBody();

        if (isset($body['nombre_cliente']) &&
            isset($body['numero_mesa'])) {

            if (intval($body['numero_mesa']) > 0 && 
                intval($body['numero_mesa']) <= intval($_ENV['CANT_MESAS'])) {
                $res = $handler->handle($req);
            } else {
                $res->getBody()->write('numero de mesa invalido');
                $res->statusCode = 404;
            }
        } else {
            $res->getBody()->write('no se encontraron los parametros');
            $res->statusCode = 404;
        }
        return $res;
    }

    public static function altaPedido($req, $handler)
    {
        $res = new ResponseMW();
        $body = $req->getParsedBody();
        
        if(isset($body['numero_mesa'])){
            $comanda = Mesa::obtenerMesa($body['numero_mesa'],true);
            if($comanda){
                
                if(isset($body['id_producto']) && Producto::exist($body['id_producto'])){
                    $res = $handler->handle($req);
                }else{
                    $res->statusCode =404;
                    $res->getBody()->write('el producto no existe');
                    return $res;
                }
            }else{
                $res->statusCode =404;
                $res->getBody()->write('La mesa ingresada no se encuentra con una comanda activa');
            }
        }else{
            $res->statusCode = 404;
            $res->getBody()->write('no se encontro el numero de mesa como parametro "numero_mesa"');
        }

        return $res;
    }

    public static function prepararPedido($req, $handler)
    {
        $res = new ResponseMW();
        $body = $req->getParsedBody();

        if(!isset($body['id_pedido'])){
            $error = 'No se encontro el parametro "id_pedido"';
            $statusError = 404;
            $res->getBody()->write($error);
            $res->withStatus($statusError);
            return $res;
        }

        $pedido = Pedido::obtenerPedido($body['id_pedido']);
            
        if(!isset($pedido)){
            $error = 'El pedido no esta guardado en la lista de pedidos';
            $statusError = 500;
            $res->getBody()->write($error);
            $res->withStatus($statusError);
            return $res;
        }

        if($pedido->estado == 'pendiente' && !isset($body['tiempo_estimado'])){
            $error = 'No se encontro el parametro "tiempo_estimado"';
            $statusError = 404;
            $res->getBody()->write($error);
            $res->withStatus($statusError);
            return $res;
        }
        
        
            
            $res = $handler->handle($req);
        
        return $res;
    }
}
