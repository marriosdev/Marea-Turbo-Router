<?php

namespace Marrios\Router;

use Marrios\Router\Entities\Parameter;
use Marrios\Router\RouteParameters;
use Marrios\Router\Entities\Url;
use Marrios\Router\ErrorMessage;

class Router {

    use ErrorMessage;

    private $definedRoutes;

    private $currentUrl;
    
    const HTTP_VERBS = "POST|GET|PUT|PATCH|DELETE|OPTIONS|HEAD";

    const METHOD_PROCESS     = 1;
    
    const CLASS_PROCESS      = 0;
    
    const FUNCTION_PROCESS   = 0;

    /**
     * 
     */
    public function __construct()
    {
        $this->currentUrl = $this->_clearUrl($_SERVER["REQUEST_URI"]);
    }

    /**
     * Function that registers the routes
     * 
     * @param String $verb      | HTTP Method   -> "POST|GET|PUT|PATCH|DELETE|OPTIONS|HEAD"
     * @param String $path      | Defined route -> "/test/image/"
     * @param Array  $execute   | Action that will be performed when there is a request in the route
     */
    public function set(String $verb, String $path, Array $execute)
    {
        $this->definedRoutes[] = [
            "path"       => $path,
            "verb"       => $verb,    
            "execute"    => $execute       
        ];
    }

    /**
     * Processing routes
     */
    public function run()
    {
        try {
     
            // Getting current URL from request
            $currentUrl = $this->currentUrl;        
            $currentUrl = $this->_clearUrl($currentUrl);

            $notFound = true;

            // Going through all defined routes
            foreach($this->definedRoutes as $key=>$value)
            {
                $this->_checkverb($value["verb"]);

                // Taking a defined route
                $url = $value["path"];
                $url = $this->_clearUrl($url);

                // Checking if the current URL matches the defined route
                if(strtoupper($value["verb"]) == $_SERVER["REQUEST_METHOD"]){
                    $urlsMatched = $this->_matched(new Url($currentUrl), new Url($url));
                    
                    // If it matches, let's run it
                    if($urlsMatched){
                        /**
                         *  If there is any dynamic parameter defined in the route, we will get these values
                         */
                        $urlParams = $this->_getUrlParams($url);

                        // Running
                        $this->_executeMethod($value["execute"], $urlParams);
                        $notFound = false;
                    }
                }
            }
        } catch (\Exception $e) {
            
            $this->renderErrorMessage($e);
        }

        if($notFound){
            echo "404 NOT FOUND";
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
            return throw new \Exception('Invalid HTTP method: "'.$verb.'"');
        }
        return true;
    }

    /**
     * Function that executes the route
     * 
     * @param $process | 
     * @route
     * @return bool|\Exception
     */
    private function _executeMethod($process, $routeParams)
    {
        // Checking if the passed parameter is a function
        if(is_callable($process[Router::CLASS_PROCESS]))
        {
            return $process[Router::FUNCTION_PROCESS]($routeParams);
        }

        if(isset($process[Router::CLASS_PROCESS])){
            $class   = $process[Router::CLASS_PROCESS];
            
            if(isset($process[Router::METHOD_PROCESS])){
                $method  = $process[Router::METHOD_PROCESS];
            }else{
                return throw new \Exception("You must pass a method from the class: {$class}");
            }
        }else{
            return throw new \Exception("You must pass a function or a class and a method");    
        }

        if(class_exists("{$class}"))
        {
            $class = new $class();
            
            if(method_exists($class, $method)){
                if($routeParams->count > 0){
                    $class->$method($routeParams);
                    return true;
                }
                $class->$method();
                return true;
            }
            return throw new \Exception("Method not found: {$method}");
        }
        return throw new \Exception("Controller not found: {$class}");
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
     * 
     * We have these values
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
     * This function returns all dynamic values ​​passed in the URL
     * 
     * @example [
     * 
     * URL passed     ->  "/site/image/1234"
     * Defined route  ->  "/site/{name}/{id}"
     * 
     * $paramList->name = "image"
     * $paramList->id   = "1234"
     * 
     * @param String $url
     * @return Marrios\Router\RouteParameters $paramList
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
