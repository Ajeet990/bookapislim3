<?php
use Slim\App;
use App\Config\Db;
use App\Model\UserModel;
session_start();

// use App\controllers\UserController;
// use Slim\Csrf\Guard;

$db = new Db();
$conn = $db->getConnection();
$userModelObj = new UserModel($conn);



// use \Psr\Http\Message\ServerRequestInterface as Request;
// use \Psr\Http\Message\ResponseInterface as Response;

// require __DIR__.'/../vendor/autoload.php';

$app = new App([
    'settings' => [
        'displayErrorDetails' => true,
        ]
    ]);
    
// $app->add(new \Slim\Csrf\Guard);
$container = $app->getContainer();
// Create Container

$container['UserHelper'] = function($container) {
    global $userModelObj;
    return new \App\Controllers\UserController($userModelObj);
};

$container['csrf'] = function($container) {
    return new \Slim\Csrf\Guard;
};

$app->add($container->csrf);

require __DIR__.'/../app/Routes.php';

