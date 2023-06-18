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
}
