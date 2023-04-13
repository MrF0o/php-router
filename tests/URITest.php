<?php

declare(strict_types=1);
require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Mrfoo\Router\Core\URI;

final class URITest extends TestCase
{
    public function testURIMatch(): void
    {
        $uri = new URI('/post/{id}');
        $match = new URI('/post/1');

        $this->assertTrue($uri->same($match));
    }
}
