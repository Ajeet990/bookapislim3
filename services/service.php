<?php
use Slim\App;
use App\config\Db;
use App\model\UserModel;

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
    return new \App\controllers\UserController;
};
require __DIR__.'/../app/routes.php';



