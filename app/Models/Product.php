<?php

namespace App\Models;

use App\Database\Database;
use PDOException;
use App\Helpers\Errors\DatabaseException;

abstract class Product
{
    protected string $sku;
    protected string $name;
    protected float $price;
    protected int $id;

    protected $type;

    protected string $product_type;

    public function __construct(string $sku, string $name, float $price, string $type, int $id)
    {
        $this->sku = $sku;
        $this->name = $name;
        $this->price = $price;
        $this->type = $type;
        $this->id = $id;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function setSku(string $sku): void
    {
        $this->sku = $sku;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    protected function saveProduct($product_type): void {
        $data = [
            'sku' => $this->sku,
            'name' => $this->name,
            'price' => $this->price,
            'type' => $product_type
        ];

        $db = new Database();

        $query = "INSERT INTO products (sku, name, price, type) VALUES (:sku, :name, :price, :type)";

        try {
            $db->execute($query, $data);
            $this->id = $db->getLastInsertId();
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode(), $e, $data);
        }
    }

    public static function getProductType(string $id): ?string{
        $db = new Database();
        $query = "SELECT type FROM products WHERE id = :id";
        $data = ['id' => $id];

        try {
            $result = $db->fetch($query, $data);
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode(), $e, $id);
        }

        if (!$result) {
            return null;
        }

        return $result['type'];

    }
    abstract public function save(): void;
    abstract public function display(): array;

    abstract public static function findByProductId(int $id);
}
