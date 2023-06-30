<?php

namespace App\Models;

use App\Database\Database;
use App\Exceptions\DatabaseException;
use PDOException;

abstract class Product
{
    protected string $sku;
    protected string $name;
    protected float $price;
    protected int $id;

    protected $type;

    protected string $product_type;

    public function __construct(string $sku, string $name, float $price)
    {
        $this->sku = $sku;
        $this->name = $name;
        $this->price = $price;
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

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
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
            $this->setId($db->getLastInsertId());
        } catch (PDOException $e) {
            if($e->getCode() == '23000') {
                throw new DatabaseException('SKU already exists', $e->getCode(), $e);
            }else {
                throw new DatabaseException($e->getMessage(), $e->getCode(), $e);
            }
        }
    }

    public static function findById(int $id){
        $db = new Database();
        $query = "SELECT * FROM products WHERE id = :id";
        $data = ['id' => $id];

        try {
            $result = $db->fetch($query, $data);
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode(), $e, $id);
        }

        if (!$result) {
            return null;
        }

        $product_type = $result['type'];
        return $product_type::findByProductId($id);
    }

    public static function getAll(){
        $db = new Database();
        $query = "SELECT * FROM products";

        try {
            $result = $db->fetchAll($query);
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode(), $e);
        }

        $products = [];

        foreach ($result as $product) {
            $product_type = $product['type'];
            $products[] = $product_type::findByProductId($product['id']);
        }

        return $products;
    }

    abstract public function save(): void;
    abstract public function display(): array;

    abstract public static function findByProductId(int $id);
}
