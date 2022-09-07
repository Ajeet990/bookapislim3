<?php
namespace App\Model;
class UserModel
{
    protected $conn;
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

    public function signUp(string $name, string $mobile_no, string $address, string $email, string $password, string $dest)
    {
        $sql = "INSERT INTO register (image, user_name, mobile_no, address, email, password) VALUES ('$dest', '$name', '$mobile_no', '$address', '$email', '$password')";
        if ($this->conn->query($sql) === true) {
            return true;
        } else {
            return false;
        }
    }

    public function logIn(string $mobile_no,string $password)
    {
        $loginQry = $this->conn->query("Select * from register where mobile_no = '$mobile_no'");      
        $num = mysqli_num_rows($loginQry);
        if($num > 0)
        {
            $row = mysqli_fetch_assoc($loginQry);
            return [$row['password'], $row['id'], $row['user_name']];
        }
    }

    public function addToken($mobile_no, $tok_val)
    {
        $addTokenQry = $this->conn->query("update register set token = '$tok_val' where mobile_no = '$mobile_no'");
        return $addTokenQry;

    }

    public function removeToken($userMobile)
    {
        $removeTokenRst = $this->conn->query("update register set token = '' where mobile_no = '$userMobile'");
        return true;
    }

    public function updateProfile(string $image, string $name,string $address,int $userId)
    {
        $updateQry = $this->conn->query("update register set image = '$image', user_name = '$name', address = '$address' where id = '$userId'");
        return true;
    }
}
