<?php
include_once __DIR__ . '/../interfaces/IApiUsable.php';
include_once __DIR__ . '/../models/Usuario.php';
include_once __DIR__ . '/../utils/jwtController.php';

class routerUsuarios implements IApiUsable
{
    public function CargarUno($req, $res, $args)
    {

        $body = $req->getParsedBody();
        $new = new Usuario();
        $new->email = $body['email'];
        $new->password = $body['password'];
        $new->nombre = $body['nombre'];
        $new->fecha_nacimiento = new DateTime($body['fecha_nacimiento']);
        $new->sector = $body['sector'];

        $newId = $new->crearUsuario();
        if (isset($newId)) {
            $res->getBody()->write('nuevo usuario creado, id: ' . $newId);
            $res = $res->withStatus(200);
        } else {
            $res->getBody()->write('no se pudo crear el usuario');
            $res = $res->withStatus(404);
        }
        return $res;
    }

    public function TraerUno($req, $res, $args)
    {
        $body = $req->getParsedBody();

        $usuario = Usuario::validarUsuario($body['email'], $body['password']);
        //var_dump($usuario);
        if (isset($usuario) && $usuario->estado) {
            $token = [
                'id' => $usuario->id,
                'sector' => $usuario->sector,
                'email' => $usuario->email
            ];
            $jwt = ControlerJWT::CrearToken($token);
            setcookie('jwt', $jwt, [
                'expires' => time() + 86400,
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'],
                'secure' => true, // asegura que las cookies sean enviadas solo sobre HTTPS
                'httponly' => true, // las cookies solo se pueden acceder a través del protocolo HTTP, y no mediante scripts como JavaScript
                'samesite' => 'Strict', // previene ataques de tipo CSRF
              ]);
            
           // $res = $res->withHeader('Set-Cookie', 'jwt=' . $jwt . '; path=/; HttpOnly; Secure; SameSite=Strict');
            $res->getBody()->write("Bienvenido " . $usuario->nombre);
            $res = $res->withStatus(200);
        } else {
            $res = $res->withStatus(400);
            $res->getBody()->write('usuario o password incorrectos');
        }
        return $res;
    }


    public function TraerTodos($req, $res, $args)
    {
        $usuarios = Usuario::obtenerTodos();

        $res->getBody()->write(json_encode($usuarios));
        return $res;
    }

    public function ModificarUno($req, $res, $args)
    {
        $body = $req->getParsedBody();
        $new = new Usuario();

        $new->email = $body['email'];
        $new->password = $body['password'];
        $new->nombre = $body['nombre'];
        $new->fecha_nacimiento = new DateTime($body['fecha_nacimiento']);
        $new->sector = $body['sector'];
        $new->estado = $body['estado'];

        $idProduct = Usuario::modificarUsuario($new);
        $res->getBody()->write('usuario modificado con exito id: ' . $idProduct);
        return $res;
    }

    public function BorrarUno($req, $res, $args)
    {
        $body = $req->getParsedBody();
        $eliminado = Usuario::borrarUsuario($body['email']);
        $res->getBody()->write($eliminado ? 'usuario eliminado' : 'no se pudo eliminar');
        return $res;
    }
    public function logout($req, $res, $args)
    {
        try {
            $res = $res->withHeader('Set-Cookie', 'jwt=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; HttpOnly; Secure; SameSite=Strict');
            $res->getBody()->write('Usuario deslogado');
            $res = $res->withStatus(200);
        } catch (Exception $e) {
            $res->getBody()->write('Error: ' . $e->getMessage());
            $res = $res->withStatus(500);
        }
        return $res;
    }
}
