<?php

namespace App\Http\Router;

class Route
{
    private $method;
    private $path;
    private $pattern;
    private $callback;

    public function __construct($method, $path, $callback)
    {
        $this->method = $method;
        $this->path = $path;
        $this->pattern = '#^' . preg_replace('/\{([a-zA-Z][a-zA-Z0-9_]*)\}/', '(?<$1>[^/]+)', $path) . '$#';
        $this->callback = $callback;
    }

    public function matches($method, $uri): bool
    {
        $parsedUri = parse_url($uri);

        return ($this->method === $method) &&
            isset($parsedUri['path']) &&
            preg_match($this->pattern, $parsedUri['path']);
    }

    public function extractSlugParameters($uri): array
    {
        $matches = [];
        preg_match($this->pattern, $uri, $matches);
        array_shift($matches); // Remove the first match (full path)

        return $matches;
    }

    public function getCallback()
    {
        return $this->callback;
    }
}