<?php
require_once __DIR__ . '/./public/autoload.php';

use App\Http\Router;
use App\Controllers\ProductController;


class App {
    public function __construct(private readonly Router $router){
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
    }
    public function run(): void {
        $this->router->handleRequest($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
    }
}

$autoload = new AutoLoad();
$autoload->run();

const app = new App(new Router());
app->run();




