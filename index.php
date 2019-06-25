<?php
define('ROOT', dirname(__FILE__));
require_once (ROOT . '/components/Router.php');

$router = new Router();
$router->run();