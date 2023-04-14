<?php

namespace Mrfoo\PHPRouter\Core;

class URI
{
    protected $uri;
    protected $segments = [];
    protected $parameters = [];

    public function __construct($uri)
    {
        $this->uri = $uri;
        $this->parseSegments();
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getSegment($index)
    {
        return isset($this->segments[$index]) ? $this->segments[$index] : null;
    }

    public function getSegments()
    {
        return $this->segments;
    }

    public function countSegments()
    {
        return count($this->segments);
    }

    public function getParameter($name)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    protected function parseSegments()
    {
        $segments = explode('/', trim($this->uri, '/'));
        foreach ($segments as $segment) {
            if (preg_match('/^({\w+})$/', $segment, $matches)) {
                $this->parameters[substr($matches[1], 1, -1)] = null;
                $this->segments[] = $matches[1];
            } else {
                $this->segments[] = $segment;
            }
        }
    }

    public function match(Uri $pattern)
    {
        if ($this->countSegments() !== $pattern->countSegments()) {
            return false;
        }

        for ($i = 0; $i < $this->countSegments(); $i++) {
            if ($pattern->getSegment($i) === null && $this->getSegment($i) !== null) {
                return false;
            } elseif (preg_match('/^{(\w+)}$/', $this->getSegment($i), $matches)) {
                $this->parameters[$matches[1]] = $pattern->getSegment($i);
            } elseif ($this->getSegment($i) !== $pattern->getSegment($i)) {
                return false;
            }
        }

        return true;
    }

    public function same(Uri $pattern) {
        if ($this->countSegments() !== $pattern->countSegments()) {
            return false;
        }

        return true;
    }
}
