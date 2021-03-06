<?php
use Slim\Http\Request;
use Slim\Http\Response;
use App\Middleware\AuthMiddleware;

// $mw = $app->get('/signUp', 'UserHelper:middle');
//end points related to users.
$app->get('/user', 'UserHelper:index');
$app->get('/users', 'UserHelper:userList');
$app->get('/tokenGen', 'tokenGen:genCSRFTkn');
$app->post('/signUp', 'UserHelper:signUp');
$app->post('/logIn', 'UserHelper:logIn');

$app->group('', function() use ($app) {

    $app->get('/logOut', 'UserHelper:logOut');
    $app->post('/updateProfile', 'UserHelper:updateProfile');
    
    //End point related to books.
    $app->post('/addBook', 'BookHelper:addBook');
    $app->post('/editBook/{bookId}', 'BookHelper:editBook');
    $app->post('/deleteBook/{bookId}', 'BookHelper:deleteBook');
    $app->post('/bookFeedback/{bookId}', 'BookHelper:bookFeedback');
    $app->get('/personalBooks', 'BookHelper:personalBooks');
    $app->get('/searchBook/{searchString}','BookHelper:searchBook');

    //End point related to request
    $app->post('/requestBook/{bookId}', 'RequestHelper:requestBook');
    $app->get('/listReceivedRequest', 'RequestHelper:listReceivedRequest');
    $app->get('/listSentRequest', 'RequestHelper:listSentRequest');
    $app->post('/grantIssueRequest/{requestingId}', 'RequestHelper:grantIssueRequest');
    $app->post('/cancelIssueRequest/{requestingId}', 'RequestHelper:cancelIssueRequest');
    $app->post('/returnBookRequest/{requestingId}', 'RequestHelper:returnBookRequest');
    $app->post('/grantReturnRequest/{requestingId}', 'RequestHelper:grantReturnRequest');
})->add(new AuthMiddleware($container));




