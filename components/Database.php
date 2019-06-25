<?php

class Database
{
    private static $paramsPath = 'config/db_params.php';
    private static $params;

    public static function getConnection()
    {
        self::$params = include(self::$paramsPath);
        try {
            $pdo = new PDO("mysql:host=" . self::$params['host'] . ";dbname=" . self::$params['dbname'],
                self::$params['user'], self::$params['password']);
        } catch (PDOException $e){
            print "Error! " . $e->getMessage() . "<br>";
            die();
        }

        return $pdo;
    }
}