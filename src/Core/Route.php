<?php

namespace Mrfoo\PHPRouter\Core;

use Exception;

class Route
{
    private URI $uri;
    private $handler;
    private string $method;
    private bool $isRedirect;
    private URI $redirectUri;
    private int $redirectStatus;
    private ?string $name = null;

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

    public function getURI() {
        return $this->uri;
    }

    public function getName() {
        return $this->name;
    }
}
