<?php
namespace App\model;
class UserModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function listUser()
    {
        return "Listing users";
    }
}
