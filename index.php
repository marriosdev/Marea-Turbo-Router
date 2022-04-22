<?php

require_once("Router.php");
require_once("Controller.php");

use Controller;
$route = new Router();


// Valores entre {} são valores dinamicos
$route->set("POST", "/teste/{dsad}/marrios/", [Controller::class, "index"]);

$route->run();