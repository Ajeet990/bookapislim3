<?php

namespace App\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController
{
    public function __construct($userModelObj)
    {
        $this->userModelObj = $userModelObj;
    }
    public function index()
    {
        return "UserController here";
        // print_r($this->userModelObj) ;
    }
    public function userList()
    {
        // return "here";
        $uList = $this->userModelObj->listUser();
        // print_r($this->userModelObj);
        // return $uList;
        echo "<pre>";
        print_r($uList);
    }
    public function signUp(Request $request, Response $response)
    {
        $params = $request->getParsedBody();
        $name = trim($params['name'] ?? '');
        $mobile_no = trim($params['mobile_no'] ?? '');
        $address = trim($params['address'] ?? '');
        $email = trim($params['email'] ?? '');
        $password = trim($params['password'] ?? '');
        $signUpRst = $this->userModelObj->signUp($name, $mobile_no, $address, $email,$password);
        if($signUpRst) {
            echo "Registerd";
        } else {
            echo "Not registerd";
        }
    }
}