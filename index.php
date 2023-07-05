<?php
require_once __DIR__ . '/./public/autoload.php';

use app\Http\Controllers\ProductController;
use app\Http\Router\Router;


class App {
    public function __construct(private readonly Router $router){
        $router->get('/', function () {
            echo "Hello, world!";
        });

        $router->get('/product', function () {
            $productController = new ProductController();
            $productController->index();
        });

        $router->post('/product', function () {
            $productController = new ProductController();
            $productController->store();
        });

        $router->get('/product/{id}', function ($slug) {
            $productController = new ProductController();
            $productController->show($slug);
        });
    }
    public function run(): void {
        header("Access-Control-Allow-Origin: * ");
        $this->router->handleRequest($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
    }
}

$autoload = new AutoLoad();
$autoload->run();

const app = new App(new Router());
app->run();




