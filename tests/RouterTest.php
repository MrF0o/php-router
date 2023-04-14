<?php

declare(strict_types=1);
require './vendor/autoload.php';

use Mrfoo\PHPRouter\Router;
use PHPUnit\Framework\TestCase;
use Mrfoo\PHPRouter\Core\URI;

final class RouterTest extends TestCase
{
    /* 
    * @runInSeparateProcess
    */
    public function testRun(): void
    {
        // fake some data
        $_SERVER['REQUEST_URI'] = "/";
        $_SERVER['REQUEST_METHOD'] = "GET";

        Router::get('/', function () {
            echo "hello";
        });

        $this->assertTrue(Router::run());
    }
}
