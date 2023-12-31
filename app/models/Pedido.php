<?php

include_once __DIR__ . "/../utils/AccesoDatos.php";
include_once __DIR__ . "/Usuario.php";
include_once __DIR__ . "/Producto.php";

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

    function __construct($id_producto = null, $orden_recibida = null)
    {
        if (isset($id_producto) && !Producto::exist($id_producto))
            throw new Exception("El producto ingresado no se encuentra en la base de datos");

        $this->id_producto = $id_producto;
        $this->orden_recibida = $orden_recibida ? date($orden_recibida) : date("Y-m-d H:i:s");
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
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO " . $_ENV['BD_PEDIDOS'] . " (id_comanda, id_producto,orden_recibida, estado)  
                                                        VALUES (:id_comanda, :id_producto,:orden_recibida, :estado) ");
        $consulta->bindValue(':id_comanda', $id_comanda);
        $consulta->bindValue(':id_producto', $this->id_producto);
        $consulta->bindValue(':orden_recibida', $this->orden_recibida);
        $consulta->bindValue(':estado', 'pendiente');
        if ($consulta->execute()) {
            $retorno = $objAccesoDatos->obtenerUltimoId();
            $this->id = $retorno;
            $this->id_comanda = $id_comanda;
            $this->estado = 'pendiente';
        } else {
            $retorno = null;
        }

        return  $retorno;
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM " . $_ENV['BD_PEDIDOS']);
        $consulta->execute();
        $data = $consulta->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }
    public static function obtenerTodosPorSector($sector, $pendiente = false)
    {

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        if ($sector == 'admin') {
            $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidos.*," . $_ENV['BD_PRODUCTOS'] . ".sector," . $_ENV['BD_PRODUCTOS'] . ".descripcion FROM " . $_ENV['BD_PEDIDOS'] .
                " JOIN " . $_ENV['BD_PRODUCTOS'] . " ON " . $_ENV['BD_PEDIDOS'] . ".id_producto = " . $_ENV['BD_PRODUCTOS'] . ".id ");
        } else if ($pendiente) {
            //$consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ".$_ENV['BD_PEDIDOS']." where sector = :sector and estado = pendiente");
            $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidos.*," . $_ENV['BD_PRODUCTOS'] . ".sector," . $_ENV['BD_PRODUCTOS'] . ".descripcion FROM " . $_ENV['BD_PEDIDOS'] .
                " JOIN " . $_ENV['BD_PRODUCTOS'] . " ON " . $_ENV['BD_PEDIDOS'] . ".id_producto = " . $_ENV['BD_PRODUCTOS'] . ".id 
            WHERE " . $_ENV['BD_PRODUCTOS'] . ".sector = :sector AND " . $_ENV['BD_PEDIDOS'] . ".estado = :estado");

            $consulta->bindValue(':sector', $sector);
            $consulta->bindValue(':estado', 'pendiente');
        } else {

            $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidos.*, " . $_ENV['BD_PRODUCTOS'] . ".sector, " . $_ENV['BD_PRODUCTOS'] . ".descripcion 
            FROM " . $_ENV['BD_PEDIDOS'] . "
            JOIN " . $_ENV['BD_PRODUCTOS'] . " ON " . $_ENV['BD_PEDIDOS'] . ".id_producto = " . $_ENV['BD_PRODUCTOS'] . ".id 
            WHERE " . $_ENV['BD_PRODUCTOS'] . ".sector = :sector");

            $consulta->bindValue(':sector', $sector);
        }

        $consulta->execute();
        $data = $consulta->fetchAll(PDO::FETCH_CLASS);

        return $data;
    }
    public static function obtenerPedidoComanda($id_comanda)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM " . $_ENV['BD_PEDIDOS'] . " WHERE id_comanda = :id_comanda");
        $consulta->bindValue(':id_comanda', $id_comanda,);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }


    public static function obtenerPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM " . $_ENV['BD_PEDIDOS'] . " WHERE id = :id");
        $consulta->bindValue(':id', $id,);
        $consulta->execute();

        $pedido = $consulta->fetchObject();
        $newPedido = new Pedido($pedido->id_producto, $pedido->orden_recibida);
        $newPedido->id = $pedido->id;
        $newPedido->id_comanda = $pedido->id_comanda;
        $newPedido->estado = $pedido->estado;
        $newPedido->tiempo_estimado = $pedido->tiempo_estimado;
        $newPedido->orden_entregada = $pedido->orden_entregada;
        $newPedido->id_empleado = $pedido->id_empleado;
        //var_dump($newPedido);
        return $newPedido;
    }

    public static function modificarPedido($newPedido)
    {
        $retotno = false;
        $pedidoAnterior = Pedido::obtenerPedido($newPedido->id);
        if ($pedidoAnterior) {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("UPDATE " . $_ENV['BD_PEDIDOS'] . "
                                                         SET  id_comanda = :id_comanda , 
                                                         id_producto = :id_producto,  
                                                         estado = :estado,
                                                         tiempo_estimado = :tiempo_estimado,
                                                         id_empleado = :id_empleado,
                                                         orden_recibida = :orden_recibida,
                                                         orden_entregada = :orden_entregada
                                                         WHERE id = :id");

            $consulta->bindValue(':id_comanda',     $newPedido->id_comanda      ? $newPedido->id_comanda    : $pedidoAnterior->id_comanda );
            $consulta->bindValue(':id_producto',    $newPedido->id_producto     ? $newPedido->id_producto    : $pedidoAnterior->id_producto);;
            $consulta->bindValue(':estado',         $newPedido->estado          ? $newPedido->estado         : $pedidoAnterior->estado);
            $consulta->bindValue(':tiempo_estimado', $newPedido->tiempo_estimado ? $newPedido->tiempo_estimado : $pedidoAnterior->tiempo_estimado);
            $consulta->bindValue(':id_empleado',    $newPedido->id_empleado     ? $newPedido->id_empleado    : $pedidoAnterior->id_empleado);
            $consulta->bindValue(':orden_recibida', $newPedido->orden_recibida  ? date($newPedido->orden_recibida) : $pedidoAnterior->orden_recibida);
            $consulta->bindValue(':orden_entregada', $newPedido->orden_entregada ?date( $newPedido->orden_entregada ): $pedidoAnterior->orden_entregada);
            $consulta->bindValue(':id', $newPedido->id,pdo::PARAM_INT);

            $retotno = $consulta->execute();
        }

        return $retotno;
    }

    public static function cambioEstado($id)
    {
        $proximoEstado = '';
        $pedido = Pedido::obtenerPedido($id);
        $objAccesoDato = AccesoDatos::obtenerInstancia();

        if ($pedido->estado == 'pendiente') $proximoEstado = 'en preparacion';
        if ($pedido->estado == 'en preparacion') $proximoEstado = 'listo para servir';
        if ($pedido->estado == 'listo para servir') $proximoEstado = 'entregado';

        if ($proximoEstado == 'listo para servir') {
            $consulta = $objAccesoDato->prepararConsulta("UPDATE " . $_ENV['BD_PEDIDOS'] . " 
                                                            SET estado = :estado , orden_entregada = :orden_entregada 
                                                            WHERE id = :id");
            $consulta->bindValue(':orden_entregada', date("Y-m-d H:i:s"));
        } else {
            $consulta = $objAccesoDato->prepararConsulta("UPDATE " . $_ENV['BD_PEDIDOS'] . " SET estado = :estado WHERE id = :id");
        }

        $consulta->bindValue(':estado', $proximoEstado);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function cargarEmpleado($id, $tiempo_estimado, $id_empleado, $sector)
    {
        $pedido = Pedido::obtenerPedido($id);
        $producto = Producto::obtenerProducto($pedido->id_producto);
        //var_dump($producto);
        if ($producto && $producto->sector == $sector && $pedido->estado == 'pendiente') {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE " . $_ENV['BD_PEDIDOS'] . " 
                                                            SET id_empleado = :id_empleado, tiempo_estimado = :tiempo_estimado , estado = :estado 
                                                            WHERE id = :id");
            $consulta->bindValue(':id_empleado', $id_empleado);
            $consulta->bindValue(':tiempo_estimado', $tiempo_estimado);
            $consulta->bindValue(':estado', 'en preparacion');
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->execute();
        } else {
            throw new Exception("sector invalido, no coincide con el pedido guardado");
        }
    }
    // se puede cambiar id_empleado y sector por el jwt guardado
    public static function pedidoTerminado($id, $id_empleado, $sector, $tiempo_entrega = null)
    {
        $pedido = Pedido::obtenerPedido($id);
        $producto = Producto::obtenerProducto($pedido->id_producto);
        //var_dump($producto);
        if ($producto && $producto->sector == $sector && $pedido->id_empleado == $id_empleado && $pedido->estado == 'en preparacion') {

            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE " . $_ENV['BD_PEDIDOS'] . " 
                                                            SET orden_entregada = :orden_entregada , estado = :estado 
                                                            WHERE id = :id");
            $consulta->bindValue(':orden_entregada', date(isset($tiempo_entrega) ? $tiempo_entrega : "Y-m-d H:i:s"));
            $consulta->bindValue(':estado', 'listo para servir');
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->execute();
        } else {
            throw new Exception("sector invalido, no coincide con el pedido guardado");
        }
    }
    public static function pedidoEntregado($id, $sector)
    {
        $pedido = Pedido::obtenerPedido($id);
        //var_dump($producto);
        if ($pedido && $sector == 'mesero' && $pedido->estado == 'listo para servir') {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE " . $_ENV['BD_PEDIDOS'] . " 
                                                            SET estado = :estado 
                                                            WHERE id = :id");
            $consulta->bindValue(':estado', 'entregado');
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->execute();
        } else {
            throw new Exception("sector invalido, no coincide con el pedido guardado");
        }
    }

    public static function borrarPedido($id, $sector)
    {
        $pedido = Pedido::obtenerPedido($id);
        //var_dump($producto);
        if ($sector == 'admin') {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE " . $_ENV['BD_PEDIDOS'] . " SET estado = :estado WHERE id = :id");
            $consulta->bindValue(':estado', "anulado");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->execute();
        } else {
            throw new Exception("Solo los admin pueden borrar un pedido");
        }
    }

    public static function pedidosParaServir($id_empleado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $sector = Usuario::obtenerSector($id_empleado);
        $data = [];
        if ($sector == 'mesero') {

            $consulta = $objAccesoDatos->prepararConsulta("SELECT " . $_ENV['BD_PEDIDOS'] . ".*, " . $_ENV['BD_PRODUCTOS'] . ".sector, " . $_ENV['BD_PRODUCTOS'] . ".descripcion, " . $_ENV['BD_MESAS'] . ".numero_mesa
    FROM " . $_ENV['BD_PEDIDOS'] . "
    JOIN " . $_ENV['BD_PRODUCTOS'] . " ON " . $_ENV['BD_PEDIDOS'] . ".id_producto = " . $_ENV['BD_PRODUCTOS'] . ".id
    JOIN " . $_ENV['BD_MESAS'] . " ON " . $_ENV['BD_PEDIDOS'] . ".id_comanda = " . $_ENV['BD_MESAS'] . ".id
    WHERE " . $_ENV['BD_PEDIDOS'] . ".estado = :estado");

            //$consulta->bindValue(':sector', $sector);
            $consulta->bindValue(':estado', 'listo para servir');
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
        }
        return $data;
    }
    /**
     * obtiene todos los pedids de la cocina osea de cocina,bares,tragos,
     *
     * @param [type] $id_empleado
     * @return void
     */
    public static function pedidosEnPreparacion($id_empleado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $sector = Usuario::obtenerSector($id_empleado);
        $data = [];

        if ($sector == 'cocina' || $sector == 'cervezeria' || $sector == 'postres' || $sector == 'barra') {
            $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidos.*," . $_ENV['BD_PRODUCTOS'] . ".sector," . $_ENV['BD_PRODUCTOS'] . ".descripcion FROM " . $_ENV['BD_PEDIDOS'] .
                " JOIN " . $_ENV['BD_PRODUCTOS'] . " ON " . $_ENV['BD_PEDIDOS'] . ".id_producto = " . $_ENV['BD_PRODUCTOS'] . ".id 
            WHERE " . $_ENV['BD_PRODUCTOS'] . ".sector = :sector AND (" . $_ENV['BD_PEDIDOS'] . ".estado = :estado1 OR " . $_ENV['BD_PEDIDOS'] . ".estado = :estado2)");

            $consulta->bindValue(':sector', $sector);
            $consulta->bindValue(':estado1', 'pendiente');
            $consulta->bindValue(':estado2', 'en preparacion');
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
        }
        return   $data;
    }

    public static function pedidosAdmin($id_empleado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $sector = Usuario::obtenerSector($id_empleado);
        $data = [];
        if ($sector == 'admin') {
            $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidos.*," . $_ENV['BD_PRODUCTOS'] . ".sector," . $_ENV['BD_PRODUCTOS'] . ".descripcion FROM " . $_ENV['BD_PEDIDOS'] .
                " JOIN " . $_ENV['BD_PRODUCTOS'] . " ON " . $_ENV['BD_PEDIDOS'] . ".id_producto = " . $_ENV['BD_PRODUCTOS'] . ".id");
            $consulta->execute();
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
        }
        return $data;
    }
    public static function pedidosCliente($id_comanda, $sector)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $data = [];
        /// if($sector == 'cliente'){
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidos.*," . $_ENV['BD_PRODUCTOS'] . ".precio," . $_ENV['BD_PRODUCTOS'] . ".sector," . $_ENV['BD_PRODUCTOS'] . ".descripcion FROM " . $_ENV['BD_PEDIDOS'] .
            " JOIN " . $_ENV['BD_PRODUCTOS'] . " ON " . $_ENV['BD_PEDIDOS'] . ".id_producto = " . $_ENV['BD_PRODUCTOS'] . ".id
            WHERE " . $_ENV['BD_PEDIDOS'] . ".id_comanda = :id_comanda
            ");
        $consulta->bindValue(':id_comanda', $id_comanda);
        $consulta->execute();
        $data = $consulta->fetchAll(PDO::FETCH_ASSOC);
        // }
        return $data;
    }
   
}
