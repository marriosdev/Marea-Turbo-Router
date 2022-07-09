<?php
require_once("vendor/autoload.php");
use Marrios\Router\HttpRouter;

$router = new HttpRouter();

// Set route
class Middleware {
    public function handle() 
    {
        return true;
    }    
}

class Controller{
    public function index() {
        echo "linda";
    }
}

$router->middlewareGroup(
    [Middleware::class], 
    [
        ["GET", "/", Controller::class], 
        ["GET", "/video", function(){ echo "Vídeos";}],
        ["GET", "/Hello", function(){ echo "Hello";}],
    ]
);

$router->notFound();