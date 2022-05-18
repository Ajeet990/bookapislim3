<?php

namespace App\controllers;

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
}