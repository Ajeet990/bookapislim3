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
        // $nameKey = $this->csrf->getTokenNameKey();
        // $valueKey = $this->csrf->getTokenValueKey();
        // $name = $request->getAttribute($nameKey);
        // $value = $request->getAttribute($valueKey);

        // echo $name;
        var_dump($request->getAttribute('csrf_value'));

        // $params = $request->getParsedBody();
        
        // $image = $_FILES['image'];
        // $img_name = $image['name'];
        // $img_path = $image['tmp_name'];
        // $dest = __DIR__."/../img/".$img_name;
        // move_uploaded_file($img_path, $dest);

        // $name = trim($params['name'] ?? '');
        // $mobile_no = trim($params['mobile_no'] ?? '');
        // $address = trim($params['address'] ?? '');
        // $email = trim($params['email'] ?? '');
        // $password = trim($params['password'] ?? '');
        // $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // $signUpRst = $this->userModelObj->signUp($name, $mobile_no, $address, $email, $hashed_password, $dest);

        // if($signUpRst) {
        //     echo "Registerd";
        // } else {
        //     echo "Not registerd";
        // }
    }
}