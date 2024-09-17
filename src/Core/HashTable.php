<?php

namespace Mrfoo\PHPRouter\Core;

class HashTable
{
	/** @var array<string, Route> */
	private array $routes = [];

	/** @var array<string, Route> */
	private array $routesByName = [];

	public function hash(Route $route): string
	{
		$uri = $route->getURI();
		$method = $route->getMethod();
		$segments = $uri->getSegments();

		$hash = $method[0]; // first letter of the method
		$segmentCount = count($segments);
		$hash .= chr(($segmentCount % 26) + 97); // lowercase letter representing segment count

		foreach ($segments as $segment) {
			if ($segment !== '' && $segment[0] === '{' && $segment[-1] === '}') {
				$hash .= '_'; // placeholder for parameters
			} elseif ($segment !== '') {
				$hash .= $segment[0] . strlen($segment); // 1st char and length for normal segments
			} else {
				$hash .= '/1'; // home page
			}
		}

		return $hash;
	}

	public function add(Route $route): void
	{
		$hash = $this->hash($route);
		if (isset($this->routes[$hash])) {
			throw new \RuntimeException("Route already exists");
		}
		$this->routes[$hash] = $route;
		$this->routesByName[$route->getName()] = $route;
	}

	public function search(Route $route): ?Route
	{
		return $this->routes[$this->hash($route)] ?? null;
	}

	public function searchByName(string $name): ?Route
	{
		return $this->routesByName[$name] ?? null;
	}

	public function mergeAtTail(iterable $list): void
	{
		foreach ($list as $route) {
			$this->add($route);
		}
	}

	public function forEach(callable $callback): void
	{
		array_walk($this->routes, $callback);
	}
}