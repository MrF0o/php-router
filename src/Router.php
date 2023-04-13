<?php

use Mrfoo\Router\Core\LinkedList;
use Mrfoo\Router\Core\Route;
use Mrfoo\Router\Core\URI;

class Router
{
    public static $routeList;

    public static function get($uri, $handler)
    {
        return static::registerRoute($uri, $handler, 'GET');
    }

    public static function post($uri, $handler)
    {
        return static::registerRoute($uri, $handler, 'POST');
    }

    public static function put($uri, $handler)
    {
        return static::registerRoute($uri, $handler, 'PUT');
    }

    public static function patch($uri, $handler)
    {
        return static::registerRoute($uri, $handler, 'PATCH');
    }

    public static function delete($uri, $handler)
    {
        return static::registerRoute($uri, $handler, 'DELETE');
    }

    public static function init()
    {
        if (!isset(self::$routeList)) {
            self::$routeList = new LinkedList();
        }
    }

    public static function run()
    {
        $user_uri = $_SERVER['REQUEST_URI'];
        $route = self::$routeList->search(new URI($user_uri));

        if ($route) {
            $route->handle();
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "404 Not Found";
        }
    }

    public static function registerRoute($uri, $handler, $method)
    {
        $_uri = new URI($uri);
        $route = new Route($uri, $handler, $method);

        self::init();
        self::$routeList->add($route);

        return $route;
    }
}
