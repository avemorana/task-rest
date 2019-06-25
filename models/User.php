<?php

require_once 'components/Database.php';

class User
{
    public static function getAllUsers()
    {
        $db = Database::getConnection();

        $stmt = $db->prepare('SELECT * FROM users');
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    public static function addUser($email, $password)
    {
        $db = Database::getConnection();

        $stmt = $db->prepare('INSERT INTO users (email, password) VALUES (?, ?)');

        $stmt->bindParam('1', $email);
        $stmt->bindParam('2', password_hash($password, PASSWORD_DEFAULT));
        $stmt->execute();

        return $db->lastInsertId();
    }

    public static function isNewEmail($email)
    {
        $db = Database::getConnection();

        $stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->bindParam('1', $email);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($rows) > 0) {
            return false;
        }
        return true;
    }

    public static function checkSignInData($email, $password)
    {
        $db = Database::getConnection();

        $stmt = $db->prepare('SELECT user_id, password FROM users WHERE email = ?');
        $stmt->bindParam('1', $email);
        $stmt->execute();
        $hash = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($password, $hash['password'])){
            return $hash['user_id'];
        }
        return false;
    }
}