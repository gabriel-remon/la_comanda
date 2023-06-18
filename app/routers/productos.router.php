<?php
include_once __DIR__.'/../interfaces/IApiUsable.php';
include_once __DIR__.'/../models/Producto.php';

class routerProductos implements IApiUsable
{
    public function CargarUno($req, $res, $args)
    {
        
        $body = $req->getParsedBody();
        $newProduct = new Producto();
        $newProduct->descripcion = $body['descripcion'];
        $newProduct->sector = $body['sector'];
        $newProduct->precio = $body['precio'];
        $newProduct->estado = $body['estado'];
    
        $idProduct = $newProduct->crearProducto();
        $res->getBody()->write('nuevo producto creado, id: '.$idProduct);
        return $res;
    }

    public function TraerUno($req, $res, $args)
    {
        $productos = Producto::obtenerProducto($args['id']);

        $res->getBody()->write(json_encode($productos));
        return $res;
    }

    public function TraerTodos($req, $res, $args)
    {
        $productos = Producto::obtenerTodos();

        $res->getBody()->write(json_encode($productos));
        return $res;
    }
    
    public function ModificarUno($req, $res, $args)
    {
        $body = $req->getParsedBody();
        $newProduct = new Producto();
        //var_dump($body);
        
        $newProduct->descripcion = $body['descripcion'];
        $newProduct->sector = $body['sector'];
        $newProduct->precio = $body['precio'];
        $newProduct->estado = $body['estado'];
        $newProduct->id = $args['id'];
        
        $idProduct = Producto::modificarProducto($newProduct);
        $res->getBody()->write('producto modificado con exito id: '.$idProduct);
        return $res;
    }

    public function BorrarUno($req, $res, $args)
    {
        $idEliminar = $args['id'];
        
        $idProduct = Producto::borrarProducto($idEliminar);
        $res->getBody()->write('producto eliminado con exito id: '.$idProduct);
        return $res;
    }
}
