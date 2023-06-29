<?php

require_once __DIR__ . '/autoload.php';

use App\Database\Database;
use App\Database\Migrations\CreateBooksTable;
use App\Database\Migrations\CreateProductsTable;
use App\Helpers\Errors\DatabaseException;

class MigrationScript
{
    private $pdo;
    private $migrations;
    private $direction;

    public function __construct()
    {
        $this->pdo = Database::getConnection();

        $this->migrations = [
            new CreateProductsTable($this->pdo),
            new CreateBooksTable($this->pdo),
        ];
    }

    public function run()
    {
        echo "Please enter command (migrate or rollback): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);

        if (trim($line) == 'migrate') {
            $this->direction = 'migrate';
            $this->runMigrations();
        } else if (trim($line) == 'rollback') {
            $this->direction = 'rollback';
            $this->rollbackMigrations();
        } else {
            echo "Invalid command";
        }
    }

    private function runMigrations()
    {
        try {
            $this->pdo->beginTransaction();
            foreach ($this->migrations as $migration) {
                $migration->up();
            }
            $this->pdo->commit();
            echo "Migrations successful.\n";
        } catch (\PDOException $e) {
            throw new DatabaseException("Migration Failed", 404, $e);
        }
    }

    private function rollbackMigrations()
    {
        if ($this->direction === 'rollback') {
            $this->migrations = array_reverse($this->migrations);
        }
        try {
            $this->pdo->beginTransaction();
            foreach ($this->migrations as $migration) {
                $migration->down();
            }
            $this->pdo->commit();
            echo "Rollback successful.\n";
        } catch (\PDOException $e) {
            echo $e;
        }
    }
}

$migrationScript = new MigrationScript();
$migrationScript->run();
