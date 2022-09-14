<?php
namespace App\Model;
class UserModel
{
    public const STATUS = 'active';
    // public const TOKEN = '';
    public const USER_TYPE = 0; //for normal user

    protected $conn;
    
    public function __construct($conn)
    {
        $this->conn = $conn;
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
        string $dest,
        string $token
    ) : bool
    {
        $status = UserModel::STATUS;
        $userType = UserModel::USER_TYPE;
        $signUpStmt = $this->conn->prepare("INSERT INTO register 
        (image, user_name, mobile_no, address, email, password, status, token, user_type)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $signUpStmt->bind_param("ssssssssi", $dest, $name, $mobile_no, $address, $email, $password, $status, $token, $userType,);
        $signUpRst = $signUpStmt->execute();
        if ($signUpRst) {
            return true;
        } else {
            return false;
        }
    }

    public function logInAtSignUp(string $mobile_no, string $tok_val) 
    {

        $getUserDetailStmt = $this->conn->prepare("SELECT * from register where mobile_no = ?");
        $getUserDetailStmt->bind_param("s", $mobile_no);
        $getUserDetailStmt->execute();
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

    public function updateProfile(string $image, string $name,string $address,int $userId) : bool
    {
        $updateQry = $this->conn->prepare("update register set image = ?, user_name = ?, address = ? where id = ?");
        $updateQry->bind_param("sssi", $image, $name, $address, $userId);
        $updateQry->execute();
        return true;
    }
}
