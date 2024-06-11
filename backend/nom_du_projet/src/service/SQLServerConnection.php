<?php

namespace App\Service;

use PDO;
use PDOException;

class SQLServerConnection
{
    private $conn;

    public function __construct(string $password)
    {
        try {
            $this->conn = new PDO("sqlsrv:server = tcp:banque.database.windows.net,1433; Database=whatapp2", "yohan.davion", $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            print("Error connecting to SQL Server.");
            die(print_r($e));
        }
    }

    public function getPDOConnection()
    {
        return $this->conn;
    }

    public function closeConnection()
    {
        $this->conn = null;
    }
}