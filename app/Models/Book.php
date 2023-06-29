<?php

namespace App\Models;

use App\Database\Database;
use PDOException;
use App\Helpers\Errors\DatabaseException;
class Book extends Product
{
    protected float $weight;

    public function __construct(string $sku, string $name, float $price, int $id, string $type ,float $weight)
    {
        parent::__construct($sku, $name, $price, $type, $id);
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
        parent::saveProduct('book');
        $db = new Database();

        $data = [
            'weight_kg' => $this->weight,
            'product_id' => $db->getLastInsertId()
        ];

        $query = "INSERT INTO books (weight_kg, product_id) VALUES ( :weight_kg, :product_id)";

        try {
            $db->execute($query, $data);
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode(), $e, $this->weight);
        }
    }

    public static function findById(int $id): ?self
    {
        $db = new Database();
        $query = "SELECT * FROM books WHERE id = :id";
        $data = ['id' => $id];

        try {
            $result = $db->fetch($query, $data);
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode(), $e, $id);
        }

        if (!$result) {
            return null;
        }

        return new self(
            $result['sku'],
            $result['name'],
            $result['price'],
            $result['weight_kg']
        );
    }

    public static function getAll(): array
    {
        $db = new Database();
        $query = "SELECT * FROM books";
        $data = [];

        try {
            $results = $db->fetchAll($query, $data);
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode(), $e);
        }

        $books = [];
        foreach ($results as $result) {
            $books[] = new self(
                $result['sku'],
                $result['name'],
                $result['price'],
                $result['weight_kg']
            );
        }

        return $books;
    }

    public static function findByProductId(int $id)
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

        return new self(
            $result['sku'],
            $result['name'],
            $result['price'],
            $result['product_id'],
            $result['type'],
            $result['weight_kg'],
        );
    }

    public function display(): array
    {
        return [
            'sku' => $this->sku,
            'name' => $this->name,
            'price' => $this->price,
            'weight' => $this->weight
        ];
    }

}