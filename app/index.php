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

//require_once './utils/AccesoDatos.php';
//require_once './middlewares/Logger.php';

//require_once './controllers/UsuarioController.php';
require_once __DIR__.'/utils/jwtController.php';

require_once __DIR__."/routers/index.router.php";

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
/*
function indexRouter ($group) {
    // Aquí se definen las rutas dentro del grupo
    $group->get('/ruta1', function ($request, $response) {
        // Lógica para la ruta '/prefix/ruta1'
         $response->getBody()->write('Ruta 1');
         return $response;
    });
}
*/
$app->group('', \indexRouter::class );
/*
$app->group('', function  ($group) {
    // Aquí se definen las rutas dentro del grupo
    $group->get('/ruta1', function ($request, $response) {
        // Lógica para la ruta '/prefix/ruta1'
         $response->getBody()->write('Ruta 1');
         return $response;
    });
});*/

//$router = new indexRouter();
//$router($app);

$app->run();
