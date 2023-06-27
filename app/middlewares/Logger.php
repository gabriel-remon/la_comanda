<?php

include_once __DIR__ . '/../utils/jwtController.php';

use Slim\Psr7\Response as ResponseMW;

class Logger
{

    public static function validarJWTUsuario($request, $handler)
    {

        $cookies = $request->getCookieParams();
        $token = $cookies['jwt'] ?? null;

        $error = null;
        $statusError = 500;
        $response = new ResponseMW();
        //var_dump($jwt);
        //var_dump('hola');
        //$authHeader = $request->getHeaderLine('Authorization');

        if (!$token) {
            $error = 'no hay un jwt guardado';
            $statusError = 401;
        }

        //list($token) = sscanf($authHeader, 'Bearer %s');
        try {
            $tokenVerificado =  ControlerJWT::VerificarToken($token);
            $request = $request->withAttribute('jwt', $tokenVerificado);
            $response = $handler->handle($request);
        } catch (\Exception $e) {

            $error = $e->getMessage();
            $statusError = 401;
        }

        if (isset($error)) {
            $view = $request->getAttribute('view');
            $response->getBody()->write($view->render('error/token.twig', ['data' => $error]));
            $response->withStatus($statusError);
        }

        return $response;
    }
    /*
    public static function validarJWTUsuario ($request, $handler) {
        
        $cookies = $request->getCookieParams();
        $token = $cookies['jwt'] ?? null;
        $error = null;
        $statusError = 500;
        //var_dump($jwt);
        //var_dump('hola');
        //$authHeader = $request->getHeaderLine('Authorization');
    
        if (!$token) {
            $response = new ResponseMW();
            $response->getBody()->write('no hay un jwt guardado');
            return $response->withStatus(401);
        }
    
        //list($token) = sscanf($authHeader, 'Bearer %s');
        try{
            $tokenVerificado =  ControlerJWT::VerificarToken($token);
            $request = $request->withAttribute('jwt', $tokenVerificado);
            $response = $handler->handle($request);
        }catch (\Exception $e) {
            
            
            $response = new ResponseMW();
            $response->getBody()->write('Invalid token');
            return $response->withStatus(401);
        }
    
        if(isset($error)){
            $view = $req->getAttribute('view');
            $res->getBody()->write($view->render('error.twig',['data'=>$error]));
            $res->withStatus($statusError);
        }

        return $response;
    }*/
    public static function validarMesero($request, $handler)
    {
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

    public static function validarAdmin($request, $handler)
    {
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

    public static function validarRoles($rolesPermitidos)
    {
        return function ($request, $handler) use ($rolesPermitidos) {
            $dataJwt = $request->getAttribute('jwt');
            $error = null;
            $statusError = 500;
            $response = new ResponseMW();

            if (!$dataJwt) {
                $error = 'no hay un jwt guardado';
                $statusError = 401;
            }

            if (!in_array($dataJwt->sector, $rolesPermitidos)) {
                $error = 'no cuenta con permisos para ingresar';
                $statusError = 404;
            }

            if (isset($error)) {
                $view = $request->getAttribute('view');
                $response->getBody()->write($view->render('error/token.twig', ['data' => $error]));
                $response->withStatus($statusError);
            } else {
                $response = $handler->handle($request);
            }


            return $response;
        };
    }
}
