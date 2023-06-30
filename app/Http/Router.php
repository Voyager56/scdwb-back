<?php

namespace App\Http;

class Router
{
    private $routes = [];

    public function get($path, $callback)
    {
        $this->addRoute('GET', $path, $callback);
    }

    public function post($path, $callback)
    {
        $this->addRoute('POST', $path, $callback);
    }

    public function delete($path, $callback)
    {
        $this->addRoute('DELETE', $path, $callback);
    }

    public function handleRequest($method, $uri): void
    {
        $queryParams = [];
        $parsedUri = parse_url($uri);

        if (isset($parsedUri['query'])) {
            parse_str($parsedUri['query'], $queryParams);
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $parsedUri['path'], $matches)) {
                array_shift($matches); // Remove the first match (full path)

                $params = [
                    'slug' => $matches,        // Pass slug parameters
                    'query' => $queryParams,   // Pass query parameters
                ];

                $callback = $route['callback'];

                if (is_callable($callback)) {
                    try {
                        $this->invokeCallback($callback, $params);
                        return;
                    } catch (\Throwable $e) {
                        $this->renderErrorPage(500, $e->getMessage());
                        return;
                    }
                } else {
                    $this->renderErrorPage(500, 'Invalid callback specified for route.');
                    return;
                }
            }
        }

        $this->renderErrorPage(404, 'Route not found.');
    }

    private function addRoute($method, $path, $callback): void
    {
        $pattern = '#^' . preg_replace('/\{([a-zA-Z][a-zA-Z0-9_]*)\}/', '(?<$1>[^/]+)', $path) . '$#';

        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'callback' => $callback
        ];
    }

    private function invokeCallback($callback, $params): void
    {
        if (is_array($callback)) {
            $object = $callback[0];
            $method = $callback[1];
            $reflectionMethod = new \ReflectionMethod($object, $method);

            $reflectionParams = $reflectionMethod->getParameters();
            $args = [];

            foreach ($reflectionParams as $param) {
                $paramName = $param->getName();
                $args[] = $params[$paramName] ?? null;
            }

            $reflectionMethod->invokeArgs($object, $args);
        } else {
            $reflectionFunction = new \ReflectionFunction($callback);

            $reflectionParams = $reflectionFunction->getParameters();
            $args = [];

            foreach ($reflectionParams as $param) {
                $paramName = $param->getName();
                $args[] = $params[$paramName] ?? null;
            }

            $reflectionFunction->invokeArgs($args);
        }
    }

    private function renderErrorPage($statusCode, $message): void
    {
        http_response_code($statusCode);

        echo <<<HTML
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <title>Error</title>
                </head>
                <body>
                    <h1>Error $statusCode</h1>
                    <p>$message</p>
                </body>
                </html>
            HTML;
    }
}
