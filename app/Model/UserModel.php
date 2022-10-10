<?php
namespace App\Model;
class UserModel
{
    public const STATUS = 'active';
    public const TOKEN = '0';
    public const USER_TYPE = 0; //for normal user

    protected $conn;
    
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function checkEmailAndMobileExists(string $email, string $mobile_no) 
    {
        $checkNumEmailExists = $this->conn->prepare("select * from register where email = ? or mobile_no = ?");
        $checkNumEmailExists->bind_param("ss", $email, $mobile_no);
        $checkNumEmailExists->execute();
        $num = $checkNumEmailExists->get_result()->num_rows;
        if ($num > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function checkUserLoggedIn(string $param)
    {
        if (strlen($param) == 10) {
            $checkLoggedInStmt = $this->conn->prepare("select * from register where mobile_no = ?");
        } else {
            $checkLoggedInStmt = $this->conn->prepare("select * from register where id = ?");
        }
        $checkLoggedInStmt->bind_param("s", $param);
        $checkLoggedInStmt->execute();
        $checkLoggedInRst = $checkLoggedInStmt->get_result();
        $loggedInUserDetail = $checkLoggedInRst->fetch_assoc();
        $loggedInUserToken = $loggedInUserDetail['token'];
        if($loggedInUserToken == '') {
            return null;
        } else {
            return $loggedInUserToken;
        }
    }

    public function listUser() : array
    {
        $userQuery = $this->conn->prepare("select * from register");
        $userQuery->execute();
        $userList = $userQuery->get_result()->fetch_all(MYSQLI_ASSOC);
        return $userList;
    }

    public function signUp(
        string $name,
        string $mobile_no,
        string $address,
        string $email,
        string $password,
        string $userImg,
        string $token
    ) : bool
    {
        $status = UserModel::STATUS;
        $userType = UserModel::USER_TYPE;
        $signUpStmt = $this->conn->prepare("insert into register (image, user_name, mobile_no, address, email, password, status, token, user_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $signUpStmt->bind_param("ssssssssi", $userImg, $name, $mobile_no, $address, $email, $password, $status, $token, $userType);
        $signUpRst = $signUpStmt->execute();
        if ($signUpRst) {
            return true;
        } else {
            return false;
        }
    }

    public function logInAtSignUp(string $mobile_no) 
    {
        $getUserDetailStmt = $this->conn->prepare("SELECT * from register where mobile_no = ?");
        $getUserDetailStmt->bind_param("s", $mobile_no);
        $getUserDetailStmt->execute();
        $lastId = (int)$getUserDetailStmt->insert_id;
        $getUserDetailsRst = $getUserDetailStmt->get_result();
        $logInDetails = $getUserDetailsRst->fetch_assoc();
        if ($getUserDetailsRst->num_rows > 0) {
            return [$logInDetails['id'], $logInDetails['user_name']];
        } else {
            return false;
        }
    }

    public function logIn(string $mobile_no,string $password)
    {
        $loginQry = $this->conn->prepare("Select * from register where mobile_no = ?");
        $loginQry->bind_param("s", $mobile_no);
        $loginQry->execute();
        $loginRst = $loginQry->get_result();
        $loginValues = $loginRst->fetch_assoc();
        if($loginRst->num_rows > 0)
        {
            return [$loginValues['password'], $loginValues['id'], $loginValues['user_name']];
        } else {
            return false;
        }
    }

    public function addToken(string $mobile_no,string $tok_val) : bool
    {
        $addTokenQry = $this->conn->prepare("update register set token = ? where mobile_no = ?");
        $addTokenQry->bind_param("ss", $tok_val, $mobile_no);
        $addTokenQry->execute();
        return true;
    }

    public function removeToken(string $userId) : bool
    {
        $tokenVal = '';
        $removeTokenRst = $this->conn->prepare("update register set token = ? where id = ?");
        $removeTokenRst->bind_param("ss", $tokenVal, $userId);
        $removeTokenRst->execute();
        return true;
    }

    public function updateProfile(string $image, string $name,string $address, String $email, String $mobile_no, int $user_id) : bool
    {
        $updateQry = $this->conn->prepare("UPDATE register set image = ?, user_name = ?, address = ?, email = ?, mobile_no = ? where id = ?");
        $updateQry->bind_param("sssssi", $image, $name, $address, $email, $mobile_no, $user_id);
        $updateQry->execute();
        return true;
    }

    public function checkUserExists(int $param)
    {
        if (strlen($param) == 10) {
            $checkUserExistStmt = $this->conn->prepare("select * from register where mobile_no = ?");
        } else {
            $checkUserExistStmt = $this->conn->prepare("select * from register where id = ?");
        }
        $checkUserExistStmt->bind_param("i", $param);
        $checkUserExistStmt->execute();
        $exists = $checkUserExistStmt->get_result();
        if ($exists->num_rows > 0) {
            return true;
        } else {
            return false;
        }

    }

    public function getUser(int $userId)
    {
        $getUserStmt = $this->conn->prepare("select * from register where id = ?");
        $getUserStmt->bind_param("i", $userId);
        $getUserStmt->execute();
        $user = $getUserStmt->get_result();
        if ($user->num_rows > 0) {
            $rst = $user->fetch_assoc();
            return $rst;
        } else {
            return false;
        }
    }

    public function updatePassword(string $mobile_no, string $hashed_password)
    {
        $updatePassStmt = $this->conn->prepare("UPDATE register set password = ? where mobile_no = ?");
        $updatePassStmt->bind_param("ss", $hashed_password, $mobile_no);
        $updateRst = $updatePassStmt->execute();
        if ($updateRst) {
            return true;
        } else {
            return false;
        }

    }

    public function setOtp(String $user_id)
    {
        $otp = (int)rand(1000,9999);
        $setOtpStmt = $this->conn->prepare("UPDATE register set otp = ? where id = ?");
        $setOtpStmt->bind_param("is", $otp, $user_id);
        $setOtpStmt->execute();
        return $otp;
    }

    public function getOtp(String $user_id)
    {
        $getOtpStmt = $this->conn->prepare("SELECT * from register where id = ?");
        $getOtpStmt->bind_param("s", $user_id);
        $getOtpStmt->execute();
        $getOtpRst = $getOtpStmt->get_result();
        $dbOtp = $getOtpRst->fetch_assoc();
        return $dbOtp['otp'];
    }

    public function getLang()
    {
        $getLangStmt = $this->conn->prepare("SELECT * from language");
        $getLangStmt->execute();
        $getLangRst = $getLangStmt->get_result();
        $getLang = $getLangRst->fetch_all(MYSQLI_ASSOC);
        return $getLang;
    }
    public function getGenre()
    {
        $getGenreStmt = $this->conn->prepare("SELECT * from genre");
        $getGenreStmt->execute();
        $getGenreRst = $getGenreStmt->get_result();
        $getGenre = $getGenreRst->fetch_all(MYSQLI_ASSOC);
        return $getGenre;
    }
}
