<?php

require_once 'components/Database.php';

class Task
{
    const AMOUNT_PER_PAGE = 3;

    public static function createTask($title, $dueDate, $priority, $userId)
    {
        $db = Database::getConnection();

        $stmt = $db->prepare('INSERT INTO tasks (title, due_date, priority, user_id) 
                                            VALUES (?, ?, ?, ?)');

        $stmt->bindParam('1', $title);
        $stmt->bindParam('2', $dueDate);
        $stmt->bindParam('3', $priority);
        $stmt->bindParam('4', $userId);
        $stmt->execute();
        return $db->lastInsertId();
    }

    public static function getAllTasks($option, $order, $page = 1)
    {
        $db = Database::getConnection();

        if ($order != 'asc' && $order != 'desc') {
            $order = 'asc';
        }
        if ($option != 'title' && $option != 'priority' && $option != 'due_date') {
            $sql_option = 'ORDER BY title ';
        } else {
            $sql_option = 'ORDER BY ' . $option . ' ';
        }

        $stmt = $db->prepare('SELECT * FROM tasks WHERE deleted = 0 ' . $sql_option . $order
            . ' LIMIT ' . self::AMOUNT_PER_PAGE . ' OFFSET ' . ($page - 1) * self::AMOUNT_PER_PAGE);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    public static function getTaskById($taskId)
    {
        $db = Database::getConnection();

        $stmt = $db->prepare('SELECT * FROM tasks WHERE task_id = ?');
        $stmt->bindParam('1', $taskId);
        $stmt->execute();
        $rows = $stmt->fetch(PDO::FETCH_ASSOC);

        return $rows;
    }

    public static function deleteTask($taskId)
    {
        $db = Database::getConnection();

        $stmt = $db->prepare('UPDATE tasks SET deleted = 1 WHERE task_id = ?');
        $stmt->bindParam('1', $taskId);
        return $stmt->execute();
    }

    public static function setTaskDone($taskId)
    {
        $db = Database::getConnection();

        $stmt = $db->prepare('UPDATE tasks SET done = 1 WHERE task_id = ?');
        $stmt->bindParam('1', $taskId);
        return $stmt->execute();
    }
}
