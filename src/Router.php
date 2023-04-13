<?php

use Mrfoo\Router\Core\LinkedList;
use Mrfoo\Router\Core\Route;
use Mrfoo\Router\Core\URI;

class Router
{
    public static $routeList;

    public static function get($uri, $handler)
    {
        $_uri = new URI($uri);
        $route = new Route($uri, $handler, 'GET');

        self::init();
        self::$routeList->add($route);
    }

    public static function post($uri, $handler)
    {
        $_uri = new URI($uri);
        $route = new Route($uri, $handler, 'POST');

        self::init();
        self::$routeList->add($route);
    }

    public static function put($uri, $handler)
    {
        $_uri = new URI($uri);
        $route = new Route($uri, $handler, 'PUT');

        self::init();
        self::$routeList->add($route);
    }

    public static function patch($uri, $handler)
    {
        $_uri = new URI($uri);
        $route = new Route($uri, $handler, 'PATCH');

        self::init();
        self::$routeList->add($route);
    }

    public static function delete($uri, $handler)
    {
        $_uri = new URI($uri);
        $route = new Route($uri, $handler, 'DELETE');

        self::init();
        self::$routeList->add($route);
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
}
