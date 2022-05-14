<?php

namespace Marrios\Router;

use Marrios\Router\Entities\Parameter;
use Marrios\Router\Entities\RouteParameters;
use Marrios\Router\Entities\Url;
use Marrios\Router\Exceptions\RouterException;
use Marrios\Router\HttpMethods\Delete;
use Marrios\Router\HttpMethods\Get;
use Marrios\Router\HttpMethods\Head;
use Marrios\Router\HttpMethods\Options;
use Marrios\Router\HttpMethods\Patch;
use Marrios\Router\HttpMethods\Post;
use Marrios\Router\HttpMethods\Put;
use Marrios\Router\Route;
use Marrios\Router\RunnerTrait;
use Marrios\Router\Pages\NotFound;

class HttpRouter {

    use RunnerTrait, Post, Get, Put, Delete, Patch, Head, Options, NotFound;

    /**
     * @var \Marrios\Router\Route
     */
    public Route $definedRoute;

    /**
     * @var String
     */
    public $currentUrl;

    /**
     * 
     */
    public function __construct()
    {
        $this->currentUrl = $this->clearUrl($_SERVER["REQUEST_URI"]);
    }

    /**
     * Processing routes
     */
    public function run()
    {
        $currentUrl = $this->currentUrl;        
        $routePath  = $this->definedRoute->route; 
        $url        = $this->clearUrl($this->definedRoute->route);

        if($this->definedRoute->method == $_SERVER["REQUEST_METHOD"]){
            $urlsMatched = $this->matched(new Url($currentUrl), new Url($url));
            
            // If it matches, let's run it
            if($urlsMatched){
                
                // If there is any dynamic parameter defined in the route, we will get these this->definedRoutes
                $urlParams = $this->getUrlParams($url);
                
                // Running
                $this->execute($this->definedRoute->routeAction, $urlParams);

                //closing
                exit;
            }
        }
    }

    /**
     * This function removes the "/" from the beginning 
     * of the string and from the end of the passed string
     * 
     * @example [
     * clearUrl("/site/image/") -> "site/image" 
     * 
     * @param String $url
     * @return String $url
     */
    public function clearUrl(String $url)
    {
        if(strlen($url) <= 1)
        {
            return "/";
        }

        if($url[0] == "/"){
            $url[0] = " ";
            $url = trim($url);
        }

        if($url[strlen($url)-1] == "/"){
            $url[strlen($url)-1] = " ";
            $url = trim($url);
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
    public function matched(Url $currentUrl, Url $definedUrl)
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
    public function getUrlParams(String $url)
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
