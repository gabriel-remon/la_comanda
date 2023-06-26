<?php

include_once __DIR__ . "/../utils/AccesoDatos.php";


class Usuario
{
    public $id;
    public $email;
    public $password;
    public $nombre;
    public $fecha_nacimiento;
    public $sector;
    public $estado;

    /*
    public function __construct($usuario=null, $clave=null,$id=null){
        $this->usuario = $usuario;
        $this->clave = $clave;
        $this->id = $id;
    }
    */

    public function setEmail($email)
    {
    }
    public function setNombre($email)
    {
    }
    public function setContraseña($email)
    {
    }
    public function setfechaNacimiento($email)
    {
    }
    public function setSector($email)
    {
    }
    public function setEstado($email)
    {
    }


    public function crearUsuario()
    {

        $retorno = null;
        $usuario = Usuario::obtenerUsuario($this->email);

        if (!isset($usuario)) {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO " . $_ENV['BD_USUARIOS'] . " (email, password,nombre,fecha_nacimiento,sector,estado)
            VALUES (:email, :password, :nombre, :fecha_nacimiento, :sector, :estado)");
            $claveHash = password_hash($this->password, PASSWORD_DEFAULT);
            $consulta->bindValue(':email', $this->email, PDO::PARAM_STR);
            $consulta->bindValue(':password', $claveHash);
            $consulta->bindValue(':nombre', $this->nombre);
            $consulta->bindValue(':fecha_nacimiento', $this->fecha_nacimiento->format('Y-m-d'));

            $consulta->bindValue(':sector', $this->sector);
            $consulta->bindValue(':estado',  true, PDo::PARAM_BOOL);

            $consulta->execute();
            $retorno = $objAccesoDatos->obtenerUltimoId();
        }


        return $retorno;
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, email, nombre,fecha_nacimiento,sector,estado 
                                                        FROM " . $_ENV['BD_USUARIOS']);
        $consulta->execute();
        $data = $consulta->fetchAll(PDO::FETCH_ASSOC);

        $retorno = [];
        foreach ($data as $element) {
            $usuario = new Usuario();
            $usuario->id = $element['id'];
            $usuario->email = $element['email'];
            $usuario->nombre = $element['nombre'];
            $usuario->fecha_nacimiento = $element['fecha_nacimiento'];
            $usuario->sector = $element['sector'];
            $usuario->estado = $element['estado'];
            array_push($retorno, $usuario);
        }

        return $retorno;
    }

    public static function obtenerUsuario($email)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM " . $_ENV['BD_USUARIOS'] . " WHERE email = :email");
        $consulta->bindValue(':email', $email, PDO::PARAM_STR);
        //$consulta->execute();
        if ($consulta->execute()) {
            $retorno = $consulta->fetchObject('Usuario');
            if($retorno === false){
                $retorno = null;
            }
        } else {
            $retorno = null;
        }

        return $retorno;
    }
    public static function validarUsuario($email, $password)
    {

        $retorno = Usuario::obtenerUsuario($email);
        //$consulta->execute();
        if (isset($retorno)) {

            if (!password_verify($password, $retorno->password)) {
                $retorno = null;
            }else{
                Usuario::darAsistencia($retorno->id);
            }
        }

        return $retorno;
    }


    public static function darAsistencia($id){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $query="INSERT INTO asistencia (id_empleado, fecha_entrada)
        SELECT :id_empleado, :fecha_entrada
        FROM DUAL
        WHERE NOT EXISTS 
        ( SELECT 1 FROM asistencia 
         WHERE id_empleado = :id_empleado_aux
         AND DATE(fecha_entrada) = DATE(:fecha_entrada_aux))";

        
        $consulta = $objAccesoDatos->prepararConsulta($query);
        $consulta->bindValue(':id_empleado', $id,PDO::PARAM_INT);
        $consulta->bindValue(':id_empleado_aux', $id,PDO::PARAM_INT);
        $consulta->bindValue(':fecha_entrada', date("Y-m-d H:i:s"));
        $consulta->bindValue(':fecha_entrada_aux', date("Y-m-d H:i:s"));
        //$consulta->execute();
        $consulta->execute();
        
    }


    public static function modificarUsuario($newUser)
    {
        $retorno = Usuario::obtenerUsuario($newUser->email);

        if (isset($retorno)) {

            $objAccesoDato = AccesoDatos::obtenerInstancia();

            $consulta = $objAccesoDato->prepararConsulta("UPDATE " . $_ENV['BD_USUARIOS'] . " 
                                                        SET email = :email, password = :password, nombre = :nombre, fecha_nacimiento = :fecha_nacimiento, sector = :sector, estado = :estado 
                                                        WHERE id = :id");
            $claveHash = password_hash($newUser->password, PASSWORD_DEFAULT);
            $consulta->bindValue(':email', $newUser->email, PDO::PARAM_STR);
            $consulta->bindValue(':password', $claveHash);
            $consulta->bindValue(':nombre', $newUser->nombre);
            $consulta->bindValue(':fecha_nacimiento', $newUser->fecha_nacimiento->format('Y-m-d'));
            $consulta->bindValue(':sector', $newUser->sector);
            $consulta->bindValue(':estado',  $newUser->estado == "true"?true:false);
            $consulta->bindValue(':id',  $retorno->id);
            $retorno = $consulta->execute();
        }

        return $retorno;
    }

    public static function borrarUsuario($email)
    {
        $retorno = false;
        $usuario = Usuario::obtenerUsuario($email);
        if ($usuario && $usuario->estado) {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE " . $_ENV['BD_USUARIOS'] . " SET estado = false WHERE email = :email");
            //$fecha = new DateTime(date("d-m-Y"));
            $consulta->bindValue(':email', $email, PDO::PARAM_INT);
            //$consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
            $retorno =  $consulta->execute();
        }

        return $retorno;
    }

    public static function altaUsuario($email)
    {

        $usuario = Usuario::obtenerUsuario($email);
        if ($usuario && $usuario->estado == false) {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE " . $_ENV['BD_USUARIOS'] . " SET estado = true WHERE email = :email");
            //$fecha = new DateTime(date("d-m-Y"));
            $consulta->bindValue(':email', $email, PDO::PARAM_INT);
            //$consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
            $consulta->execute();
        }
    }

    public static function exist($id, $sector = null)
    {

        $objAccesoDato = AccesoDatos::obtenerInstancia();
        if (isset($sector)) {
            $consulta = $objAccesoDato->prepararConsulta("SELECT id " . $_ENV['BD_USUARIOS'] . " 
                                                                WHERE id = :id and sector = :sector");
            $consulta->bindValue(':sector', $sector);
        } else {
            $consulta = $objAccesoDato->prepararConsulta("SELECT id " . $_ENV['BD_USUARIOS'] . " 
                                                           WHERE id = :id");
        }
        $consulta->bindValue(':id', $id);
        $consulta->execute();
        $retorno = $consulta->fetchObject('Usuario');
        if ($retorno !== false) {
            $retorno = true;
        }

        return $retorno;
    }

    public static function obtenerSector($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("SELECT sector from " . $_ENV['BD_USUARIOS'] . " WHERE id = :id");
        $consulta->bindValue(':id', $id);
        $consulta->execute();
        $retorno = $consulta->fetchObject();
        if ($retorno !== false) {
            $retorno = $retorno->sector;
        } 

        return $retorno;
    }
}
