<?php

declare(strict_types=1);
require './vendor/autoload.php';

use Mrfoo\Router\Router;
use PHPUnit\Framework\TestCase;
use Mrfoo\Router\Core\URI;

final class Router extends TestCase
{
    public function testGet(): void
    {
        Router::get('/hello', function (test) {
            echo 'hello';
        });

        $this->assertTrue();
    }
}
