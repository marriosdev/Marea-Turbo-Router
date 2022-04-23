<?php

require("vendor/autoload.php");

require_once("Controller.php");

use Controller;
use Marrios\Router\Router;

$route = new Router();

$route->set("POST",  "/function/{teste}", [function($e){echo $e->teste;}]);
$route->set("POST",  "/class/{teste}", [Controller::class, "index"]);

$route->run();