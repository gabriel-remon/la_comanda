<?php
class jsonControler {

    public static function jsonDecoder($request, $response, $next)
    {
        $retorno = $next($request, $response);
        return $retorno;
    }
}