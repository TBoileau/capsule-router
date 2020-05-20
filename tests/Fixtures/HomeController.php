<?php

namespace TBoileau\Router\Tests\Fixtures;

use GuzzleHttp\Psr7\Response;

/**
 * Class HomeController
 *
 * @package TBoileau\Router\Tests\Fixtures
 */
class HomeController
{
    public function index()
    {
        return new Response(200, [], "Hello world !");
    }
}
