<?php

class Database{
    const HOST = 'database';
    const DB_NAME = 'adv-db';
    const DB_USERNAME = 'root';
    const DB_PASSWORD = '1111';
    public $conn;
    public function connect()
    {
        try {
            $this->conn = new PDO("mysql:host=".self::HOST.";dbname=".self::DB_NAME."",self::DB_USERNAME, self::DB_PASSWORD);
            return $this->conn;
        } catch (PDOException $pe) {
            die("Could not connect to the database:" . $pe->getMessage());
        }
    }
}