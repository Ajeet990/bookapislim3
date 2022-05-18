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
        $listUser = array();
        $userQuery = $this->conn->query("select * from register");
        while($row = mysqli_fetch_assoc($userQuery)) {
            array_push($listUser, $row);
        }
        return $listUser;

        
    }

    public function signUp($name, $mobile_no, $address, $email, $password)
    {
        $registerQry = $this->conn->query("INSERT INTO `register` (`user_name`, `mobile_no`, `address`, `email`, `password`) VALUES ('$name', '$mobile_no', '$address', '$email', '$password')");
        return $registerQry;


    }
}
