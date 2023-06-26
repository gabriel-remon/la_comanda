<?php

include_once __DIR__.'/Pedido.php';
include_once __DIR__.'/../utils/AccesoDatos.php';

class Mesa
{
    public $nombre_cliente;
    public $numero_mesa;
    public $password;
    public $estado;
    public $id_encuesta;
    public $url_foto;
    public $id;

    public function __construct($nombre_cliente, $numero_mesa)
    {
        $this->nombre_cliente = $nombre_cliente;
        $this->numero_mesa = $numero_mesa;
        $this->password = substr(bin2hex(random_bytes(3)), 0, 5);
        $this->estado = explode(',',$_ENV['ESTADO_MESA'])[0];
    }

    public function altaMesa($pedido= null)
    {

        $retorno = -1;
        if (Mesa::obtenerMesa($this->numero_mesa,true)) {
            $retorno = -2;
            return $retorno;
        }
       
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO ".$_ENV['BD_MESAS']." (nombre_cliente, numero_mesa,password,estado)
                                                         VALUES (:nombre_cliente, :numero_mesa, :password, :estado)");
        $consulta->bindValue(':nombre_cliente', $this->nombre_cliente, PDO::PARAM_STR);
        $consulta->bindValue(':numero_mesa', $this->numero_mesa);
        $consulta->bindValue(':password',  $this->password);
        $consulta->bindValue(':estado',  $this->estado );
        
        if ($consulta->execute()) {
            $this->id = $objAccesoDatos->obtenerUltimoId();
            $retorno = $this->id;
            if(isset($pedido) && is_array($pedido)){
                foreach ($pedido as $element) {
                    $element->altaPedido($this->id);
                }
            }
        }

        return $retorno;
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ".$_ENV['BD_MESAS']);
        if ($consulta->execute()) {
            $data = $consulta->fetchAll(PDO::FETCH_ASSOC);

            $retorno = [];
            foreach ($data as $element) {
                
                $mesa = new Mesa($element['nombre_cliente'],$element['numero_mesa']);
                $mesa->password = $element['password'];
                $mesa->estado = $element['estado'];
                $mesa->id_encuesta = $element['id_encuesta'];
                $mesa->id = $element['id'];
                array_push($retorno, $mesa);
            }
        } else {
            $retorno = null;
        }

        return $retorno;
    }

    /**
     * si opcion es true enves de de is de buscara por numero de mesa
     */
    public static function obtenerMesa($buscardor, $opcion = false)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        if ($opcion) {
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ".$_ENV['BD_MESAS']." 
                                                            WHERE numero_mesa = :numero_mesa AND estado <> :estado");
            $consulta->bindValue(':numero_mesa', $buscardor);
            $consulta->bindValue(':estado', "cerrada");
        } else {
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ".$_ENV['BD_MESAS']." WHERE id = :id");
            $consulta->bindValue(':id', $buscardor);
        } 

        if ($consulta->execute()) {
//            $retorno = $consulta->fetchObject();
            $data = $consulta->fetchObject();
            if($data === false ){
                $retorno=null;
            }else{
                $retorno = new Mesa($data->nombre_cliente,$data->numero_mesa);
                $retorno->password = $data->password;
                $retorno->estado = $data->estado;
                $retorno->id_encuesta = $data->id_encuesta;
                $retorno->id = $data->id;
                $retorno->url_foto = $data->url_foto;
            }

        } else {
            $retorno = null;
        }

