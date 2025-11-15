<?php
namespace App\Core;
use PDO, PDOException;
class Database {
    private static ?PDO $instance = null;
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            try {
                self::$instance = new PDO(Config::$db_dsn, Config::$db_user, Config::$db_pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                error_log($e->getMessage());
                throw $e;
            }
        }
        return self::$instance;
    }
}
