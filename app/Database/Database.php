<?php

namespace App\Database;

use App\Helpers\Errors\DatabaseException;
use PDO;
use PDOException;
use App\Helpers\Env\EnvLoader;
class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (!self::$connection) {
            $env = new EnvLoader();
            $env->load();
            try {
                $host = $env->get('DB_HOST');
                $database = $env->get('DB_DATABASE');
                $username = $env->get('DB_USERNAME');
                $password = $env->get('DB_PASSWORD');

                self::$connection = new PDO("mysql:host=$host;dbname=$database", $username, $password);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die('Database connection error: ' . $e->getMessage());
            }
        }
        return self::$connection;
    }

    public static function execute(string $query, array $params = []): bool
    {
        try {
            $stmt = self::getConnection()->prepare($query);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            die('Query execution failed: ' . $e->getMessage());
        }
    }

    public static function fetch(string $query, array $params = []): array
    {
        try {
            $stmt = self::getConnection()->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            die('Query execution failed: ' . $e->getMessage());
        }
    }

    public static function fetchAll(string $query, array $params = []): array
    {
        try {
            $stmt = self::getConnection()->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            die('Query execution failed: ' . $e->getMessage());
        }
    }


    public static function getLastInsertId(): string
    {
        return self::getConnection()->lastInsertId();
    }
}
