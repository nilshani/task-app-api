<?php

use Slim\App;
use App\TaskController;

return function (App $app) {
    $container = $app->getContainer();
    $controller = new TaskController($container);

    $app->get('/tasks', [$controller, 'list']);
    $app->get('/tasks/{id}', [$controller, 'get']);
    $app->post('/tasks', [$controller, 'create']);
    $app->put('/tasks/{id}', [$controller, 'update']);
    $app->delete('/tasks/{id}', [$controller, 'delete']);
};
