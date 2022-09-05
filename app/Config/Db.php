<?php

namespace App\Config;

class Db
{
    private $conn;

    public function __construct()
    {
        $servername = "localhost";
        $username = "amn";
        $password = "N0p@sword";
        $dbname = "bookexchange";
        // $servername = "localhost";
        // $username = "root";
        // $password = "";
        // $dbname = "bookapi";

        $this->conn = mysqli_connect($servername, $username, $password, $dbname);
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
