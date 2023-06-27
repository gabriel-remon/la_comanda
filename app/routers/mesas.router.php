<?php
include_once __DIR__ . '/../interfaces/IApiUsable.php';
include_once __DIR__ . '/../models/Mesa.php';

class routerMesas implements IApiUsable
{
    public function CargarUno($req, $res, $args)
    {

        $body = $req->getParsedBody();
        $new = new Mesa($body['nombre_cliente'], $body['numero_mesa']);


        $newId = $new->altaMesa();
        if ($newId > -1) {
            $res->getBody()->write('nueva comanda creada, id: ' . $newId . 'password: ' . $new->password);
        } else {
            $res->getBody()->write('no se creo la comanda');
            $res = $res->withStatus(404);
        }
        return $res;
    }
    public function CargarFoto($req, $res, $args)
    {
        $mesage = null;
        $statusCode = 500;
        try {
            $body = $req->getParsedBody();
            $foto = $req->getUploadedFiles();
            if(isset($foto['imagen_mesa'])){
                $foto= $req->getUploadedFiles()['imagen_mesa'];
            
                if ($foto?->getError() === UPLOAD_ERR_OK &&  isset($body['idComanda'])) {
                    $directorioDestino = 'public/imagenes/' . $body['idComanda'] . '.jpg';
                    $foto->moveTo(__DIR__.'/../'.$directorioDestino);
    
                    if (Mesa::cargarFoto($body['idComanda'], $directorioDestino)) {
                        $mesage = 'foto guardada';
                        $statusCode = 200;
                    } else {
                        $mesage = 'error al guardar la foto';
                        $statusCode = 500;
                    }
                } else {
                    $mesage = 'error al enviar la foto, posiblemente no existe idComanda';
                    $statusCode = 404;
                }
            }else{
                $mesage = 'no existe el parametro imagen_mesa';
                $statusCode = 404;
                
            }
            } catch (Exception $e) {
                $mesage = $e->getMessage();
                $statusCode = 500;
            }
            

        $res->getBody()->write($mesage);
        $res = $res->withStatus($statusCode);

        return $res;
    }

    public function TraerUno($req, $res, $args)
    {
        $productos = Mesa::obtenerMesa($args['id']);

        $res->getBody()->write(json_encode($productos));
        return $res;
    }
    public function loginCliente($req, $res, $args)
    {
        $body = $req->getParsedBody();

        $mesa = Mesa::validarMesa($body['numero_mesa'], $body['password']);

        if (isset($mesa)) {
            $token = [
                'id' => $mesa->id,
                'sector' => 'cliente',
                'numero_mesa' => $mesa->numero_mesa
            ];
            $jwt = ControlerJWT::CrearToken($token);
            $res = $res->withHeader('Set-Cookie', 'jwt=' . $jwt . '; path=/; HttpOnly; Secure; SameSite=Strict');
            $res->getBody()->write('acceso consedido');
            $res = $res->withStatus(200);
        } else {
            $res = $res->withStatus(400);
            $res->getBody()->write('usuario o password incorrectos');
        }
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
        $new->password = isset($body['password']) ? $body['password'] : null;
        $new->estado = isset($body['estado']) ? $body['estado'] : null;
        $new->id_encuesta = isset($body['id_encuesta']) ? $body['id_encuesta'] : null;
        $new->id = $args['id'];

        $newId = Mesa::modificartMesa($new);
        $res->getBody()->write('mesa modificado con exito id: ' . $newId);
        return $res;
    }

    public function BorrarUno($req, $res, $args)
    {
        $idProduct = Mesa::borrarMesa($args['id']);
        $res->getBody()->write('mesa eliminado con exito id: ' . $idProduct);
        return $res;
    }
    public function listoParaPagar($req, $res, $args)
    {
        try {
            $dataJwt = $req->getAttribute('jwt');
            $mesage = null;
            $statusMensage = 500;

            if ($dataJwt->sector == 'admin' || $dataJwt->sector == 'mesero') {
                if (Mesa::ListoParaPagar($args['id'])) {
                    $mesage = 'mesa lista para pagar';
                    $statusMensage = 200;
                } else {
                    $mesage = 'no se pudo realizar la accion';
                    $statusMensage = 500;
                }
            } else {
                $mesage = 'error solo un mesero o un admin puede hacer esta accion';
                $statusMensage = 500;
            }

            $res->getBody()->write($mesage);
            $res = $res->withStatus($statusMensage);
        } catch (Exception $e) {
            $res->getBody()->write($e->getMessage());
            $res =  $res->withStatus(500);
        }

        return $res;
    }
    public function cobrar($req, $res, $args)
    {
        try {
            $dataJwt = $req->getAttribute('jwt');
            $mesage = null;
            $statusMensage = 500;

            if ($dataJwt->sector == 'admin') {
                if (Mesa::cobrar($args['id'])) {
                    $mesage = 'mesa cobrada';
                    $statusMensage = 200;
                } else {
                    $mesage = 'no se pudo realizar la accion';
                    $statusMensage = 500;
                }
            } else {
                $mesage = 'error solo un admin puede hacer esta accion';
                $statusMensage = 500;
            }

            $res->getBody()->write($mesage);
            $res = $res->withStatus($statusMensage);
        } catch (Exception $e) {
            $res->getBody()->write($e->getMessage());
            $res =  $res->withStatus(500);
        }

        return $res;
    }
}
