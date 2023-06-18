<?php

use Slim\Handlers\Strategies\RequestHandler;
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Response as ResponseMW;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './utils/AccesoDatos.php';
require_once './middlewares/Logger.php';

require_once './controllers/UsuarioController.php';
require_once './utils/jwtController.php';


// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes

//require __DIR__.'/routers/index.router.php';
//var_dump('indexRouter');
$app->group('/test', function ($group) {
    // Aquí se definen las rutas dentro del grupo
    $group->get('/ruta1', function ($request, $response) {
        // Lógica para la ruta '/prefix/ruta1'
        return $response->getBody()->write('Ruta 1');
    });
});

//$app->group('/test','indexRouter');
/*
$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('[/]', \UsuarioController::class . ':TraerTodos');
  $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
  $group->post('[/]', \UsuarioController::class . ':CargarUno');
});

$app->map(['GET', 'POST'], '/ejercicio1', function (Request $req, Response $res, array $args): Response {

  $res->getBody()->write("API => " . $req->getMethod() . "<br>");
  return $res;
})->add(function (Request $req,  $handler) {


  if ($req->getMethod() == 'GET') {

    $res = $handler->handle($req);
    $retorno = "NO se necesitan credenciales para GET <br>";
  }

  if ($req->getMethod() == 'POST') {
    $body = $req->getParsedBody();

    $retorno = "BUSCANDO CREDENCIAELES: <br>";

    if (isset($body['perfil']) && $body['perfil'] == 'admin') {
      $retorno .= "Bienvenido " . (isset($body['nombre']) ? $body['nombre'] : "") . "<br>";
      $res = $handler->handle($req);
    } else {
      $retorno .= "No cuenta con los permisos necesarios <br>";
      $res = new ResponseMW();
    }
  }

  if (!isset($res)) {
    $res = new ResponseMW();
    $res->getBody()->write("error en el servidor");
  } else {
    $retorno .= "volviendo del identificador de credenciales <br>";
    $res->getBody()->write($retorno);
  }
  return $res;
});


$app->map(['GET', 'POST'], '/ejercicio2', function (Request $req, Response $res, array $args): Response {

  $retorno = [
    'message' => "API => " . $req->getMethod(),
    'status' => '200'
  ];
  $res->getBody()->write(json_encode($retorno));
  return $res;
})->add(function (Request $req,  $handler) {

  $res = new ResponseMW();

  if ($req->getMethod() == 'GET') {
    $res = $handler->handle($req);
  }

  if ($req->getMethod() == 'POST') {
    $body = $req->getParsedBody();

    if (isset($body['obj_json'])) {
      $data = json_decode($body['obj_json'], true);
      //var_dump($data);
      if ($data['perfil'] == 'admin') {
        $res = $handler->handle($req);
      } else {
        $retorno = [
          'message' => "Error " . $data['nombre'] . " sin permisos",
          'status' => '403'
        ];

        $res->getBody()->write(json_encode($retorno));
      }
    } else {
      $retorno = [
        'message' => "no se encontro el obj_json",
        'status' => '403'
      ];
      $res->getBody()->write(json_encode($retorno));
    }
  }
  return $res;
});


$app->get('/ejercicio3',  \UsuarioController::class . ':TraerTodos');
$app->post('/ejercicio3',  \UsuarioController::class . ':TraerTodos')
->add(\Logger::class.":verificarCredencial");

function generateRandomKey($length = 32)
{
    $key = '';

    if (function_exists('random_bytes')) {
        $key = bin2hex(random_bytes($length));
    } elseif (function_exists('openssl_random_pseudo_bytes')) {
        $key = bin2hex(openssl_random_pseudo_bytes($length));
    } else {
        // En caso de que no se pueda generar una clave criptográficamente segura, puedes
        // utilizar una generación de clave basada en caracteres aleatorios, pero ten en cuenta
        // que no será tan segura como la generación criptográfica.
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $key .= $characters[rand(0, $charactersLength - 1)];
        }
    }

    return $key;
}


$app->post('/login', function($req, $res) {

  $body =$req->getParsedBody();
  //var_dump( $body);
  if(isset($body['user']) && isset($body['password']) && $body['user']=='info@utnfra.com' && $body['password']=='Utn750'){
    $retorno = [
      'user' => $body['user'],
      'rol' => 'admin'
    ];
    $token = AutentificadorJWT::CrearToken($retorno);
    $res->getBody()->write(json_encode($token));
  }else{
    $res->getBody()->write('no se genero el token');
  }

  return $res;
});

$app->post('/verificar', function($req, $res) {

  $body =$req->getParsedBody();
  //var_dump( $body);
  if(isset($body['token']) ){
    try{
      $data = AutentificadorJWT::ObtenerPayLoad($body['token']);
    }catch(Exception $e){
      $data = $e->getMessage();
    }
    $res->getBody()->write(json_encode($data));
  }else{
    $res->getBody()->write('no se genero el token');
  }

  return $res;
});

*/

$app->run();
