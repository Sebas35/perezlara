<?php

namespace Core;

use PDO;
use PDOException;

class Connection
{
    private string $driver;
    private int $port;
    private string $hostname;
    private string $database;
    private string $username;
    private string $password;

    public function __construct()
    {
        $this -> driver = $_ENV['DB_CONNECTION'];
        $this -> hostname = $_ENV['DB_HOST'];
        $this -> port = $_ENV['DB_PORT'];
        $this -> database = $_ENV['DB_DATABASE'];
        $this -> username = $_ENV['DB_USERNAME'];
        $this -> password = $_ENV['DB_PASSWORD'];
        print_r($this -> driver);
        print_r($this -> hostname);
        print_r($this -> database);
        print_r($this -> username);
        print_r($this -> password);
    }

    public function connect(): PDO
    {
        try {
            $connection = new PDO(
                $this -> driver . ':host=' . $this -> hostname . ';port=' . $this -> port . ';dbname=' . $this ->
                database,
                $this -> username,
                $this -> password,
                options: [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException) {
            $connection = false;
        }
        return $connection;
    }
}
