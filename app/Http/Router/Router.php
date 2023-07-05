<?php

namespace App\Http\Router;

use App\Http\Router\Route;
class Router
{
    private $routes = [];

    public function get($path, $callback): void
    {
        $this->addRoute('GET', $path, $callback);
    }

    public function post($path, $callback): void
    {
        $this->addRoute('POST', $path, $callback);
    }

    public function delete($path, $callback): void
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
            if ($route->matches($method, $parsedUri['path'])) {
                $slug = $route->extractSlugParameters($parsedUri['path']);
                $query = $queryParams;
                $callback = $route->getCallback();

                if (is_callable($callback)) {
                    try {
                        $this->invokeCallback($callback, $slug, $query);
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
        $this->routes[] = new Route($method, $path, $callback);
    }

    private function invokeCallback($callback, $slug, $query): void
    {
        if (is_array($callback)) {
            $object = $callback[0];
            $method = $callback[1];
            $reflectionMethod = new \ReflectionMethod($object, $method);

            $reflectionParams = $reflectionMethod->getParameters();
            $args = [];

            foreach ($reflectionParams as $param) {
                $paramName = $param->getName();
                if ($paramName === 'slug') {
                    $args[] = $slug;
                } elseif ($paramName === 'query') {
                    $args[] = $query;
                } else {
                    $args[] = null;
                }
            }

            $reflectionMethod->invokeArgs($object, $args);
        } else {
            $reflectionFunction = new \ReflectionFunction($callback);

            $reflectionParams = $reflectionFunction->getParameters();
            $args = [];

            foreach ($reflectionParams as $param) {
                $paramName = $param->getName();
                if ($paramName === 'slug') {
                    $args[] = $slug;
                } elseif ($paramName === 'query') {
                    $args[] = $query;
                } else {
                    $args[] = null;
                }
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
                <<p>$message</p>
            </body>
            </html>
        HTML;
    }
}
