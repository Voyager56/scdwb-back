<?php

namespace App\Database\Migrations;

use PDO;
use PDOException;
class CreateBooksTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function up()
    {
        $this->pdo->exec("
        CREATE TABLE books (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT,
            weight_kg FLOAT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            )
        ");
        echo "Migration successful. Books table created. \n";

    }

    public function down()
    {
        $this->pdo->exec("DROP TABLE IF EXISTS books");
        echo "Migration successful. Books table dropped. \n";
    }
}
