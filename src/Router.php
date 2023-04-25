<?php
namespace Mrfoo\PHPRouter;

use Mrfoo\PHPRouter\Core\LinkedList;
use Mrfoo\PHPRouter\Core\Route;
use Mrfoo\PHPRouter\Core\URI;
use Mrfoo\PHPRouter\Exceptions\MethodNotSupportedException;
use Exception;

class Router
{
    public static LinkedList $routeList;
    public static ?LinkedList $tmpGroup;
    public static bool $isTrackingGroup = false;

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

    public static function match(array $methods, $uri , $handler)
    {
        return die('unimplemented function: ' . __FUNCTION__);
    }

    public static function any($uri, $handler)
    {
        return die('unimplemented function: ' . __FUNCTION__);
    }

    public static function init()
    {
        if (!isset(self::$routeList)) {
            self::$routeList = new LinkedList();
            self::$tmpGroup = new LinkedList();
        }
    }

    public static function run()
    {
        self::init();
        $user_uri = $_SERVER['REQUEST_URI'];
        $user_method = $_SERVER['REQUEST_METHOD'];
        $route = self::$routeList->search(new URI($user_uri));

        if ($route && $route->getMethod() == $user_method) {
            $route->handle();
        } else {
            // shoud be moved to another method ie: performTests($route);
            if ($route && $route->getMethod() != $user_method) {
                try {
                    throw new MethodNotSupportedException($user_method, [$route->getMethod()]);
                } catch (Exception $e) {
                    print ($e->getMessage());
                    return false;
                }
            } else {
                // TODO: NotFoundException
                header("HTTP/1.0 404 Not Found");
                echo "404 Not Found";
                return false;
            }
        }

        return true;
    }

    public static function registerRoute($uri, $handler, $method)
    {
        $_uri = new URI($uri);
        $route = new Route($uri, $handler, $method);

        self::init();
        if (self::$isTrackingGroup) {
            self::$tmpGroup->add($route);
        } else {
            self::$routeList->add($route);
        }
        

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
            $_p = $options['prefix'];
            call_user_func($callback);

            // now $tmpGroup have each grouped route
            // 1. apply prefix
            self::$tmpGroup->forEach(function (Route $route) use ($_p) {
                $route->applyPrefix($_p);
            });

            // 2. apply middlewares
            // TODO
        }

        self::$routeList->mergeAtTail(self::$tmpGroup);

        self::$isTrackingGroup = false;
        self::$tmpGroup = null;
    }

    public function redirect(string $route, int $status) : Route
    {
        return die('unimplemented function: ' . __FUNCTION__);
    }
}
