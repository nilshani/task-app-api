<?php

require __DIR__ . '/../vendor/autoload.php';

use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use Slim\Middleware\BodyParsingMiddleware;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$containerBuilder = new ContainerBuilder();
(require __DIR__ . '/../src/dependencies.php')($containerBuilder);
$container = $containerBuilder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();

// ✅ Add this middleware
$app->addBodyParsingMiddleware();

// Add simple API key auth middleware
$app->add(function ($request, $handler) {
    $apiKey = $request->getHeaderLine('X-API-Key');
    $validKey = $_ENV['API_KEY'] ?? 'secret123';

    if ($apiKey !== $validKey) {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response
            ->withStatus(401)
            ->withHeader('Content-Type', 'application/json');
    }

    return $handler->handle($request);
});


// Register routes
(require __DIR__ . '/../src/routes.php')($app);

$app->run();
