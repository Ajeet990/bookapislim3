<?php
namespace App\GetTokenFromDb;
class GetToken
{
    protected $conn;
    
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getTokenFromDb(int $userId) : string
    {
        $getToken = $this->conn->prepare("select token from register where id = ?");
        $getToken->bind_param("i", $userId);
        $getToken->execute();
        $token = $getToken->get_result();
        $getToken = $token->fetch_assoc();
        return $getToken['token'];
    }
}