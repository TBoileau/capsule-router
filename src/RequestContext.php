<?php

namespace TBoileau\Router;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class RequestContext
 * @package TBoileau\Router
 * @method getScheme(): string
 * @method getAuthority(): string
 * @method getUserInfo(): string
 * @method getHost(): string
 * @method getPort(): null|int
 * @method getPath(): string
 * @method getQuery(): string
 * @method getFragment(): string
 * @method withScheme(string $scheme): UriInterface
 * @method withUserInfo(string $user, ?string $password): UriInterface
 * @method withHost(string $host): UriInterface
 * @method withPort(?int $port): UriInterface
 * @method withPath(string $path): UriInterface
 * @method withQuery(string $query): UriInterface
 * @method withFragment(string $fragment): UriInterface
 * @method __toString(): string
 */
class RequestContext
{
    /**
     * @var UriInterface
     */
    private UriInterface $uri;

    /**
     * RequestContext constructor.
     * @param UriInterface $uri
     */
    public function __construct(UriInterface $uri)
    {
        $this->uri = $uri;
    }

    /**
     * @param RequestInterface $request
     * @return RequestContext
     */
    public static function fromRequest(RequestInterface $request): RequestContext
    {
        return new self($request->getUri());
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->uri, $name], $arguments);
    }
}
