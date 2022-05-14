<?php

namespace Marrios\Router;

use Marrios\Router\Exceptions\RouterException;

class Route
{
    public $route; 

    public $routeAction;
    
    public $method;

    /**
     * 
     */
    public function __construct(String $method, String $route, Array $routeAction)
    {
        return $this->set($method, $route, $routeAction);
    }
    
    /**
     * Function that registers the routes
     * 
     * @param String $method        | HTTP Method   -> "POST|GET|PUT|PATCH|DELETE|OPTIONS|HEAD"
     * @param String $route         | Defined route -> "/test/image/"
     * @param Array  $routeAction   | Action that will be performed when there is a request in the route
     * 
     * @return Object Marrios\Router\Router; 
     */
    public function set(String $method, String $route, Array $routeAction)
    {        

        if(count($routeAction)==0)
        {
            throw new RouterException("Third parameter invalid. You must pass a callBack function or a controller"); 
        }
        
        if(is_callable($routeAction[0])){
            $this->routeAction = [
                "callBack" => $routeAction[0]
            ];
        }else{
            if(class_exists($routeAction[0])){
                if(!isset($routeAction[1]))
                {
                    $routeAction[1] = "index";
                }
                $this->routeAction= [
                    "controller" => [
                        "class"     => $routeAction[0],
                        "method"    => $routeAction[1]
                    ]
                ];
            }else{
                throw new RouterException("Controller not found: ". $routeAction[0]);
            }
        }

        $this->method = $method;
        $this->route = $route;

        return $this;
    }
}