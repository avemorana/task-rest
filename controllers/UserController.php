<?php

require_once 'models/User.php';
require_once 'models/Token.php';
require_once 'components/View.php';

class UserController
{
    public function actionIndex()
    {
        $users = User::getAllUsers();
        return View::showJSON($users);
    }

    public function actionSignup()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return View::showJSON(['status' => 'error']);
        }

        $email = strtolower(htmlspecialchars($data['email']));
        $password = htmlspecialchars($data['password']);

        $checkEmailResult = $this->checkEmail($email);
        if ($checkEmailResult) {
            return View::showJSON($checkEmailResult);
        }

        $userId = User::addUser($email, $password);
        if (!$userId) {
            return View::showJSON(['status' => 'user not created']);
        }
        $token = Token::createToken($userId);
        if (!isset($token['token']) || !isset($token['expires_in'])) {
            return View::showJSON(['status' => 'token not created']);
        }
        return View::showJSON([
            'status' => 'ok',
            'user_id' => $userId,
            'token' => $token['token'],
            'expires_in' => $token['expires_in']
        ]);
    }

    public function actionSignin()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return View::showJSON(['status' => 'error']);
        }

        $email = strtolower(htmlspecialchars($data['email']));
        $password = htmlspecialchars($data['password']);

        $userId = User::checkSignInData($email, $password);
        if (!$userId){
            return View::showJSON(['status' => 'Data in invalid']);
        }

        $token = Token::createToken($userId);
        if (!isset($token['token']) || !isset($token['expires_in'])) {
            return View::showJSON(['status' => 'token not created']);
        }
        return View::showJSON([
            'status' => 'ok',
            'token' => $token['token'],
            'expires_in' => $token['expires_in']
        ]);
    }

    public function checkEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'Email is incorrect'];
        }
        if (!User::isNewEmail($email)) {
            return ['status' => 'This email already exists'];
        }
        return null;
    }
}
