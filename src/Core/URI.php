<?php

namespace Mrfoo\PHPRouter\Core;

class URI
{
	private string $uri;
	private array $segments = [];
	private array $parameters = [];
	private array $rules = [];

	public function __construct(string $uri)
	{
		$this->uri = $uri;
		$this->parseSegments();
	}

	public function getUri(): string
	{
		return $this->uri;
	}

	public function getSegment(int $index): ?string
	{
		return $this->segments[$index] ?? null;
	}

	public function getSegments(): array
	{
		return $this->segments;
	}

	public function countSegments(): int
	{
		return count($this->segments);
	}

	public function getParameter(string $name): ?string
	{
		return $this->parameters[$name] ?? null;
	}

	public function getParameters(): array
	{
		return $this->parameters;
	}

	protected function parseSegments(): void
	{
		$this->segments = explode('/', trim($this->uri, '/'));
		$segmentCount = count($this->segments);

		for ($i = 0; $i < $segmentCount; $i++) {
			$segment = $this->segments[$i];

			if ($segment !== '' && $segment[0] === '{' && $segment[strlen($segment) - 1] === '}') {
				$paramName = substr($segment, 1, -1);
				$this->parameters[$paramName] = null;
			}
		}
	}

	public function match(URI $pattern): bool
	{
		if ($this->countSegments() !== $pattern->countSegments()) {
			return false;
		}

		$patternSegments = $pattern->getSegments();
		foreach ($this->segments as $i => $thisSegment) {
			$patternSegment = $patternSegments[$i];

			if ($thisSegment !== '' && $thisSegment[0] === '{' && $thisSegment[-1] === '}') {
				$paramName = substr($thisSegment, 1, -1);
				if (!$this->testSegmentAgainst($paramName, $patternSegment)) {
					return false;
				}
				$this->parameters[$paramName] = $patternSegment;
			} elseif ($thisSegment !== $patternSegment) {
				return false;
			}
		}

		return true;
	}

	private function testSegmentAgainst(string $segment, string $value): bool
	{
		return !isset($this->rules[$segment]) || preg_match("/{$this->rules[$segment]}/", $value) === 1;
	}

	public function registerWhereOnSegment(string $segmentName, string $regex): void
	{
		$this->rules[$segmentName] = $regex;
	}

	public function same(URI $pattern): bool
	{
		return $this->countSegments() === $pattern->countSegments();
	}
}