<?php
namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\GetTokenFromDb\GetToken;
use App\Config\Db;
class AuthMiddleware
{
    protected $getToken;
    protected $con;
    protected $conn;

    public function __construct()
    {
        $this->con = new Db();
        $this->conn = $this->con->getConnection();
        $this->getToken = new GetToken($this->conn);
    }
    public function __invoke(Request $request, Response $response, $next)
    {
        if(isset($_SESSION['userId']) && $_SESSION['userId'] != '' && isset($_SESSION['userLoggedInToken'])) {
            $tokenFromDb = $this->getToken->getTokenFromDb($_SESSION['userId']);
            if($tokenFromDb == $_SESSION['userLoggedInToken']) {
                $response = $next($request, $response);
                return $response;
            } else {
                $jsonMessage = array("isSuccess" => false,
                "message" => "Invalid Request. Token not matching.",
                "Token" => "");
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(500);
            }
        } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "User Not logged In, please logIn first",
            "Token" => "");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(500);
        }
    }
}