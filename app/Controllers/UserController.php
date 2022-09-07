<?php

namespace App\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\GetTokenFromDb\GetToken;
use App\Token\GenToken;

// session_start();

class UserController
{
    protected $conn;
    protected $valToken;
    protected $userModelObj;
    protected $token;
    public function __construct($userModelObj, $conn)
    {
        $this->conn = $conn;
        $this->valToken = new GetToken($this->conn);
        $this->userModelObj = $userModelObj;
        $this->token = new GenToken();
    }

    public function checkEmailAndMobileExists($email, $mobile_no)
    {
        $valQry = $this->conn->query("select * from register where email = '$email' or mobile_no = '$mobile_no'");
        $num = mysqli_num_rows($valQry);
        if ($num > 0) {
            return false;
        } else {
            return true;
        }
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
                                
        $name = trim($params['name'] ?? '');
        $mobile_no = trim($params['mobile_no'] ?? '');
        $address = trim($params['address'] ?? '');
        $email = trim($params['email'] ?? '');
        $password = trim($params['password'] ?? '');
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        if (strlen($mobile_no) != 10) {
            $jsonMessage = array("isSuccess" => false,
            "message" => "Mobile number should be of 10 digits.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }

        $validation = $this->checkEmailAndMobileExists($email, $mobile_no);
        if ($validation) {
            if (isset($_FILES['image']) && strlen($_FILES['image']['name']) != 0) {
                $allowedExt = ['image/png', 'image/jpg', 'image/jpeg'];
                if (in_array($_FILES['image']['type'], $allowedExt)) {
                    $image = $_FILES['image'];
                    $img_name = $image['name'];
                    $img_path = $image['tmp_name'];
                    $dest = __DIR__."/../img/users/".$img_name;
                } else {
                    $jsonMessage = array("isSuccess" => false,
                    "message" => "Only images are allowed.");
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                    ->withHeader("content-type", "application/json")
                    ->withStatus(200);
                }
            } else {
                $jsonMessage = array("isSuccess" => false,
                "message" => "Please upload image for User.");
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(200);
            }
            
            $signUpRst = $this->userModelObj->signUp($name, $mobile_no, $address, $email, $hashed_password, $dest);
            if ($signUpRst) {
                move_uploaded_file($img_path, $dest);
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
                "message" => "Something error occured.");
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
        if ($loginRst) {
            if (password_verify($password, $loginRst[0])) {
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
            } else {
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
    }

    public function index(){
	$file = file("/var/log/apache2/domains/bookexchange.oidea.xyz.error.log");
	for ($i = max(0, count($file)-6); $i < count($file); $i++) {
  		echo $file[$i] . "\n";
	}
   	echo "Index Working";
    }
    public function updateProfile(Request $request, Response $response)
    {
        
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

    }
}
