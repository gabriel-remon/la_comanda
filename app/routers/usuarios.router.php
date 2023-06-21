<?php
include_once __DIR__.'/../interfaces/IApiUsable.php';
include_once __DIR__.'/../models/Usuario.php';

class routerUsuarios implements IApiUsable
{ public function CargarUno($req, $res, $args)
    {
        
        $body = $req->getParsedBody();
        $new = new Usuario();
        $new->email = $body['email'];
        $new->password = $body['password'];
        $new->nombre = $body['nombre'];
        $new->fecha_nacimiento = new DateTime($body['fecha_nacimiento']);
        $new->sector = $body['sector'];
    
        $newId = $new->crearUsuario();
        if(isset($newId)){
            $res->getBody()->write('nuevo usuario creado, id: '.$newId);
        }else{
            $res->getBody()->write('no se pudo crear el usuario');
        }
        return $res;
    }

    public function TraerUno($req, $res, $args)
    {
        $body = $req->getParsedBody();

        $usuario = Usuario::validarUsuario($body['email'],$body['password']);
        //var_dump($usuario);
        if(isset($usuario)){
            $res->getBody()->write(json_encode($usuario));
        }else{
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
        $res->getBody()->write('usuario modificado con exito id: '.$idProduct);
        return $res;
    }

    public function BorrarUno($req, $res, $args)
    {
        $body = $req->getParsedBody();
        $eliminado = Usuario::borrarUsuario($body['email']);
        $res->getBody()->write($eliminado?'usuario eliminado':'no se pudo eliminar' );
        return $res;
    }
}
