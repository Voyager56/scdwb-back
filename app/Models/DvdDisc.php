<?php

namespace App\Models;

use App\Database\Database;
use PDOException;
use App\Helpers\Errors\DatabaseException;
class DvdDisc extends Product
{
    protected int $size;

    public function __construct(string $sku, string $name, float $price, string $type, int $id, int $size)
    {
        parent::__construct($sku, $name, $price, $type, $id);
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
            throw new DatabaseException($e->getMessage(), $e->getCode(), $e, $this->size);
        }



    }

    public function display(): array
    {
        // TODO: Implement display() method.
    }

    public static function findByProductId(int $id)
    {
        // TODO: Implement findByProductId() method.
    }
}