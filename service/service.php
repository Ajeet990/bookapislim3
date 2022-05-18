<?php
use Slim\App;


// use \Psr\Http\Message\ServerRequestInterface as Request;
// use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__.'/../vendor/autoload.php';

$app = new App([
    'settings' => [
        'displayErrorDetails' => true,
    ]
]);

$container = $app->getContainer();
$container['UserController'] = function($container){
    return new \App\controllers\UserController;
};
require __DIR__.'/../app/routes.php';



