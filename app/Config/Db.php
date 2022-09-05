<?php

namespace App\Config;

class Db
{
    private $conn;

    public function __construct()
    {
        $servername = "localhost";
        $username = "root";
        $password = "Admin@123";
        $dbname = "bookapi";

        $this->conn = mysqli_connect($servername, $username, $password, $dbname);
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
