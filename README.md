Router
======

Example of router in PHP 7.4.

# Installation

```
composer require tboileau/router ^1.0
```

# Create your router

```php
<?php

use GuzzleHttp\Psr7\Request;
use TBoileau\Router\RequestContext;
use TBoileau\Router\Router;

$router = new Router(RequestContext::fromRequest(new Request("GET", $path)));
```

*Note: We use `guzzlehttp/guzzle` for **psr-7** implementation.*

# Add route

```php
<?php

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use TBoileau\Router\Route;

$router->add(new Route("home", "/", function (): ResponseInterface {
    return new Response(200, [], "Hello world !");
}));
```

Or by calling an action of a controller :

```php
<?php

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use TBoileau\Router\Route;

class HomeController
{
    public function index(): ResponseInterface
    {
        return new Response(200, [], "Hello world !");
    }
}

$router->add(new Route("home", "/", [HomeController::class, "index"]));
```

# Call function or controller's action from a specific path

```php
<?php

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use TBoileau\Router\RequestContext;
use TBoileau\Router\Route;
use TBoileau\Router\Router;

$router = new Router(RequestContext::fromRequest(new Request("GET", $path)));

$router->add(new Route("home", "/", function (): ResponseInterface {
    return new Response(200, [], "Hello world !");
}));

/** @var ResponseInterface $response */
$response = $router->call();
```
