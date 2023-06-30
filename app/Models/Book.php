<?php

namespace App\Models;

use App\Database\Database;
use App\Exceptions\DatabaseException;
use App\Exceptions\RequestValidationException;
use PDOException;

class Book extends Product implements \JsonSerializable
{
    protected float $weight;

    public function __construct(string $sku, string $name, float $price ,float $weight)
    {
        parent::__construct($sku, $name, $price);
        $this->weight = $weight;
    }

    public function setWeight(float $weight): void
    {
        $this->weight = $weight;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function save(): void
    {

        if($this->weight <= 0) {
            throw new RequestValidationException(['weight' => 'Weight must be greater than 0']);
        }

        parent::saveProduct('App\Models\Book');
        $db = new Database();

        $data = [
            'weight_kg' => $this->weight,
            'product_id' => $db->getLastInsertId()
        ];

        $query = "INSERT INTO books (weight_kg, product_id) VALUES ( :weight_kg, :product_id)";

        try {
            $db->execute($query, $data);
        } catch (PDOException $e) {
            $query = "DELETE FROM products WHERE id = :id";
            $db->execute($query, ['id' => $db->getLastInsertId()]);
            throw new DatabaseException($e->getMessage(), $e->getCode(), $e, $this->weight);
        }
    }

    public static function findByProductId(int $id): ?Book
    {
        $db = new Database();
        $query = "SELECT * FROM books JOIN products ON books.product_id = products.id WHERE books.product_id = :id";
        $data = ['id' => $id];

        try {
            $result = $db->fetch($query, $data);
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode(), $e, $id);
        }

        if (!$result) {
            return null;
        }

        $product = new self(
            $result['sku'],
            $result['name'],
            $result['price'],
            $result['weight_kg']
        );

        $product->setId($result['product_id']);

        return $product;
    }

    public function display(): array
    {
        return [
            'sku' => $this->sku,
            'name' => $this->name,
            'price' => $this->price,
            'weight' => $this->weight,
            'type' => "App\Models\Book",
            'id' => $this->getId(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->display();
    }
}