<?php

namespace Marrios\Router;

use Marrios\Router\Entities\Parameter;
use Marrios\Router\Entities\RouteParameters;
use Marrios\Router\Entities\Url;
use Marrios\Router\ErrorMessage;
use Marrios\Router\Exceptions\RouterException;

class Router {

    /**
     * @var Array
     */
    private $definedRoute;

    /**
     * @var String
     */
    private $currentUrl;

    /**
     */
    const HTTP_VERBS = "POST|GET|PUT|PATCH|DELETE|OPTIONS|HEAD";

    /**
     * Function that registers the routes
     * 
     * @param String $verb      | HTTP Method   -> "POST|GET|PUT|PATCH|DELETE|OPTIONS|HEAD"
     * @param String $path      | Defined route -> "/test/image/"
     * @param Array  $execute   | Action that will be performed when there is a request in the route
     * 
     * @return Object Marrios\Router\Router; 
     */
    public function set(String $verb, String $path, Array $execute)
    {        
        unset($this->definedRoute);
        $this->currentUrl = $this->_clearUrl($_SERVER["REQUEST_URI"]);
        $this->_checkverb($verb);

        $this->definedRoute["route"] = [
            "path"       => $path,
            "verb"       => $verb,           
        ];

        if(count($execute)==0)
        {
            throw new RouterException("Third parameter invalid. You must pass a callBack function or a controller"); 
        }
        
        if(is_callable($execute[0])){
            $this->definedRoute["route"] += [
                "callBack" => $execute[0]
            ];
        }
        if(class_exists($execute[0])){
            if(!isset($execute[1]))
            {
                $execute[1] = "index";
            }
            $this->definedRoute["route"] += [
                "controller" => [
                    "class"     => $execute[0],
                    "method"    => $execute[1]
                ]
            ];
        }else{
            throw new RouterException("Controller not found: ". $execute[0]);
        }
        return $this;
    }

    /**
     * Processing routes
     */
    public function run()
    {
        $this->middlewareVerify;

        $currentUrl   = $this->currentUrl;        
        $currentUrl   = $this->_clearUrl($currentUrl);
        $definedRoute = $this->definedRoute["route"] ; 
        $url          = $definedRoute["path"];
        $url          = $this->_clearUrl($url);

        // Checking if the current URL matches the defined route
        if(strtoupper($definedRoute["verb"]) == $_SERVER["REQUEST_METHOD"]){
            
            $urlsMatched = $this->_matched(new Url($currentUrl), new Url($url));
            
            // If it matches, let's run it
            if($urlsMatched){
                
                // If there is any dynamic parameter defined in the route, we will get these this->definedRoutes
                $urlParams = $this->_getUrlParams($url);
                
                // Running
                $this->_execute($definedRoute, $urlParams);

                //closing
                exit;
            }
        }
    }

    /**
     * Checks if the method passed in the defined route is valid
     * 
     * @param String $verb
     * @return \Exception|Bool
     */
    private function _checkVerb(String $verb)
    {
        if(!preg_match("/".strtoupper($verb)."/", Router::HTTP_VERBS))
        {
            throw new \Exception('Invalid HTTP method: "'.$verb.'"');
        }
        return true;
    }

    /**
     * Executing the callBack function passed in the route
     * 
     * @param \Closure $process | Callback function
     * @param \Marrios\Router\Entities\RouteParameters | Route parameters
     * 
     * @return Mixed
     */
    private function _runCallBack(\Closure $callBack, RouteParameters $routeParams)
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
    private function _runController(Array $process, RouteParameters $routeParams)
    {
        $controller = new $process["class"]();
        $method = $process["method"];

        if(!method_exists($controller, $method))
        {
            return throw new RouterException("Controller method not found: ". $method);
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
    private function _execute(Array $process, RouteParameters $routeParams)
    {
        if(isset($process["callBack"]))
        {
            $this->_runCallBack($process["callBack"], $routeParams);
            return true;
        }
        
        if(isset($process["controller"]))
        {
            $this->_runController($process["controller"], $routeParams);
            return true;
            
        }
        throw new RouterException("An error occurred while performing this action");
    }

    /**
     * This function removes the "/" from the beginning 
     * of the string and from the end of the passed string
     * 
     * @example [
     * _clearUrl("/site/image/") -> "site/image" 
     * 
     * @param String $url
     * @return String $url
     */
    private function _clearUrl(String $url)
    {
        if($url[0] == "/"){
            $url[0] = " ";
            $url    = trim($url);
        }

        if($url[strlen($url)-1] == "/"){
            $url[strlen($url)-1] = " ";
            $url                 = trim($url);
        }

        return $url;
    }

    /**
     * Checking if the current route matches the route passed in the function
     * 
     * @example [
     * We have these this->definedRoutes
     * 
     * $currentUrl -> "site/image/123 
     * $definedUrl -> "site/{name}/{id}
     * 
     * the function return will be: true
     * 
     * $currentUrl -> "site/image/123 
     * $definedUrl -> "site/{name}/{id}/token
     * 
     * the function return will be: false
     * 
     * @param Marrios\Router\Entities\Url  $currentUrl
     * @param Marrios\Router\Entities\Url  $definedUrl
     */
    private function _matched(Url $currentUrl, Url $definedUrl)
    {
        $currentUrl = explode("/", $currentUrl->get());
        $definedUrl = explode("/", $definedUrl->get());
        
        if(count($currentUrl) != count($definedUrl)){
            return false;
        }

        $countMatch = count($definedUrl);

        for($i=0; $i < $countMatch; $i++){
            if($definedUrl[$i] !=  $currentUrl[$i]){
                /**
                 * Checking if this parameter the parameter is dynamic
                 * If it is dynamic, we disregard the difference
                 */
                if(!preg_match("/[{}]/", $definedUrl[$i])){
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * This function returns all dynamic this->definedRoutes ​​passed in the URL
     * 
     * @example [
     * URL passed     ->  "/site/image/1234"
     * Defined route  ->  "/site/{name}/{id}"
     * 
     * $paramList->name = "image"
     * $paramList->id   = "1234"
     * 
     * @param String $url
     * @return \Marrios\Router\Entities\RouteParameters $paramList
     */
    private function _getUrlParams(String $url)
    {
        $url        = explode("/", $url);
        $currentUrl = explode("/", $this->currentUrl);

        $paramsList = new RouteParameters();
        
        for($i=0; $i < count($url); $i++){
            $definedParam = new Parameter($url[$i]);
            $currentParam = new Parameter($currentUrl[$i]);

            if($definedParam->valid()){
                $paramsList->setParameter($definedParam, $currentParam);
            }
        }
        return $paramsList;
    }   
}
