<?php

use DI\ContainerBuilder;
use Slim\App;

require __DIR__ . '/../src/autoload.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../config/container.php');
$container = $containerBuilder->build();
$app = $container->get(App::class);

(require __DIR__ . '/../config/routes.php')($app);
(require __DIR__ . '/../config/middleware.php')($app);

$app->run();
