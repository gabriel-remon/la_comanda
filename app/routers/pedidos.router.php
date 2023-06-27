<?php
require_once './interfaces/IApiUsable.php';

class routerPedidos implements IApiUsable
{
    public function CargarUno($req, $res, $args)
    {
        try {
            $body = $req->getParsedBody();

            $comanda = Mesa::obtenerMesa($body['numero_mesa'], true);
            if ($comanda) {
                $pedido = new Pedido($body['id_producto']);

                $pedido->altaPedido($comanda->id);
                $res= $res->withStatus(200);
                $res->getBody()->write(json_encode($pedido));
            }
        } catch (Exception $err) {
            $res= $res->withStatus(500);
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
        switch ($dataJwt->sector) {
                case 'cocina':
                case 'cervezeria':
                case 'postres':
                case 'barra':
                    $mesage =Pedido::pedidosEnPreparacion($dataJwt->id);
                    $statusMensage = 200;
                    break;
                case 'mesero':
                    
                    $mesage =Pedido::pedidosParaServir($dataJwt->id);
                    $statusMensage = 200;
                    break;
                case 'admin':
                    $mesage =Pedido::pedidosAdmin($dataJwt->id);
                    $statusMensage = 200;
                    break;
                case 'cliente':
                    $mesage =Pedido::pedidosCliente($dataJwt->id,$dataJwt->sector);
                    $statusMensage = 200;
                    break;
                default:
                    $mesage ='No cuenta con los permisos';
                    $statusMensage = 404;
                    break;
            
        }
        $res->getBody()->write(json_encode($mesage));
        $res= $res->withStatus($statusMensage);
        return $res;
    }

    public function ModificarUno($req, $res, $args)
    {
        $body = $req->getParsedBody();
        $new = new Pedido(
            isset($body['id_producto']) ? $body['id_producto'] : null,
            isset($body['orden_recibida']) ? $body['orden_recibida'] : null
        );

        $new->id_comanda = isset($body['id_comanda']) ? $body['id_comanda'] : null;
        $new->estado = isset($body['estado']) ? $body['estado'] : null;
        $new->tiempo_estimado = isset($body['tiempo_estimado']) ? $body['tiempo_estimado'] : null;
        $new->id_empleado = isset($body['id_empleado']) ? $body['id_empleado'] : null;
        $new->orden_entregada = isset($body['orden_entregada']) ? $body['orden_entregada'] : null;
        $new->id = $args['id'];
        $newId = Pedido::modificarPedido($new);
        $res->getBody()->write('mesa modificado con exito id: ' . $newId);
        return $res;
    }

    public function BorrarUno($req, $res, $args)
    {

        $idProduct = Mesa::borrarMesa($args['id']);
        $res->getBody()->write('pedido eliminado con exito id: ' . $idProduct);
        return $res;
    }
    public function preparar($req, $res, $args)
    {

        try {
            $body = $req->getParsedBody();
            $dataJwt = $req->getAttribute('jwt');
            $pedido = Pedido::obtenerPedido($body['id_pedido']);

            switch ($pedido->estado) {
                case 'pendiente':
                    Pedido::cargarEmpleado($body['id_pedido'], $body['tiempo_estimado'],$dataJwt->id, $dataJwt->sector);
                    $mesage ='Se tomo el pedido';
                    $statusMensage = 200;
                    break;
                case 'en preparacion':
                    Pedido::pedidoTerminado($body['id_pedido'], $dataJwt->id, $dataJwt->sector, isset($body['tiempo_entrega'])?$body['tiempo_entrega']:null);
                    $mesage ='se termino el pedio';
                    $statusMensage = 200;
                    break;
                case 'listo para servir':
                    Pedido::pedidoEntregado($body['id_pedido'], $dataJwt->sector);
                    Mesa::ClienteComiendo( $pedido->id_comanda); //cambia el estado de la comanda si el cliente tiene todos tus pedidos
                    $mesage ='Se entrego el pedido';
                    $statusMensage = 200;
                    break;
                default:
                    $mesage ='El pedido se encuentra terminado';
                    $statusMensage = 204;
                    break;
            }
            
            $res->getBody()->write($mesage);
            $res= $res->withStatus($statusMensage);
        } catch (Exception $e) {
            $res->getBody()->write($e->getMessage());
            $res=  $res->withStatus(500);
        }

        return $res;
    }
}
