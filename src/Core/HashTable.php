<?php

namespace Mrfoo\PHPRouter\Core;

class HashTable
{
    public array $routes;

    public function __construct()
    {
        $this->routes = [];
    }

    public function hash(URI $URI)
    {
        $computed = substr(md5(""), 0, 2);

        foreach ($URI->getSegments() as $seg) {
            $computed .= substr(md5($seg), 0, 2);
        }

        $computed .= count($URI->getSegments());

        return $computed;
    }

    public function add(Route $route): void
    {
        $hash = $this->hash($route->getURI());
        if (!$this->searchInternal($route->getURI())) {
            $this->routes[$hash] = $route;
        } else {
            // TODO: Route Already Exists
            die("route already exists");
        }
    }

    private function searchInternal(URI $uri): ?Route
    {
        $hash = $this->hash($uri);
        if (isset($this->routes[$hash])) {
            return $this->routes[$hash];
        } else {
            return NULL;
        }
    }

    public function search(URI $uri): ?Route
    {
        foreach ($this->routes as $route) {
            if ($route->match($uri)) {
                return $route;
            }
        }

        return null;
    }

    public function searchByName(string $name): ?Route
    {
        foreach ($this->routes as $route) {
            if ($route->getName() === $name) {
                return $route;
            }
        }

        return null;
    }

    // for compatibility reasons with the old linked list
    public function mergeAtTail($list): void
    {
        $list->forEach(function ($el) {
            $this->add($el);
        });
    }

    public function forEach($callback): void
    {
        foreach ($this->routes as $route) {
            call_user_func($callback, $route);
        }
    }
}
