<?php

namespace TBoileau\Router;

use Psr\Http\Message\ResponseInterface;

/**
 * Class Router
 *
 * @package TBoileau\Router
 */
class Router
{
    /**
     * @var Route[]
     */
    private array $routes = [];

    /**
     * @var RequestContext
     */
    private RequestContext $requestContext;

    /**
     * Router constructor.
     * @param RequestContext $requestContext
     */
    public function __construct(RequestContext $requestContext)
    {
        $this->requestContext = $requestContext;
    }

    /**
     * @return Route
     * @throws RouteNotFoundException
     */
    public function match(): Route
    {
        foreach ($this->routes as $route) {
            if ($route->test($this->requestContext->getPath())) {
                return $route;
            }
        }

        throw new RouteNotFoundException();
    }

    /**
     * @return ResponseInterface
     * @throws RouteNotFoundException
     * @throws \ReflectionException
     */
    public function call(): ResponseInterface
    {
        return $this->match()->call($this->requestContext->getPath());
    }

    /**
     * @param  Route $route
     * @return $this
     * @throws RouteAlreadyExistsException
     */
    public function add(Route $route): self
    {
        if ($this->has($route->getName())) {
            throw new RouteAlreadyExistsException();
        }

        $this->routes[$route->getName()] = $route;

        return $this;
    }

    /**
     * @param  string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->routes[$name]);
    }

    /**
     * @return array|Route[]
     */
    public function getRouteCollection(): array
    {
        return $this->routes;
    }
}
