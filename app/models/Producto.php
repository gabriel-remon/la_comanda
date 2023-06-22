
<?php

include_once __DIR__ . "/../utils/AccesoDatos.php";

class Producto
{
    public $id;
    public $descripcion;
    public $sector;
    public $precio;
    public $estado;

    /*
    public function __construct($usuario=null, $clave=null,$id=null){
        $this->usuario = $usuario;
        $this->clave = $clave;
        $this->id = $id;
    }
    */

    public function setDescripcion($email)
    {
    }
    public function setSector($email)
    {
    }
    public function setEstado($email)
    {
    }


    public function crearProducto()
    {

        $retorno = -1;

        if (Producto::obtenerProducto($this->id) == null); {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO " . $_ENV['BD_PRODUCTOS'] . " (descripcion, sector ,precio, estado)
                                                             VALUES (:descripcion,:sector,:precio, :estado)");

            $consulta->bindValue(':descripcion', $this->descripcion);
            $consulta->bindValue(':sector', $this->sector);
            $consulta->bindValue(':precio', $this->precio);
            $consulta->bindValue(':estado', true);
            $consulta->execute();
            $retorno = $objAccesoDatos->obtenerUltimoId();
        }


        return $retorno;
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM " . $_ENV['BD_PRODUCTOS']);
        $consulta->execute();
        $data = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $retorno = [];
        foreach ($data as $element) {
            $usuario = new Producto();
            $usuario->id = $element['id'];
            $usuario->descripcion = $element['descripcion'];
            $usuario->sector = $element['sector'];
            $usuario->precio = $element['precio'];
            $usuario->estado = $element['estado'];
            array_push($retorno, $usuario);
        }

        return $retorno;
    }

    public static function obtenerProducto($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM " . $_ENV['BD_PRODUCTOS'] . " WHERE id = :id");
        $consulta->bindValue(':id', $id);
        //$consulta->execute();
        if ($consulta->execute()) {
            $retorno = $consulta->fetchObject('Producto');
        
        } else {
            $retorno = null;
        }

        return $retorno;
    }

    public static function modificarProducto($newProduct)
    {
        $retorno = false;

        if (Producto::obtenerProducto($newProduct->id)) {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE " . $_ENV['BD_PRODUCTOS'] . " 
                                                        SET descripcion = :descripcion, sector = :sector, precio = :precio, estado = :estado 
                                                        WHERE id = :id");

            $consulta->bindValue(':descripcion', $newProduct->descripcion, PDO::PARAM_STR);
            $consulta->bindValue(':sector', $newProduct->sector);
            $consulta->bindValue(':precio', $newProduct->precio);
            $consulta->bindValue(':estado',  $newProduct->estado);
            $consulta->bindValue(':id',  $newProduct->id);
            $retorno = $consulta->execute();
        }

        return $retorno;
    }

    /**
     * -1 no existe el producto
     * -2 el producto ya esta dado de baja
     * -3 error en el server
     * mayor 0  - el producto fue dado de baja
     *
     * @param [type] $id
     * @return int
     */
    public static function borrarProducto($id)
    {
        $producto = Producto::obtenerProducto($id);
        if(!$producto){
            $retorno = -1;
            return $retorno;
    }
        if(!$producto->estado){
            $retorno = -2;
            return $retorno;
    }

        if ( $producto->estado) {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE " . $_ENV['BD_PRODUCTOS'] . "
                                                         SET estado = false WHERE id = :id");
            //$fecha = new DateTime(date("d-m-Y"));
            $consulta->bindValue(':id', $id);
            //$consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
            $consulta->execute()? $retorno= $id: $retorno=-3;
        }

        return $retorno;
    }

    public static function altaProducto($id)
    {

        $retorno = false;
        $producto = Producto::obtenerProducto($id);
        if ($producto && $producto->estado) {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE " . $_ENV['BD_PRODUCTOS'] . " 
                                                            SET estado = true WHERE id = :id");
            //$fecha = new DateTime(date("d-m-Y"));
            $consulta->bindValue(':id', $id);
            //$consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
            $retorno = $consulta->execute();
        }

        return $retorno;
    }

    public static function exist($id)
    {


        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("SELECT id from " . $_ENV['BD_PRODUCTOS'] . " WHERE id = :id");
        $consulta->bindValue(':id', $id);
        $consulta->execute();
        $retorno = $consulta->fetchObject('Producto');
        if ($retorno !== false) {
            $retorno = true;
        } 

        return $retorno;
    }
}
