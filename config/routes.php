<?php

return array(
    'user/signup' => 'user/signup',
    'user/signin' => 'user/signin',
    'task/done/([0-9]+)' => 'task/done/$1',
    'task/delete/([0-9]+)' => 'task/delete/$1',
    'task/create' => 'task/create',
    'task/([0-9]+)' => 'task/view/$1',
    'task' => 'task/index',
    'user' => 'user/index',
    '' => 'main/main',
);
