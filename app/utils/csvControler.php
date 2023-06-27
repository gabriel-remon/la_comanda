<?php
class csvControler
{

    public static function descargarcsv($req, $res, $args)
    {
        $tabla = $args['baseDatos'];
        if (
            $tabla == $_ENV['BD_USUARIOS'] ||
            $tabla == $_ENV['BD_PRODUCTOS'] ||
            $tabla == $_ENV['BD_PEDIDOS'] ||
            $tabla == $_ENV['BD_MESAS'] ||
            $tabla == $_ENV['BD_ASISTENCIA']
        ) {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM " . $tabla);
            // $consulta->bindValue(':tabla', $args['db']);
            $consulta->execute();
            $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

            // Crear el contenido del CSV en una cadena
            $contenidoCSV = '';
            $encabezados = array_keys($resultados[0]);
            $contenidoCSV .= implode(',', $encabezados) . "\n";
            foreach ($resultados as $fila) {
                $contenidoCSV .= implode(',', $fila) . "\n";
            }
            $res = $res
                ->withHeader('Content-Type', 'text/csv')
                ->withHeader('Content-Disposition', 'attachment; filename="' . $tabla . '.csv"')
                ->withBody(new \Slim\Psr7\Stream(fopen('php://temp', 'r+')));

            $res->getBody()->write($contenidoCSV);
        } else {
            $res->getBody()->write('no se pudo realizar la descarga, base de datos no encontrada');
            $res = $res->withStatus(500);
        }
        return $res;
    }

    public static function cargarcsv($req, $res, $args)
    {
        $tabla = $args['baseDatos'];
        if (
            $tabla == $_ENV['BD_USUARIOS'] ||
            $tabla == $_ENV['BD_PRODUCTOS'] ||
            $tabla == $_ENV['BD_PEDIDOS'] ||
            $tabla == $_ENV['BD_MESAS'] ||
            $tabla == $_ENV['BD_ASISTENCIA']
        ) {
            $uploadedFile = $req->getUploadedFiles()['csv'];
            $csvFile = $uploadedFile->getStream()->getMetadata('uri');
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $keys = [];
            $content = [];

            if (($handle = fopen($csvFile, 'r')) !== false) {
                $keys = fgetcsv($handle); 
                
                $keys = fgetcsv($handle); 
                
                while (($data = fgetcsv($handle)) !== false) {
                    $content[] = $data;
                }

                fclose($handle);
                $arrayClaves = $keys;
               
                $consulta = $objAccesoDatos->prepararConsulta("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :tabla");
                $consulta->bindValue(':tabla', $tabla);
                $consulta->execute();
                $columnasTabla = [];
                while ($fila = $consulta->fetch(PDO::FETCH_ASSOC)) {
                    $columnasTabla[] = $fila['COLUMN_NAME'];
                }
                $diferencias = array_diff($arrayClaves, $columnasTabla);
                if (empty($diferencias)) {
                    $columnasStr = implode(', ', $arrayClaves);
                    $consulta = $objAccesoDatos->prepararConsulta("DELETE FROM $tabla ");
                        $consulta->execute();
                    foreach ($content as $element) {
                        $valoresStr = "'" . implode("', '", $element) . "'";
                        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO $tabla ($columnasStr) VALUES ($valoresStr)");
                        $consulta->execute();
                    }
                    $res->getBody()->write(' se pudo realizar la carga');
                    $res = $res->withStatus(200);
                } else {
                    $res->getBody()->write('no se pudo realizar la carga 2');
                    $res = $res->withStatus(500);
                }
            }
        } else {
            
            $res->getBody()->write('no se pudo realizar la carga 1');
            $res = $res->withStatus(500);
        }

        return $res;
    }
}
