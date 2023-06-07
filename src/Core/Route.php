<?php

namespace Mrfoo\PHPRouter\Core;

use Exception;
use Middleware;
use Mrfoo\PHPRouter\Core\Middleware as CoreMiddleware;

class Route
{
    private URI $uri;
    private $handler;
    private string $method;
    private bool $isRedirect;
    private URI $redirectUri;
    private int $redirectStatus;
    private ?string $name = null;
    private array $middlewares;

    public function __construct(string $uri, $handler, string $method)
    {
        $this->uri = new URI($uri);
        $this->handler = $handler;
        $this->method = $method;
        $this->middlewares = [];
    }

    public function match(URI $uri): bool
    {
        return $this->uri->match($uri);
    }

    public function handle()
    {
        if (is_callable($this->handler)) {
            $this->preHandleMiddlewares();
            return call_user_func_array($this->handler, $this->uri->getParameters());
        }

        if (is_array($this->handler) && count($this->handler) === 2) {
            $class = $this->handler[0];
            $method = $this->handler[1];
            if (class_exists($class) && method_exists($class, $method)) {
                return (new $class)->$method();
            }
        }

        throw new Exception('Invalid handler provided.');
    }

    public function handleRedirect()
    {
        return die('unimplemented function: ' . __FUNCTION__);
    }

    public function name(string $name): Route
    {
        $this->name = $name;

        return $this;
    }

    public function where(string $segmentName, string $regex): Route
    {
        $this->uri->registerWhereOnSegment($segmentName, $regex);

        return $this;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function applyPrefix($prefix)
    {
        $this->uri = new URI($prefix . $this->uri->getUri());
    }

    public function redirect(string $route, int $status): Route
    {
        $this->isRedirect = true;
        $this->redirectUri = new URI($route);
        $this->redirectStatus = $status;

        return $this;
    }

    public function getURI()
    {
        return $this->uri;
    }

    public function getName()
    {
        return $this->name;
    }

    private function configureMiddlewares(array $middlewares)
    {
        foreach ($middlewares as $middleware) {
            if (class_exists($middleware, true)) {
                $obj = new $middleware;
                
                if ($obj instanceof \Mrfoo\PHPRouter\Core\Middleware) {
                    array_push($this->middlewares, $obj);
                }
            }
        }
    }

    public function middleware(string|array $middlewares)
    {
        if (gettype($middlewares) == 'array')
            $this->configureMiddlewares($middlewares);
        else if (gettype($middlewares) == 'string')
            $this->configureMiddlewares([$middlewares]);
    }

    private function preHandleMiddlewares() {
        foreach($this->middlewares as $m) {
            $m->handle();
        }
    }

    // should be public because we are trying to 
    public function postHandleMiddlewares() {
        foreach($this->middlewares as $m) {
            $m->terminate();
        }
    }
}
