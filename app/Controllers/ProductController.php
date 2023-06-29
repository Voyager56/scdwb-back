<?php
namespace App\Controllers;

use App\Models\Book;
use App\Models\Product;

class ProductController {
    private array $products = [
        "book" => Book::class,
    ];
    
    public function index()
    {
        $products = Product::getAll();
        echo json_encode($products);
    }

    public function store($slug, $query): void
    {
        $product_type = $this->products[$query["type"]];
        $data = $_POST;
        $product = new $product_type(...$data);
        $product->save();

        var_dump(json_encode($product));
    }

    public function show($slug)
    {
        $id = $slug["id"];
        $product = Product::findById($id);

        echo json_encode($product);
    }
}