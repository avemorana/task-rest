<?php

class MainController
{
    public function actionMain()
    {
        $data = array(
            'users' => [
                [
                    'id' => 1,
                    'name' => 'jane'
                ],
                [
                    'id' => 2,
                    'name' => 'peter'
                ],
                [
                    'id' => 3,
                    'name' => 'monika'
                ],
            ]
        );
        echo json_encode($data);
        return true;
    }
}