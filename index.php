<?php

require_once("vendor/autoload.php");
use Marrios\Router\HttpRouter;

$router = new HttpRouter();

$router->post("/function",      [function(){
    echo "POST";
}])->run();

// $router->get("/function",       [Controller::class, "index"])->run();
$router->delete("/function",    [function(){echo "DELETE";}])->run();
$router->put("/function",       [function(){echo "PUT";}])->run();
$router->patch("/function",     [function(){echo "PATCH";}])->run();
$router->head("/function",      [function(){echo "HEAD";}])->run();
$router->options("/function",   [function(){echo "OPTIONS";}])->run();

$router->notFound();
