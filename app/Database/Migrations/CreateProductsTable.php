<?php

namespace App\Database\Migrations;

use PDO;
use PDOException;
class CreateProductsTable
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function up()
    {
        $this->pdo->exec("
            CREATE TABLE products (
                id INT AUTO_INCREMENT PRIMARY KEY,
                sku VARCHAR(255) UNIQUE,
                name VARCHAR(255),
                price FLOAT,
                type VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        echo "Migration successful. Products table created. \n";

    }

    public function down()
    {
        $this->pdo->exec("DROP TABLE products");
        echo "Migration successful. Products table dropped. \n";
    }
}

