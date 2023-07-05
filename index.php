<?php
require_once __DIR__ . '/./public/autoload.php';

use App\Http\Controllers\ProductController;
use App\Http\Router\Router;


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

//        delete method working with postman but not with 000webhost
//        $router->delete('/product/{id}', function ($slug) {
//            $productController = new ProductController();
//            $productController->delete($slug);
//        });

        $router->post('/product/delete/{id}', function ($slug) {
            $productController = new ProductController();
            $productController->delete($slug);
        });

        $router->post('/product/massDelete', function () {
            $productController = new ProductController();
            $productController->massDelete();
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




