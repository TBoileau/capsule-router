<?php

namespace TBoileau\Router\Tests;

use Generator;
use PHPUnit\Framework\TestCase;
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
     * @var Router
     */
    private Router $router;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->router = new Router();
    }

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
        $this->router->add($route);

        $this->assertContains($route, $this->router->getRouteCollection());

        $this->assertEquals($route, $this->router->match($path));

        $this->assertStringContainsString($response, $this->router->call($path));
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
                    return sprintf("%s : %s", $id, $slug);
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
                    return sprintf("Page %d", $page);
                },
                ["page" => 1]
            ),
            "/blog/",
            "Page 1"
        ];
    }

    public function test if route requirements is wrong()
    {
        $this->router->add(new Route(
            "article",
            "/blog/{id}/{slug}",
            function (string $slug, string $id) {
                return sprintf("%s : %s", $id, $slug);
            },
            [],
            ["id" => "\d+"]
        ));
        $this->expectException(RouteNotFoundException::class);
        $this->router->match("/blog/fail/slug");
    }

    public function test if route not found by match()
    {
        $this->expectException(RouteNotFoundException::class);
        $this->router->match("/");
    }

    public function test if route already exists()
    {
        $this->router->add(
            new Route(
                "home",
                "/",
                function () {
                }
            )
        );
        $this->expectException(RouteAlreadyExistsException::class);
        $this->router->add(
            new Route(
                "home",
                "/",
                function () {
                }
            )
        );
    }
}
