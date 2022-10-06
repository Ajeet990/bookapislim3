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

    public function userList(Request $request, Response $response)
    {
        $uList = $this->userModelObj->listUser();
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
        $userImgLink = '0';
        $params = $request->getParsedBody();
                                
        $name = trim($params['name'] ?? '');
        $mobile_no = trim($params['mobile_no'] ?? '');
        $address = trim($params['address'] ?? '');
        $email = trim($params['email'] ?? '');
        $password = trim($params['password'] ?? '');

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $validation = $this->userModelObj->checkEmailAndMobileExists($email, $mobile_no);
        if ($validation) {   
            if (isset($_FILES['image']) && strlen($_FILES['image']['name']) != 0) {
                $allowedExt = ['png', 'jpg', 'jpeg'];
                $path = $_FILES['image']['name'];
                $imgExt = pathinfo($path, PATHINFO_EXTENSION);

                if (in_array($imgExt, $allowedExt)) {
                    $image = $_FILES['image'];
                    $img_name = $image['name'];
                    $img_path = $image['tmp_name'];
                    $dest = __DIR__."/../img/users/".$img_name;
                    $userImgLink = "app/img/users/".$img_name;
                    move_uploaded_file($img_path, $dest);
                } else {
                    $jsonMessage = array("isSuccess" => false,
                    "message" => "Only images are allowed.",
                    "Token" => null,
                    "userId" => null);
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                    ->withHeader("content-type", "application/json")
                    ->withStatus(200);
                }
            }
            $tok_val = $this->token->genCSRFTkn();
            $signUpRst = $this->userModelObj->signUp($name, $mobile_no, $address, $email, $hashed_password, $userImgLink, $tok_val);
            if ($signUpRst) {
                $loginRst = $this->userModelObj->logInAtSignUp($mobile_no, $tok_val);
                if ($loginRst) {

                    $_SESSION['userLoggedInToken'] = $tok_val;
                    $_SESSION['userLoggedInMobile'] = $mobile_no;
                    $_SESSION['userId'] = $loginRst[0];
                    $_SESSION['userName'] = $loginRst[1];

                    $jsonMessage = array("isSuccess" => true,
                    "message" => "Registration success",
                    "Token" => $tok_val,
                    "userId" => $loginRst[0]);
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                    ->withHeader("content-type", "application/json")
                    ->withStatus(200);
                } else {
                    $jsonMessage = array("isSuccess" => false,
                    "message" => "Something error occured, during login.",
                    "Token" => null,
                    "userId" => null);
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                    ->withHeader("content-type", "application/json")
                    ->withStatus(200);
                }
            } else {
                $jsonMessage = array("isSuccess" => false,
                "message" => "Something error occured, during signUp",
                "Token" => null,
                "userId" => null);
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(200);
            } 
        } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "Phone or email already exists.",
            "Token" => null,
            "userId" => null);
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }    
    }

    public function logIn(Request $request, Response $response)
    {
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
                                        "Token" => $tok_val,
                                        "userId" => $loginRst[1]);
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(200);
            } else {
                    $jsonMessage = array("isSuccess" => false,
                                        "message" => "Password Wrong.",
                                        "Token" => null,
                                        "userId" => null);
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                    ->withHeader("content-type", "application/json")
                    ->withStatus(200);
            }
        } else {
                $jsonMessage = array("isSuccess" => false,
                "message" => "Mobile no is wrong.",
                "Token" => null,
                "userId" => null);
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(200);
        }
    }

    public function logOut(Request $request, Response $response, $args)
    {
        $userId = (int)$args['userId'];
        $userExistsRst = $this->userModelObj->checkUserExists($userId);
        if ($userExistsRst) {
            $this->userModelObj->removeToken($userId);
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
        } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "User not exists...",
            "Token" => null,
            "userId" => null);
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
        $userImgLink = '';
        $params = $request->getParsedBody();
        $name = trim($params['name'] ?? '');
        $address = trim($params['address'] ?? '');
        $email = trim($params['email'] ?? '');
        $mobile_no = trim($params['mobile_no'] ?? '');
        $user_id = (int)trim($params['user_id'] ?? '');

        $getUserImage = $this->userModelObj->getUser($user_id);
        $userImgLink = $getUserImage['image'];
        if (isset($_FILES['image']) && strlen($_FILES['image']['name']) != 0) {
            $allowedExt = ['png', 'jpg', 'jpeg'];
            $path = $_FILES['image']['name'];
            $imgExt = pathinfo($path, PATHINFO_EXTENSION);

            if (in_array($imgExt, $allowedExt)) {
                $image = $_FILES['image'];
                $img_name = $image['name'];
                $img_path = $image['tmp_name'];
                $dest = __DIR__."/../img/users/".$img_name;
                $userImgLink = "app/img/users/".$img_name;
                move_uploaded_file($img_path, $dest);
            } else {
                $jsonMessage = array("isSuccess" => false,
                "message" => "Only images are allowed.",
                "Token" => null,
                "userId" => null);
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(200);
            }
        }

        $updateRst =  $this->userModelObj->updateProfile($userImgLink, $name, $address, $email, $mobile_no, $user_id);
        if($updateRst) {
            $jsonMessage = array("isSuccess" => true,
            "message" => "Profile updated Successfully.",);
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "Profile not updated.",);
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }
    }

    public function getUserById(Request $request, Response $response, $args)
    {
        $userId = (int)$args['userId'];
        $userExists = $this->userModelObj->checkUserExists($userId);
        if ($userExists) {
            $getUserDetails = $this->userModelObj->getUser($userId);
            $jsonMessage = array("isSuccess" => true,
            "message" => "User details",
            "user" => $getUserDetails);
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "User not exists.",
            "user" => null);
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }
    }

    public function resetPassword(Request $request, Response $response)
    {
        $params = $request->getParsedBody();
        $mobile_no = trim($params['mobile_no'] ?? '');
        $password = trim($params['password'] ?? '');

        $userExists = $this->userModelObj->checkUserExists($mobile_no);
        if ($userExists) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $updatePass = $this->userModelObj->updatePassword($mobile_no, $hashed_password);
            if ($updatePass) {
                $jsonMessage = array("isSuccess" => true,
                "message" => "Password updated successfully.");
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(200);
            } else {
                $jsonMessage = array("isSuccess" => false,
                "message" => "Something went wrong.");
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(200);
            }         
        } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "Mobile number not registerd.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }

    }

    public function getOtp(Request $request, Response $response)
    {
        $params = $request->getParsedBody();
        $mobile_no = trim($params['mobile_no'] ?? '');
        $userId = trim($params['user_id'] ?? '');
        $userExists = $this->userModelObj->checkUserExists($userId);
        if ($userExists) {
            $otp = $this->userModelObj->setOtp($userId);
            $jsonMessage = array("isSuccess" => true,
            "message" => "OTP generated.",
            "OTP" => $otp);
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "User does't exists.",
            "OTP" => null);
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }
    }

    public function verifyOtp(Request $request, Response $response)
    {
        $params = $request->getParsedBody();
        $userOtp = trim($params['otp'] ?? '');
        $userId = trim($params['user_id'] ?? '');
        $dbOtp = $this->userModelObj->getOtp($userId);
        if ($userOtp == $dbOtp) {
            $jsonMessage = array("isSuccess" => true,
            "message" => "OTP verified.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "Wrong OTP.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }
    }
    public function getLanguage(Request $request, Response $response)
    {
        $lang = $this->userModelObj->getLang();
        $jsonMessage = array("isSuccess" => true,
        "message" => "List of languages.",
        "languages" => $lang);
        $response->getBody()->write(json_encode($jsonMessage));
        return $response
        ->withHeader("content-type", "application/json")
        ->withStatus(200);

    }
    public function getGenre(Request $request, Response $response)
    {
        $genre = $this->userModelObj->getGenre();
        $jsonMessage = array("isSuccess" => true,
        "message" => "List of Genres.",
        "genres" => $genre);
        $response->getBody()->write(json_encode($jsonMessage));
        return $response
        ->withHeader("content-type", "application/json")
        ->withStatus(200);

    }
}
