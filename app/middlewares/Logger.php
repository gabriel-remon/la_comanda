<?php

include_once __DIR__.'/../utils/jwtController.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response as ResponseMW;
use Firebase\JWT\JWT;

class Logger
{
    public static function mostrarVerbo($request, $response, $next)
    {
        $retorno = $next($request, $response);
        return $retorno;
    }

    public static function verificarCredencial($req, $handler)
    {
        //$res = $handler->handle($req)
        $body = $req->getParsedBody();
        $res = new ResponseMW();

        if (isset($body['obj_json'])) {
            $data = json_decode($body['obj_json'], true);
            if(isset($data['usuario']) && isset($data['clave'])){
                $usuario = Usuario::obtenerUsuario($data['usuario'], $data['clave']);
                if($usuario){
                    $res = $handler->handle($req);
                    //$retorno = $next($req, $res);
                    return $res;
                }else{
                    $error=[
                        'message' =>'usuario invalid',
                        'status' =>'403'
                    ];
                }
            }{
                $error=[
                    'message' =>'se enviaron los datos de manera erronea',
                    'status' =>'403'
                ];
            }
        }else{
            $error=[
                'message' =>'no se encontro el parametro obj_json',
                'status' =>'403'
            ];
        }
        $res->getBody()->write(json_encode($error));
        return $res;
    }

    public static function LogOperacion($request, $response, $next)
    {
        $retorno = $next($request, $response);
        return $retorno;
    }


    public static function validarJWTUsuario ($request, $handler) {
        
        $authHeader = $request->getHeaderLine('Authorization');
    
        if (!$authHeader) {
            $response = new ResponseMW();
            $response->getBody()->write('no hay un jwt guardado');
            return $response->withStatus(401);
        }
    
        list($token) = sscanf($authHeader, 'Bearer %s');
        try{
            $tokenVerificado =  ControlerJWT::VerificarToken($token);
            $request = $request->withAttribute('jwt', $tokenVerificado);
            $response = $handler->handle($request);
        }catch (\Exception $e) {
            
            
            $response = new ResponseMW();
            $response->getBody()->write('Invalid token');
            return $response->withStatus(401);
        }
    
        return $response;
    }
    public static function validarMesero ($request, $handler) {
        $dataJwt = $request->getAttribute('jwt');
    
        if (!$dataJwt) {
            $response = new ResponseMW();
            $response->getBody()->write('no hay un jwt guardado');
            return $response->withStatus(401);
        }
        
        if ($dataJwt->sector !== 'mesero' && $dataJwt->sector !== 'admin') {
            $response = new ResponseMW();
            $response->getBody()->write('no cuenta con permisos para ingresar');
            return $response->withStatus(404);
        }
    
        return $handler->handle($request);
    }

    public static function validarAdmin ($request, $handler) {
        $dataJwt = $request->getAttribute('jwt');
    
        if (!$dataJwt) {
            $response = new ResponseMW();
            $response->getBody()->write('no hay un jwt guardado');
            return $response->withStatus(401);
        }
        
        if ($dataJwt->sector !== 'admin') {
            $response = new ResponseMW();
            $response->getBody()->write('no cuenta con permisos para ingresar');
            return $response->withStatus(404);
        }
    
        return $handler->handle($request);
    }

    public static function validarRoles($rolesPermitidos) {
        return function ($request, $handler) use ($rolesPermitidos) {
            $dataJwt = $request->getAttribute('jwt');
        
            if (!$dataJwt) {
                $response = new ResponseMW();
                $response->getBody()->write('no hay un jwt guardado');
                return $response->withStatus(401);
            }
            
            if (!in_array($dataJwt->sector, $rolesPermitidos)) {
                $response = new ResponseMW();
                $response->getBody()->write('no cuenta con permisos para ingresar');
                return $response->withStatus(404);
            }
        
            return $handler->handle($request);
        };
    }
    
}
