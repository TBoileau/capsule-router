<?php

namespace TBoileau\Router\Tests;

use PHPUnit\Framework\TestCase;
use TBoileau\Router\Route;
use TBoileau\Router\RouteAlreadyExistsException;
use TBoileau\Router\RouteNotFoundException;
use TBoileau\Router\Router;
use TBoileau\Router\Tests\Fixtures\FooController;
use TBoileau\Router\Tests\Fixtures\HomeController;

/**
 * Class RouterTest
 * @package TBoileau\Router\Tests
 */
class RouterTest extends TestCase
{
    public function test()
    {
        $router = new Router();

        $routeHome = new Route("home", "/", [HomeController::class, "index"]);

        $routeFoo = new Route("foo", "/foo/{bar}", [FooController::class, "bar"]);

        $routeArticle = new Route("article", "/blog/{id}/{slug}", function (string $slug, string $id) {
            return sprintf("%s : %s", $id, $slug);
        });

        $router->add($routeHome);
        $router->add($routeFoo);
        $router->add($routeArticle);

        $this->assertCount(3, $router->getRouteCollection());

        $this->assertContainsOnlyInstancesOf(Route::class, $router->getRouteCollection());

        $this->assertEquals($routeHome, $router->get("home"));

        $this->assertEquals($routeHome, $router->match("/"));
        $this->assertEquals($routeArticle, $router->match("/blog/12/mon-article"));

        $this->assertEquals("Hello world !", $router->call("/"));

        $this->assertEquals(
            "12 : mon-article",
            $router->call("/blog/12/mon-article")
        );

        $this->assertEquals(
            "bar",
            $router->call("/foo/bar")
        );
    }

    public function testIfRouteNotFoundByMatch()
    {
        $router = new Router();
        $this->expectException(RouteNotFoundException::class);
        $router->match("/");
    }

    public function testIfRouteNotFoundByGet()
    {
        $router = new Router();
        $this->expectException(RouteNotFoundException::class);
        $router->get("fail");
    }

    public function testIfRouteAlreadyExists()
    {
        $router = new Router();
        $router->add(new Route("home", "/", function() {}));
        $this->expectException(RouteAlreadyExistsException::class);
        $router->add(new Route("home", "/", function() {}));
    }
}
