<?php

namespace TBoileau\Router;

/**
 * Class Router
 * @package TBoileau\Router
 */
class Router
{
    /**
     * @var Route[]
     */
    private array $routes = [];

    /**
     * @param string $path
     * @return Route
     * @throws RouteNotFoundException
     */
    public function match(string $path): Route
    {
        foreach ($this->routes as $route) {
            if ($route->test($path)) {
                return $route;
            }
        }

        throw new RouteNotFoundException();
    }

    /**
     * @param string $path
     * @return mixed
     * @throws RouteNotFoundException
     * @throws \ReflectionException
     */
    public function call(string $path)
    {
        return $this->match($path)->call($path);
    }

    /**
     * @param Route $route
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
     * @param string $name
     * @return Route
     * @throws RouteNotFoundException
     */
    public function get(string $name): Route
    {
        if (!$this->has($name)) {
            throw new RouteNotFoundException();
        }

        return $this->routes[$name];
    }

    /**
     * @param string $name
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
