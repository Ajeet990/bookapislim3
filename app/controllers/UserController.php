<?php

namespace App\controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\ValidateToken\ValidateToken;
use App\Token\Token;

// session_start();

class UserController
{
    private $val;
    public function __construct($userModelObj, $conn)
    {
        $this->conn = $conn;
        $this->valToken = new ValidateToken($this->conn);
        $this->userModelObj = $userModelObj;
        $this->token = new Token();
    }

    public function checkEmailAndMobileExists($email, $mobile_no)
    {
        $valQry = $this->conn->query("select * from register where email = '$email' or mobile_no = '$mobile_no'");
        $num = mysqli_num_rows($valQry);
        if($num > 0)
        {
            return true;
        } else {
            return false;
        }

        // echo $email."-".$mobile_no;
    }


    public function userList(Request $request, Response $response)
    {
        // return "here";
        $uList = array($this->userModelObj->listUser());
        $jsonMessage = array("isSuccess" => true,
                                "message" => "List of users",
                                "list" => $uList);
        $response->getBody()->write(json_encode($jsonMessage));
        return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(200);
    }
    public function signUp(Request $request, Response $response)
    {
        $params = $request->getParsedBody();
        
        $image = $_FILES['image'];
        $img_name = $image['name'];
        $img_path = $image['tmp_name'];
        $dest = __DIR__."/../img/users/".$img_name;
        move_uploaded_file($img_path, $dest);

        $name = trim($params['name'] ?? '');
        $mobile_no = trim($params['mobile_no'] ?? '');
        $address = trim($params['address'] ?? '');
        $email = trim($params['email'] ?? '');
        $password = trim($params['password'] ?? '');
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $validation = $this->checkEmailAndMobileExists($email, $mobile_no);
        if($validation == !true)
        {
            $signUpRst = $this->userModelObj->signUp($name, $mobile_no, $address, $email, $hashed_password, $dest);
            if($signUpRst) {
                $tok_val = $this->token->genCSRFTkn();
                $jsonMessage = array("isSuccess" => true,
                                        "message" => "Registration success",
                                        "Token" => $tok_val);
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(200);
            } else {
                $jsonMessage = array("isSuccess" => false,
                                    "message" => "Something error occred.");
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(500);
            }
        } else {
            $jsonMessage = array("isSuccess" => false,
                            "message" => "Phone or Email already exists.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }
    }

    public function logIn(Request $request, Response $response)
    {
        if(!isset($_SESSION['userId'])) {

        $params = $request->getParsedBody();
        $mobile_no = trim($params['mobile_no'] ?? '');
        $password = trim($params['password'] ?? '');

        $loginRst = $this->userModelObj->logIn($mobile_no, $password);
        if($loginRst)
        {
            if(password_verify($password, $loginRst[0])) {
                    $tok_val = $this->token->genCSRFTkn();
                    $this->userModelObj->addToken($mobile_no, $tok_val);
                    $_SESSION['userLoggedInToken'] = $tok_val;
                    $_SESSION['userLoggedInMobile'] = $mobile_no;
                    $_SESSION['userId'] = $loginRst[1];
                    $_SESSION['userName'] = $loginRst[2];

                    $jsonMessage = array("isSuccess" => true,
                                            "message" => "Login Success",
                                            "Token" => $tok_val);
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                    ->withHeader("content-type", "application/json")
                    ->withStatus(200);
            }
            else {
                    $jsonMessage = array("isSuccess" => false,
                                        "message" => "Login Failed. Password doesn't match");
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                    ->withHeader("content-type", "application/json")
                    ->withStatus(200);
            }
        }
        else {
                $jsonMessage = array("isSuccess" => false,
                "message" => "Login Failed. Mobile number doesn't exits.");
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(200);
        }
        } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "You are already loggedIn, please logOut first.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }

    }

    public function logOut(Request $request, Response $response)
    {
        if(isset($_SESSION['userId']) && $_SESSION['userId'] != '') {
            $removeTokenRst = $this->userModelObj->removeToken($_SESSION['userLoggedInMobile']);
            if($removeTokenRst) {
                unset($_SESSION['userLoggedInToken']);
                unset($_SESSION['userLoggedInMobile']);
                unset($_SESSION['userId']);
                session_destroy();
                $jsonMessage = array("isSuccess" => true,
                "message" => "Logged Out successfully.");
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(200);
            }
        } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "User not logged in, please login first.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }
    }


    public function updateProfile(Request $request, Response $response)
    {
        if( isset($_SESSION['userId']) && $_SESSION['userId'] != '') {  
            $userTokenDb = $this->valToken->getTokenFromDb($_SESSION['userId']);
            if($userTokenDb == $_SESSION['userLoggedInToken']) {
        
                $params = $request->getParsedBody();
                $image = $_FILES['image'];
                $img_name = $image['name'];
                $img_path = $image['tmp_name'];
                $dest = __DIR__."/../img/users/".$img_name;
                move_uploaded_file($img_path, $dest);

                $name = trim($params['name'] ?? '');
                $address = trim($params['address'] ?? '');
                $updateRst =  $this->userModelObj->updateProfile($dest, $name, $address, $_SESSION['userId']);
                if($updateRst) {
                    $jsonMessage = array("isSuccess" => true,
                    "message" => "Profile updated Successfully.");
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                    ->withHeader("content-type", "application/json")
                    ->withStatus(401);
                }

                } else {
                    $jsonMessage = array("isSuccess" => false,
                    "message" => "Invalid request, Token not matching");
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                    ->withHeader("content-type", "application/json")
                    ->withStatus(401);
        
                }
        } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "User not logged in, please login first.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }
    }

    


}