        return $retorno;
    }

    public static function modificartMesa($newMesa)
    {

        $mesaGuardada= Mesa::obtenerMesa($newMesa->id);
        if(isset($mesaGuardada)){
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE ".$_ENV['BD_MESAS']." 
                                                        SET nombre_cliente = :nombre_cliente, 
                                                        numero_mesa = :numero_mesa ,
                                                        password = :password ,
                                                        estado = :estado ,
                                                        id_encuesta = :id_encuesta 
                                                        WHERE id = :id");
            $consulta->bindValue(':nombre_cliente', $newMesa->nombre_cliente, PDO::PARAM_STR);
            $consulta->bindValue(':numero_mesa', $newMesa->numero_mesa, PDO::PARAM_STR);
            $consulta->bindValue(':password', isset($newMesa->password)?$newMesa->password:$mesaGuardada->password);
            $consulta->bindValue(':estado', isset($newMesa->estado)?$newMesa->estado:$mesaGuardada->estado);
            $consulta->bindValue(':id_encuesta', $newMesa->id_encuesta, PDO::PARAM_INT);
            $consulta->bindValue(':id', $newMesa->id, PDO::PARAM_INT);
    
            return $consulta->execute();
        }
        return false;
    }

    public static function borrarMesa($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE ".$_ENV['BD_MESAS']." 
                                                    SET estado = :estado 
                                                    WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':estado', "cancelada");
        return $consulta->execute();
    }
    public static function cargarFoto($id,$url_foto)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE ".$_ENV['BD_MESAS']." 
                                                    SET url_foto = :url_foto 
                                                    WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':url_foto', $url_foto);
        return $consulta->execute();
    }

    public static function validarMesa($numeroMesa, $password)
    {

        $retorno = Mesa::obtenerMesa($numeroMesa,true);
        if (isset($retorno)) {

            if ($password != $retorno->password) {
                $retorno = null;
            }
        }

        return $retorno;
    }
    public static function ClienteComiendo($id)
    {
        $mesa = Mesa::obtenerMesa($id);
        $estados = explode(',',$_ENV['ESTADO_MESA']);

        if (isset($mesa) && $mesa->estado == $estados[0]) {

            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("SELECT COUNT(*) AS cnt 
                                                        FROM ".$_ENV['BD_PEDIDOS']." 
                                                        WHERE id_comanda = :id_comanda AND estado != 'entregado'");
            $consulta->bindValue(':id_comanda', $id, PDO::PARAM_INT);
            $result =$consulta->execute();
            if ($result['cnt'] == 0) {

                $consulta = $objAccesoDato->prepararConsulta("UPDATE ".$_ENV['BD_MESAS']." 
                SET estado = :estado 
                WHERE id = :id");
                $consulta->bindValue(':id', $id, PDO::PARAM_INT);
                $consulta->bindValue(':estado', $estados[1]);
                return $consulta->execute();
            }

        }

        return false;
    }
    public static function ListoParaPagar($id)
    {

        $mesa = Mesa::obtenerMesa($id);
        $estados = explode(',',$_ENV['ESTADO_MESA']);
        $retorno = false ;

        if (isset($mesa) && $mesa->estado == $estados[1]) {

            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE ".$_ENV['BD_MESAS']." 
                                                    SET estado = :estado 
                                                    WHERE id = :id");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->bindValue(':estado', $estados[2]);
            $retorno =$consulta->execute();
        }

        return $retorno;
    }
    public static function Cobrar($id)
    {

        $mesa = Mesa::obtenerMesa($id);
        $estados = explode(',',$_ENV['ESTADO_MESA']);
        $retorno = false ;

        if (isset($mesa) && $mesa->estado == $estados[2]) {

            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE ".$_ENV['BD_MESAS']." 
                                                    SET estado = :estado 
                                                    WHERE id = :id");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->bindValue(':estado', $estados[3]);
            $retorno =$consulta->execute();
        }

        return $retorno;
    }

    public static function estado($id)
    {


        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("SELECT estado from " . $_ENV['BD_MESAS'] . " WHERE id = :id");
        $consulta->bindValue(':id', $id);
        $consulta->execute();
        $retorno = $consulta->fetchObject();
        if ($retorno == false) {
            $retorno = null;
        } else{
            $retorno = $retorno->estado;
        }

        return $retorno;
    }
}
