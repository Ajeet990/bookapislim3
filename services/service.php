<?php
use Slim\App;
use App\Config\Db;
use App\Model\UserModel;
// use App\controllers\UserController;

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

$container = $app->getContainer();
$container['UserHelper'] = function($container){
    global $userModelObj;
    return new \App\Controllers\UserController($userModelObj);
};
require __DIR__.'/../app/Routes.php';



