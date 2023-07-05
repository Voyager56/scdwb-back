<?php

namespace App\Models;

use App\Database\Database;
use App\Exceptions\DatabaseException;
use App\Exceptions\RequestValidationException;
use PDOException;

class Furniture extends Product implements \JsonSerializable
{
    protected string $sku;
    protected string $name;
    protected float $price;

    protected string $height;
    protected string $width;
    protected string $length;

    public function __construct(string $sku, string $name, float $price, string $height, string $width, string $length)
    {
        parent::__construct($sku, $name, $price);
        $this->height = $height;
        $this->width = $width;
        $this->length = $length;
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

    public function getHeight(): string
    {
        return $this->height;
    }

    public function getWidth(): string
    {
        return $this->width;
    }

    public function getLength(): string
    {
        return $this->length;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setHeight(string $height): void
    {
        $this->height = $height;
    }

    public function setWidth(string $width): void
    {
        $this->width = $width;
    }

    public function setLength(string $length): void
    {
        $this->length = $length;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }


    public function save(): void
    {
        // TODO: Implement save() method.

        if($this->height <= 0) {
            throw new   RequestValidationException(['height' => 'Height must be greater than 0']);
        }
        if($this->width <= 0) {
            throw new   RequestValidationException(['width' => 'Width must be greater than 0']);
        }
        if($this->length <= 0) {
            throw new   RequestValidationException(['length' => 'Length must be greater than 0']);
        }

        parent::saveProduct('App\Models\Furniture');
        $db = new Database();
        $query = "INSERT INTO furniture (height_cm, width_cm, length_cm, product_id) VALUES (:height_cm, :width_cm, :length_cm, :product_id)";

        $data = [
            'height_cm' => $this->height,
            'width_cm' => $this->width,
            'length_cm' => $this->length,
            'product_id' => $db->getLastInsertId()
        ];

        try {
            $db->execute($query, $data);
        } catch (PDOException $e) {
            $query = "DELETE FROM products WHERE id = :id";
            $db->execute($query, ['id' => $db->getLastInsertId()]);
            throw new DatabaseException($e->getMessage());
        }
    }

    public function display(): array
    {
        // TODO: Implement display() method.

        return [
            'sku' => $this->sku,
            'name' => $this->name,
            'price' => $this->price,
            'height' => $this->height,
            'width' => $this->width,
            'length' => $this->length,
            'type' => 'App\Models\Furniture',
             'id' => $this->getId()
        ];
    }

    public static function findByProductId(array $product_data): ?Furniture
    {
        // TODO: Implement findByProductId() method.

        $db = new Database();
        $query = "SELECT * FROM furniture WHERE product_id = :id";
        $data = ['id' => $product_data['id']];

        try {
            $result = $db->fetch($query, $data);
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage());
        }
        if (empty($result)) {
            return null;
        }

        $furniture =  new Furniture(
            $product_data['sku'],
            $product_data['name'],
            $product_data['price'],
            height: $result['height_cm'],
            width: $result['width_cm'],
            length: $result['length_cm']
        );

        $furniture->setId($product_data['id']);

        return $furniture;
    }

    public function jsonSerialize(): array
    {
        // TODO: Implement jsonSerialize() method.
        return $this->display();
    }
}