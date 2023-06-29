<?php

namespace App\Database\Migrations;

use PDO;
use PDOException;
class CreateFurnitureTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function up()
    {
        $this->pdo->exec("
        CREATE TABLE furniture (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT,
            height_cm INT,
            width_cm INT,
            length_cm INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            )
        ");
        echo "Migration successful. Furniture table created. \n";

    }

    public function down()
    {
        $this->pdo->exec("DROP TABLE IF EXISTS furniture");
        echo "Migration successful. Furniture table dropped. \n";
    }

}