<?php
namespace core\db;

use PDO;
use PDOException;
class Db
{
    private static $pdo = null;

    public static function pdo()
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        try {
            $dsn    = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_NAME);
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT => true,
            ];

            return self::$pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }
}