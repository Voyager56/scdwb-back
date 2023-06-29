<?php
namespace App\Controllers;

use App\Models\Product;
use App\Models\Book;

class ProductController {
    private array $products = [
        "book" => Book::class
    ];
    public function __construct(){
    }

    public function store($slug, $query): void
    {
        $product_type = $query["type"];
        $data = $_POST;
        $product = new $this->products[$product_type](...$data);
        $product->save();
        echo json_encode($product->display());
    }

    public function show($slug)
    {
        $id = $slug["id"];
        $productType = Product::getProductType($id);
        $product = $this->products[$productType]::findByProductId($id);
        var_dump($product);
    }
}