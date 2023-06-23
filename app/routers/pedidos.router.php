<?php
require_once './interfaces/IApiUsable.php';

class routerPedidos implements IApiUsable
{
    public function CargarUno($req, $res, $args)
    {
        try {
            $body = $req->getParsedBody();

            $comanda = Mesa::obtenerMesa($body['numero_mesa'], true);
            if($comanda){
                $pedido = new Pedido($body['id_producto']);
                
                $pedido->altaPedido($comanda->id);
                $res->withStatus(200);
                $res->getBody()->write(json_encode($pedido));
            }
        } catch (Exception $err) {
            $res->withStatus(500);
            $res->getBody()->write(json_encode($err));
        }
        return $res;
    }

    public function TraerUno($req, $res, $args)
    {
        $productos = Pedido::obtenerPedido($args['id']);

        $res->getBody()->write(json_encode($productos));
        return $res;
    }

    public function TraerTodos($req, $res, $args)
    {
        $dataJwt = $req->getAttribute('jwt');
        if($dataJwt->sector == 'cliente'){
            $productos = Pedido::obtenerPedidoComanda($dataJwt->id);
        }else{
            $productos = Pedido::obtenerTodosPorSector($dataJwt->sector);
        }
        $res->getBody()->write(json_encode($productos));
        return $res;
    }

    public function ModificarUno($req, $res, $args)
    {
        $body = $req->getParsedBody();
        $new = new Pedido(isset($body['id_producto'])?$body['id_producto']:null
                        , isset($body['orden_recibida'])?$body['orden_recibida']:null);

        $new->id_comanda = isset($body['id_comanda'])?$body['id_comanda']:null;
        $new->estado = isset($body['estado'])?$body['estado']:null;
        $new->tiempo_estimado = isset($body['tiempo_estimado'])?$body['tiempo_estimado']:null;
        $new->id_empleado = isset($body['id_empleado'])?$body['id_empleado']:null;
        $new->orden_entregada = isset($body['orden_entregada'])?$body['orden_entregada']:null;
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
    public function preparar($req, $res, $args)
    {
        
        try{
            $body = $req->getParsedBody();
            $dataJwt = $req->getAttribute('jwt');
            $pedido = Pedido::obtenerPedido($body['id_pedido']);
            if ($pedido->estado == 'pendiente')
                Pedido::cargarEmpleado($body['id_pedido'],$dataJwt->id,$body['tiempo_estimado'],$dataJwt->sector);
            else 
                Pedido::cambioEstado($body['id_pedido']);
            

            //if ($pedido->estado == 'pendiente' && $dataJwt->sector ==)

            $res->getBody()->write('pedido modificado');
            $res->withStatus(200);
        }catch(Exception $e){
            $res->getBody()->write($e->getMessage());
            $res->withStatus(500);
        }

        return $res;
    }
}
