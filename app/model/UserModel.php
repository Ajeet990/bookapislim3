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
}
