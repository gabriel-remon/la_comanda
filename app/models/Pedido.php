<?php

include_once __DIR__."/../utils/AccesoDatos.php";
include_once __DIR__."/Usuario.php";

class Pedido
{
    public $id;
    public $id_comanda;
    public $id_producto;
    public $estado;
    public $tiempo_estimado;
    public $id_empleado;
    public $orden_recibida;
    public $orden_entregada;

    function __construct($id_producto, $orden_recibida = null)
    {
        $this->id_producto = $id_producto;
        $this->orden_recibida = $orden_recibida ? date($orden_recibida) : date("Y-m-d H:i:s");
    }

    public function setIdEmpleado($id_empleado){
        
    }
    public function setOdenEtregada($Orden_terminada){

    }
    public function setTiempoEstimado($tiempo_estimado){

    }
    public function setEstado($estado){

    }


    /**
     * retorna el id guarado en la base de datos
     *
     * @param [type] $id_comanda
     * @return void
     */
    public function altaPedido($id_comanda)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO ".$_ENV['BD_PEDIDOS']." (id_comanda, id_producto,orden_recibida, estado) VALUES 
                                                        VALUES (:id_comanda, :id_producto,:orden_recibida, :estado) ");
        $consulta->bindValue(':id_comanda', $id_comanda);
        $consulta->bindValue(':id_producto', $this->id_producto);;
        $consulta->bindValue(':orden_recibida', $this->orden_recibida);
        $consulta->bindValue(':estado', 'pendiente');
        if ($consulta->execute()) {
            $retorno = $objAccesoDatos->obtenerUltimoId();
        } else {
            $retorno = null;
        }

        return  $retorno;
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ".$_ENV['BD_PEDIDOS']);
        $consulta->execute();
        $data = $consulta->fetchAll(PDO::FETCH_CLASS, "Pedido");

        return $data;
    }

    public static function obtenerPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ".$_ENV['BD_PEDIDOS']." WHERE id = :id");
        $consulta->bindValue(':id', $id,);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function modificarPedido($newPedido)
    {
        $retotno = false;
        $pedidoAnterior = Pedido::obtenerPedido($newPedido->id);
        if ($pedidoAnterior) {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("UPDATE ".$_ENV['BD_PEDIDOS']."
                                                         SET  id_comanda = :id_comanda , 
                                                         id_producto = :id_producto, 
                                                         orden_recibida = :orden_recibida, 
                                                         estado = :estado,
                                                         tiempo_estimado = :tiempo_estimado,
                                                         id_empleado = :id_empleado,
                                                         orden_recibida = :orden_recibida,
                                                         orden_entregada = :orden_entregada
                                                         WHERE id = :id");    

            $consulta->bindValue(':id_comanda',     $newPedido->id_comanda      ?$newPedido->id_producto    :$pedidoAnterior->id_producto);
            $consulta->bindValue(':id_producto',    $newPedido->id_producto     ?$newPedido->id_producto    :$pedidoAnterior->id_producto);;
            $consulta->bindValue(':estado',         $newPedido->estado          ?$newPedido->estado         :$pedidoAnterior->estado);
            $consulta->bindValue(':tiempo_estimado',$newPedido->tiempo_estimado ?$newPedido->tiempo_estimado:$pedidoAnterior->tiempo_estimado);
            $consulta->bindValue(':id_empleado',    $newPedido->id_empleado     ?$newPedido->id_empleado    :$pedidoAnterior->id_empleado);
            $consulta->bindValue(':orden_recibida', $newPedido->orden_recibida  ?$newPedido->orden_recibida :$pedidoAnterior->orden_recibida);
            $consulta->bindValue(':orden_entregada',$newPedido->orden_entregada ?$newPedido->orden_entregada:$pedidoAnterior->orden_entregada);
            $consulta->bindValue(':id', $newPedido->id);
            
            $retotno = $consulta->execute();
        }

        return $retotno ;
    }

    public static function borrarPedido($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE ".$_ENV['BD_PEDIDOS']." SET estado = :estado WHERE id = :id");
        $consulta->bindValue(':estado', "anulado");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }




}
