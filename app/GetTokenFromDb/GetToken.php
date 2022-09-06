<?php
namespace App\GetTokenFromDb;
class GetToken
{
    protected $conn;
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getTokenFromDb($userId)
    {
        $getToken = $this->conn->query("select token from register where id = '$userId'");
        $userToken = mysqli_fetch_assoc($getToken);
        return $userToken['token'];
    }
}