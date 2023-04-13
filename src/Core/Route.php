<?php

namespace Mrfoo\Router\Core;

use Exception;

class Route
{
    private URI $uri;
    private $handler;
    private string $method;
    private string $name;

    public function __construct(string $uri, $handler, string $method)
    {
        $this->uri = new URI($uri);
        $this->handler = $handler;
        $this->method = $method;
    }

    public function match(URI $uri): bool
    {
        return $this->uri->match($uri);
    }

    public function handle()
    {
        if (is_callable($this->handler)) {

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

    public function name(string $name): Route
    {
        $this->name = $name;

        return $this;
    }

    public function where(string $regex): Route
    {
        
        return $this;
    }
}
