<?php

namespace App\Config;

class Db
{
    private $conn;

    public function __construct()
    {
        $servername = "127.0.0.1";
        $username = "amn";
        $password = "N0p@sword";
        $dbname = "bookexchange";

        $this->conn = mysqli_connect($servername, $username, $password, $dbname);
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
