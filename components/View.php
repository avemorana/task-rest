<?php

class View
{
    public static function showJSON($data)
    {
        echo json_encode($data);
        return true;
    }
}
