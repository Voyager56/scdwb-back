<?php

namespace app\Http\Router;

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
            if ($route->matches($method, $parsedUri['path'], $queryParams)) {
                $params = $route->extractParameters($parsedUri['path']);
                $callback = $route->getCallback();

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
        $this->routes[] = new Route($method, $path, $callback);
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

            if (count($args) === count($reflectionParams)) {
                $reflectionFunction->invokeArgs($args);
            } else {
                $this->sendErrorResponse('Invalid number of arguments for callback function.');
            }
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

    private function sendErrorResponse($message): void
    {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['error' => $message]);
        exit();
    }
}


