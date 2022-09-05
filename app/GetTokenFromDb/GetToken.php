<?php
namespace App\getTokenFromDb;
class GetToken
{
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getTokenFromDb($userId)
    {
        $getToken = $this->conn->query("select token from register where id = '$userId'");
        $userToken = mysqli_fetch_assoc($getToken);
        return $userToken['token'];
        // echo $userId;

    }
}