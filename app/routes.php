<?php
use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/user', 'UserHelper:index');
$app->get('/users', 'UserHelper:userList');

