<?php

declare(strict_types=1); 

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory; 
use Slim\Middleware\BodyParsingMiddleware;
use App\Database;
use App\Middleware\CorsMiddleware;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$app = AppFactory::create();

$app->addRoutingMiddleware(); 
$app->add(new CorsMiddleware());
$app->addBodyParsingMiddleware();

$app->addErrorMiddleware(true, true, true);

require __DIR__ . '/../src/routes.php';

$app->run();
