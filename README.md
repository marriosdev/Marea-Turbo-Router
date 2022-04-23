@MárriosDev
# Marrios/Router


#### Route Manager in PHP for MVC Projects

* [Starting](#starting)
* [Parameters](#parameters)

# Starting

## 1 - Using functions


 ```php
 use Marrios\Router\Router;

 // Instantiating the route object
 $route = new Router();

// Set route
$route->set("GET",  "/helloworld", [function(){ echo "Hello World!";}]);

//To run the routes
$route->run();
 ```
When accessing the /helloworld route
 ```php
    Hello World!
 ```


 <!-- ======================================== -->

## 2 - Using Controllers


 ```php
use Marrios\Router\Router;
use App\Controllers\TesteController;

// Instantiating the route object
$route = new Router();

// Set route
$route->set("GET",  "/helloworld", [TesteController::class, "helloWorld"]);

//To run the routes
$route->run();
 ```
When accessing the /helloworld route
 ```php
    Hello World!
 ```


 <!-- ============================= -->



# Parameters
## Using dynamic parameters 
### Dynamic parameters are defined using curly braces { }
### * Note: When defining a dynamic route, you must add a parameter to the callback function or in the controller method

<br>

### Follow the example below using CallBack:

 ```php
 use Marrios\Router\Router;

 // Instantiating the route object
 $route = new Router();

// Set route
$route->set("GET", "/blog/{category}/{id_post}", [ function($param){ echo $param->category;}]);

//To run the routes
$route->run();
 ```
When accessing the /blog/video/1323 route
 ```php
    video
 ```

 <br>

### Follow the example below using Controller:

 ```php
use Marrios\Router\Router;
use App\Controllers\TesteController;

// Instantiating the route object
$route = new Router();

// Set route
$route->set("GET", "/blog/{category}/{id_post}", [TesteController::class, "helloWorld"]);

//To run the routes
$route->run();
 ```

### Your controller should look like this
```php
class TesteController
{
    public function helloWorld($param)
    {
        echo $param->id_post;
    }
}
```

When accessing the /blog/video/1323 route
 ```php
    1323
 ```