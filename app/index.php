<?php

use Slim\Handlers\Strategies\RequestHandler;
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);


use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

require_once __DIR__.'/utils/jwtController.php';

require_once __DIR__."/routers/index.router.php";

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__."/../");
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

//motor de plantillas  twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new \Twig\Environment($loader, ['debug'=>true]);
$twig->addExtension(new Twig\Extension\DebugExtension());

//agrego la clase de twig a todas las request como atributo 'view'
$app->add(function ($request, $handler) use ($twig) {
    $request = $request->withAttribute('view', $twig);
    $response = $handler->handle($request);
    return $response;
});


  
  


// Routes
$app->group('', \indexRouter::class )->add(function ($request, $handler) {
    $uri = $request->getUri();
    if ($uri->getScheme() !== 'https') {
      $uri = $uri->withScheme('https');
      return (new Slim\Psr7\Response())
        ->withStatus(301)
        ->withHeader('Location', (string)$uri);
    }
    return $handler->handle($request);
  });

$app->run();
