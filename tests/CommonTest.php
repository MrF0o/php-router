<?php

declare(strict_types=1);
require './vendor/autoload.php';

use Mrfoo\PHPRouter\Router;
use PHPUnit\Framework\TestCase;

final class CommonTest extends TestCase
{
    /* 
    * @runInSeparateProcess
    */
    public function testRoute(): void
    {
        Router::get("/hello/{name}", function () {
            null;
        })->name("hello");

        $url = route("hello");

        $this->assertNotEmpty($url);
    }
}
