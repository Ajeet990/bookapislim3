<?php
use Slim\Http\Request;
use Slim\Http\Response;



$app->get('/user', 'UserController:index');