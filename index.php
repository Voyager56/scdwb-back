<?php
require_once __DIR__ . '/./public/autoload.php';

use App\Http\Router;
use App\Controllers\ProductController;

$router = new Router();

$router->get('/', function () {
    echo "Hello, world!";
});

$router->post('/product', function ($slug, $query) {
    $productController = new ProductController();
    $productController->store($slug, $query);
});

$router->get('/product/{id}', function ($slug) {
    $productController = new ProductController();
    $productController->show($slug);
});

$router->post('/users/{id}/{name}', function ($slug) {
    $id = $slug['id'];
    $name = $slug['name'];

    echo json_encode([
        'id' => $id,
        'name' => $name
    ]);

});

$router->handleRequest($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);




