<?php

namespace Marrios\Router;

use Marrios\Router\Exceptions\RouterException;

trait RunnerTrait
{
    /**
     * Executing the callBack function passed in the route
     * 
     * @param \Closure $process | Callback function
     * @param \Marrios\Router\Entities\RouteParameters | Route parameters
     * 
     * @return Mixed
     */
    public function runCallBack(\Closure $callBack, \Marrios\Router\Entities\RouteParameters $routeParams)
    {
        try{
            return $callBack($routeParams);
        }catch(RouterException $e)
        {
            throw new RouterException($e->getMessage());
        } 
    }
    
    /**
     * Method that runs the controller
     * 
     * @param Array $process | Array containing the class and the method that will be executed
     * @param \Marrios\Router\Entities\RouteParameters | Route parameters
     * 
     * @return Mixed
     */
    public function runController(Array $process, \Marrios\Router\Entities\RouteParameters $routeParams)
    {
        $controller = new $process["class"]();
        $method = $process["method"];

        if(!method_exists($controller, $method))
        {
            throw new RouterException("Controller method not found: ". $method);
        }

        if($routeParams->count > 0)
        {
            return $controller->$method($routeParams);
        }

        return $controller->$method();
    }

    /**
     * Function that executes the route
     * 
     * @param $process | $routeParams 
     * 
     * @return bool|\Exception
     */
    public function execute(Array $process, \Marrios\Router\Entities\RouteParameters $routeParams)
    {

        if(isset($process["callBack"]))
        {
            $this->runCallBack($process["callBack"], $routeParams);
            return true;
        }
        
        if(isset($process["controller"]))
        {
            $this->runController($process["controller"], $routeParams);
            return true;
        }
        throw new RouterException("An error occurred while performing this action");
    }
}