<?php

use DI\ContainerBuilder;
// use PDO;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        PDO::class => function () {
            $host = $_ENV['DB_HOST'] ?? 'db';
            $port = $_ENV['DB_PORT'] ?? '5432';
            $db   = $_ENV['DB_DATABASE'] ?? 'tasks_db';
            $user = $_ENV['DB_USERNAME'] ?? 'postgres';
            $pass = $_ENV['DB_PASSWORD'] ?? 'secret';
            $dsn = "pgsql:host=$host;port=$port;dbname=$db";

            return new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
    ]);
};
