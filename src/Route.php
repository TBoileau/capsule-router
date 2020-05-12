<?php

namespace TBoileau\Router;

use ReflectionFunction;
use ReflectionParameter;

/**
 * Class Route
 * @package TBoileau\Router
 */
class Route
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $path;

    /**
     * @var array|callable
     */
    private $callable;

    /**
     * Route constructor.
     * @param string $name
     * @param string $path
     * @param array|callable $callable
     */
    public function __construct(string $name, string $path, $callable)
    {
        $this->name = $name;
        $this->path = $path;
        $this->callable = $callable;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $path
     * @return bool
     */
    public function test(string $path): bool
    {
        $pattern = str_replace("/", "\/", $this->path);
        $pattern = sprintf("/^%s$/", $pattern);
        $pattern = preg_replace("/(\{\w+\})/", "(.+)", $pattern);
        return preg_match($pattern, $path);
    }

    /**
     * @param string $path
     * @return mixed
     * @throws \ReflectionException
     */
    public function call(string $path)
    {
        $pattern = str_replace("/", "\/", $this->path);
        $pattern = sprintf("/^%s$/", $pattern);
        $pattern = preg_replace("/(\{\w+\})/", "(.+)", $pattern);
        preg_match($pattern, $path, $matches);

        preg_match_all("/\{(\w+)\}/", $this->path, $paramMatches);

        array_shift($matches);

        $parameters = $paramMatches[1];

        $argsValue = [];

        if (count($parameters) > 0) {
            $parameters = array_combine($parameters, $matches);

            if (is_array($this->callable)) {
                $reflectionFunc = (new \ReflectionClass($this->callable[0]))->getMethod($this->callable[1]);
            } else {
                $reflectionFunc = new ReflectionFunction($this->callable);
            }

            $args = array_map(fn (ReflectionParameter $param) => $param->getName(), $reflectionFunc->getParameters());

            $argsValue = array_map(function (string $name) use ($parameters) {
                return $parameters[$name];
            }, $args);
        }

        $callable = $this->callable;

        if (is_array($callable)) {
            $callable = [new $callable[0](), $callable[1]];
        }

        return call_user_func_array($callable, $argsValue);
    }
}
