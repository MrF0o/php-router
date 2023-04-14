<?php

use Mrfoo\PHPRouter\Core\LinkedList;
use Mrfoo\PHPRouter\Core\Route;
use Mrfoo\PHPRouter\Core\URI;
use Mrfoo\PHPRouter\Exceptions\MethodNotSupportedException;

class Router
{
    public static LinkedList $routeList;
    public static bool $isTrackingGroup;

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
        $user_method = $_SERVER['REQUEST_METHOD'];
        $route = self::$routeList->search(new URI($user_uri));

        if ($route && $route->getMethod() == $user_method) {
            $route->handle();
        } else {
            if ($route->getMethod() != $user_method) {
                try {
                    throw new MethodNotSupportedException($user_method, [$route->getMethod()]);
                } catch (Exception $e) {
                    print ($e->getMessage());
                }
            } else {
                header("HTTP/1.0 404 Not Found");
                echo "404 Not Found";
            }
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

    public static function buildRoute($uri, $handler, $method)
    {
        $route = new Route($uri, $handler, $method);

        return $route;
    }

    public static function group(array $options, $callback): void
    {
        self::$isTrackingGroup = true;

        if (isset($options['prefix'])) {
        }

        call_user_func($callback);
    }
}
