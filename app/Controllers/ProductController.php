<?php
namespace App\Controllers;

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

    public function store($slug, $query): void
    {
        $product_type = $this->products[$query["type"]];
        $data = $_POST;
        try{
            $product = new $product_type(...$data);
            $product->save();
        }catch(RequestValidationException $e){
            echo json_encode($e->getErrors());
        }catch(\Exception $e){
            echo json_encode($e->getMessage());
        }

        echo json_encode($product);
    }

    public function show($slug): void
    {
        $id = $slug["id"];
        $product = Product::findById($id);

        echo json_encode($product);
    }
}