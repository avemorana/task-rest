<?php

require_once 'components/Database.php';

class Token
{
    const TOKEN_LENGTH = 5;
    const TOKEN_LIFETIME = 60 * 60;

    public static function createToken($userId)
    {
        $db = Database::getConnection();

        $tokenByUser = self::getTokenByUserId($userId);
        if ($tokenByUser){
            $sql = 'UPDATE tokens SET token_value = ?, expires_in = ? WHERE user_id = ?';
        } else {
            $sql = 'INSERT INTO tokens (token_value, expires_in, user_id) VALUES (?, ?, ?)';
        }

        $token = self::generateToken();
        $expiresIn = time() + self::TOKEN_LIFETIME;

        $stmt = $db->prepare($sql);
        $stmt->bindParam('1', $token);
        $stmt->bindParam('2', $expiresIn);
        $stmt->bindParam('3', $userId);
        $stmt->execute();

        return ['token' => $token, 'expires_in' => $expiresIn];
    }

    public static function generateToken()
    {
        return bin2hex(random_bytes(self::TOKEN_LENGTH));
    }

    public static function getExpiresInByToken($token)
    {
        $db = Database::getConnection();

        $stmt = $db->prepare('SELECT expires_in FROM tokens WHERE token_value = ?');
        $stmt->bindParam('1', $token);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['expires_in'];
    }

    public static function getTokenByUserId($userId)
    {
        $db = Database::getConnection();

        $stmt = $db->prepare('SELECT * FROM tokens WHERE user_id = ?');
        $stmt->bindParam('1', $userId);
        $stmt->execute();
        $rows = $stmt->fetch(PDO::FETCH_ASSOC);
        return $rows;
    }
}