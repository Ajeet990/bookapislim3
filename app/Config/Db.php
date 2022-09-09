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

        // $servername = "182.76.151.197";
        // $username = "amn";
        // $password = "N0p@sword";
        // $dbname = "bookexchange";

        // $servername = "127.0.0.1";
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
