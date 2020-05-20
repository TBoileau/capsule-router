<?php

namespace TBoileau\Router\Tests;

use Generator;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use TBoileau\Router\RequestContext;
use TBoileau\Router\Route;
use TBoileau\Router\RouteAlreadyExistsException;
use TBoileau\Router\RouteNotFoundException;
use TBoileau\Router\Router;
use TBoileau\Router\Tests\Fixtures\FooController;
use TBoileau\Router\Tests\Fixtures\HomeController;

/**
 * Class RouterTest
 *
 * @package TBoileau\Router\Tests
 */
class RouterTest extends TestCase
{
    /**
     * @dataProvider provideRoutes
     * @param Route $route
     * @param string $path
     * @param string $response
     * @throws RouteAlreadyExistsException
     * @throws RouteNotFoundException
     * @throws \ReflectionException
     */
    public function test if route retrieves good action(Route $route, string $path, string $response)
    {
        $router = new Router(RequestContext::fromRequest(new Request("GET", $path)));

        $router->add($route);

        $this->assertContains($route, $router->getRouteCollection());

        $this->assertEquals($route, $router->match());

        $this->assertInstanceOf(ResponseInterface::class, $router->call());

        $this->assertStringContainsString($response, $router->call()->getBody()->getContents());
    }

    /**
     * @return Generator
     */
    public function provideRoutes(): Generator
    {
        yield [
            new Route("home", "/", [HomeController::class, "index"]),
            "/",
            "Hello world !"
        ];

        yield [
            new Route("foo", "/foo/{bar}", [FooController::class, "bar"]),
            "/foo/test",
            "test"
        ];

        yield [
            new Route(
                "article",
                "/blog/{id}/{slug}",
                function (string $slug, string $id) {
                    return new Response(200, [], sprintf("%s : %s", $id, $slug));
                },
                [],
                ["id" => "\d+"]
            ),
            "/blog/12/article",
            "12 : article"
        ];

        yield [
            new Route(
                "blog",
                "/blog/{page}",
                function (int $page) {
                    return new Response(200, [], sprintf("Page %d", $page));
                },
                ["page" => 1]
            ),
            "/blog/",
            "Page 1"
        ];
    }

    public function test if route requirements is wrong()
    {
        $router = new Router(RequestContext::fromRequest(new Request("GET", "/blog/fail/slug")));
        $router->add(new Route(
            "article",
            "/blog/{id}/{slug}",
            function (string $slug, string $id) {
                return sprintf("%s : %s", $id, $slug);
            },
            [],
            ["id" => "\d+"]
        ));
        $this->expectException(RouteNotFoundException::class);
        $router->match();
    }

    public function test if route not found by match()
    {
        $router = new Router(RequestContext::fromRequest(new Request("GET", "/")));
        $this->expectException(RouteNotFoundException::class);
        $router->match();
    }

    public function test if route already exists()
    {
        $router = new Router(RequestContext::fromRequest(new Request("GET", "/")));
        $router->add(
            new Route(
                "home",
                "/",
                function () {
                }
            )
        );
        $this->expectException(RouteAlreadyExistsException::class);
        $router->add(
            new Route(
                "home",
                "/",
                function () {
                }
            )
        );
    }
}
