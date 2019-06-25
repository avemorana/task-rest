<?php

require_once 'models/Task.php';
require_once 'models/Token.php';
require_once 'components/View.php';

class TaskController
{
    public function actionView($taskId)
    {
        $checkAccessResult = $this->checkAccess($_GET);
        if ($checkAccessResult) {
            return View::showJSON($checkAccessResult);
        }

        $task = Task::getTaskById($taskId);
        if (!$task) {
            $task = ['status' => 'Not found'];
        }
        return View::showJSON($task);
    }

    public function actionIndex()
    {
        $checkAccessResult = $this->checkAccess($_GET);
        if ($checkAccessResult) {
            return View::showJSON($checkAccessResult);
        }
        $page = (int)htmlspecialchars($_GET['page']);
        if (!isset($page) || !is_int($page)){
            $page = 1;
        }
        $option = htmlspecialchars($_GET['sort_option']);
        $order = htmlspecialchars($_GET['sort_order']);

        $tasks = Task::getAllTasks($option, $order, $page);
        return View::showJSON($tasks);
    }

    public function actionCreate()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $checkAccessResult = $this->checkAccess($data['auth']);
        if ($checkAccessResult) {
            return View::showJSON($checkAccessResult);
        }

        if (!isset($data['title']) || !isset($data['due_date']) || !isset($data['priority'])) {
            return View::showJSON(['status' => 'error']);
        }
        $title = htmlspecialchars($data['title']);
        $dueDate = htmlspecialchars($data['due_date']);
        $priority = htmlspecialchars($data['priority']);

        $taskId = Task::createTask($title, $dueDate, $priority, htmlspecialchars($data['auth']['user_id']));
        if (!$taskId) {
            return View::showJSON(['status' => 'error']);
        }
        return View::showJSON([
            'status' => 'ok',
            'task_id' => $taskId
        ]);
    }

    public function actionDelete($taskId)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $checkAccessResult = $this->checkAccess($data['auth']);
        if ($checkAccessResult) {
            return View::showJSON($checkAccessResult);
        }
        $result = Task::deleteTask($taskId);
        $json = ($result) ? ['status' => 'ok'] : ['status' => 'error'];
        return View::showJSON($json);
    }

    public function actionDone($taskId)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $checkAccessResult = $this->checkAccess($data['auth']);
        if ($checkAccessResult) {
            return View::showJSON($checkAccessResult);
        }
        $result = Task::setTaskDone($taskId);
        $json = ($result) ? ['status' => 'ok'] : ['status' => 'error'];
        return View::showJSON($json);
    }

    public function checkToken($token, $userId)
    {
        $tokenFromDb = Token::getTokenByUserId($userId);
        if (!$tokenFromDb || $tokenFromDb['token_value'] !== $token || Token::getExpiresInByToken($token) < time()) {
            return ['status' => 'Access denied'];
        }

        return null;
    }

    public function checkAccess($data)
    {
        if (!isset($data['token']) || !isset($data['user_id'])) {
            return ['status' => 'Access denied'];
        }
        $token = htmlspecialchars($data['token']);
        $userId = htmlspecialchars($data['user_id']);

        $checkTokenResult = $this->checkToken($token, $userId);

        if ($checkTokenResult) {
            return $checkTokenResult;
        }
        return null;
    }
}