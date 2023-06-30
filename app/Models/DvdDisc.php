<?php

namespace App\Models;

use App\Database\Database;
use App\Exceptions\DatabaseException;
use App\Exceptions\RequestValidationException;
use PDOException;

class DvdDisc extends Product implements \JsonSerializable
{
    protected int $size;

    public function __construct(string $sku, string $name, float $price, int $size)
    {
        parent::__construct($sku, $name, $price);
        $this->size = $size;
    }

    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function save(): void
    {

        if($this->size <= 0) {
            throw new   RequestValidationException(['size' => 'Size must be greater than 0']);
        }

        parent::saveProduct('App\Models\DvdDisc');
        $db = new Database();

        $data = [
            'size_mb' => $this->size,
            'product_id' => $db->getLastInsertId()
        ];

        $query = "INSERT INTO dvds (size_mb, product_id) VALUES ( :size_mb, :product_id)";

        try {
            $db->execute($query, $data);
        } catch (PDOException $e) {
            $query = "DELETE FROM products WHERE id = :id";
            $db->execute($query, ['id' => $db->getLastInsertId()]);
            throw new DatabaseException($e->getMessage(), $e->getCode(), $e, $this->size);
        }



    }

    public function display(): array
    {
        return [
            'sku' => $this->sku,
            'name' => $this->name,
            'price' => $this->price,
            'size' => $this->size,
            'type' => 'App\Models\DvdDisc',
            'id' => $this->getId()
        ];
    }

    public static function findByProductId(int $id): ?DvdDisc
    {
        // TODO: Implement findByProductId() method.

        $db = new Database();
        $query = "SELECT * FROM dvds WHERE product_id = :id";
        $data = ['id' => $id];

        try {
            $result = $db->fetch($query, $data);
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode(), $e);
        }

        if (empty($results)) {
            return null;
        }

        $dvd = new DvdDisc(
            $result['sku'],
            $result['name'],
            $result['price'],
            $result['size_mb']
        );

        $dvd->setId($id);

        return $dvd;
    }


    public function jsonSerialize(): array
    {
        // TODO: Implement jsonSerialize() method.
        return $this->display();
    }
}