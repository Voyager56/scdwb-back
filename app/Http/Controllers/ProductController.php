<?php
namespace app\Http\Controllers;

use App\Exceptions\RequestValidationException;
use App\Models\Book;
use App\Models\DvdDisc;
use App\Models\Furniture;
use App\Models\Product;

class ProductController {
    private array $products = [
        "book" => Book::class,
        'furniture' =>  Furniture::class,
        'dvd' => DvdDisc::class
    ];
    
    public function index(): void
    {
        $products = Product::getAll();
        echo json_encode($products);
    }

    public function store(): void
    {
        $data = $_POST;
        $productType = $data["productType"];
    
        if (!isset($this->products[$productType])) {
            echo json_encode(['errors' => "Invalid product type"]);
            return;
        }
    
        $productTypeClass = $this->products[$productType];
    
        try {
            unset($data["productType"]);
    
            $reflectionClass = new \ReflectionClass($productTypeClass);
            $constructorParams = $reflectionClass->getConstructor()->getParameters();
    
            if (count($data) < count($constructorParams)) {
                $this->sendErrorResponse("Invalid number of parameters");
            }
    
            $args = array_values($data);
            $product = $reflectionClass->newInstanceArgs($args);
    
            $product->save();
    
            echo json_encode($product);
        } catch (RequestValidationException $e) {
            $this->sendErrorResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->sendErrorResponse($e->getMessage());
        }
    }

    public function show($slug): void
    {
        $id = $slug["id"];
        $product = Product::findById($id);

        echo json_encode($product);
    }

    private function sendErrorResponse($message): void
    {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['error' => $message]);
        exit();
    }
}