<?php
use Slim\App;
use App\Config\Db;
use App\Model\UserModel;
use App\Model\BookModel;
use App\Model\RequestModel;
session_start();


$db = new Db();
$conn = $db->getConnection();
$userModelObj = new UserModel($conn);
$bookModelObj = new BookModel($conn);
$requestModelObj = new RequestModel($conn);

$app = new App([
    'settings' => [
        'displayErrorDetails' => true,
    ]
]);
    

$container = $app->getContainer();
// Create Container

//container for userController
$container['UserHelper'] = function($container) {
    global $userModelObj;
    global $conn;
    return new \App\Controllers\UserController($userModelObj, $conn);
};

//container for bookController
$container['BookHelper'] = function($container) {
    global $bookModelObj;
    global $conn;
    return new \App\Controllers\BookController($bookModelObj, $conn);
};
//container for request related operations
$container['RequestHelper'] = function($container) {
    global $requestModelObj;
    global $conn;
    return new \App\Controllers\RequestController($requestModelObj, $conn);
};

//container for token generator
$container['tokenGen'] = function($container) {
    return new \App\Token\GenToken;
};
require __DIR__.'/../app/Routes.php';

