<?php

namespace TBoileau\Router\Tests\Fixtures;

use GuzzleHttp\Psr7\Response;

/**
 * Class HomeController
 *
 * @package TBoileau\Router\Tests\Fixtures
 */
class FooController
{
    public function bar(string $bar)
    {
        return new Response(200, [], $bar);
    }
}
