<?php

namespace Marrios\Router;

use Marrios\Router\Logs\Logs;
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
use Marrios\Router\Pages\NotFound;
use Marrios\Router\Middleware;
use Marrios\Router\Group;

class HttpRouter
{
    use Group, Post, Get, Put, Delete, Patch, Head, Options, NotFound, Middleware, Logs;

    /**
     * @var \Marrios\Router\Route
     */
    public Route $definedRoute;

    /**
     * @var Url
     */
    public $currentUrl;

    /**
     * 
     */
    public function __construct()
    {
        $this->currentUrl = new Url($_SERVER["REQUEST_URI"]);
    }

    /**
     * Processing routes
     */
    public function run()
    {
        // If it is running in a group of routes, it will only execute the routes after they are grouped. 
        // So we stop the code here and let the Group perform the necessary steps        
        if ($this->runGroup) {
            $instanceClone = clone $this;
            $instanceClone->runGroup = false;
            return $instanceClone;
        }

        $currentUrl = $this->currentUrl;
        $routePath  = $this->definedRoute->route;
        $url        = new Url($this->definedRoute->route);

        if ($this->definedRoute->method == $_SERVER["REQUEST_METHOD"]) {
            $urlsMatched = $this->matched($currentUrl, $url);
            if ($urlsMatched) {
                $urlParams = $this->getUrlParams($url);
                if ($this->middlewareAccess) {
                    $this->execute($this->definedRoute->routeAction, $urlParams);
                    $this->middlewareAccess = true;
                    $this->startLogs($this);
                    exit;
                }
            }
            $this->middlewareAccess = true;
        }
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
     * @param Url  $currentUrl
     * @param Url  $definedUrl
     */
    public function matched(Url $currentUrl, Url $definedUrl)
    {
        $currentUrl = explode("/", $currentUrl->get());
        $definedUrl = explode("/", $definedUrl->get());

        if (count($currentUrl) != count($definedUrl)) {
            return false;
        }

        $countMatch = count($definedUrl);

        for ($i = 0; $i < $countMatch; $i++) {
            if ($definedUrl[$i] !=  $currentUrl[$i]) {
                /**
                 * Checking if this parameter the parameter is dynamic
                 * If it is dynamic, we disregard the difference
                 */
                if (!preg_match("/[{}]/", $definedUrl[$i])) {
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
     * @param Url $url
     * @return \Marrios\Router\Entities\RouteParameters $paramList
     */
    public function getUrlParams(Url $url)
    {
        $url        = explode("/", $url->get());
        $currentUrl = explode("/", $this->currentUrl->get());

        $paramsList = new RouteParameters();

        for ($i = 0; $i < count($url); $i++) {
            $definedParam = new Parameter($url[$i]);
            $currentParam = new Parameter($currentUrl[$i]);

            if ($definedParam->valid()) {
                $paramsList->setParameter($definedParam, $currentParam);
            }
        }
        return $paramsList;
    }

    /**
     * Executing the callBack function passed in the route
     * 
     * @param \Closure $process | Callback function
     * @param \Marrios\Router\Entities\RouteParameters | Route parameters
     * 
     * @return Mixed
     */
    public function runCallBack(\Closure $callBack, RouteParameters $routeParams)
    {
        try {
            return $callBack($routeParams);
        } catch (RouterException $e) {
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
    public function runController(array $process, RouteParameters $routeParams)
    {
        $controller = new Controller($process['class']);
        $controller->runMethod($process['method'], $routeParams);
    }

    /**
     * Function that executes the route
     * 
     * @param $process | $routeParams 
     * 
     * @return bool|\Exception
     */
    public function execute(array $process, RouteParameters $routeParams)
    {
        if (isset($process["callBack"])) {
            $this->runCallBack($process["callBack"], $routeParams);
            return true;
        }
        if (isset($process["controller"])) {
            $this->runController($process["controller"], $routeParams);
            return true;
        }
        throw new RouterException("An error occurred while performing this action");
    }
}
