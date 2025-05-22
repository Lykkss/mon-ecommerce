<?php
namespace App\Core;
use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        $host = 'db';             // Nom du service Docker
        $db   = 'pokemon_db';
        $user = 'root';
        $pass = 'root';
        $dsn  = "mysql:host={$host};dbname={$db};charset=utf8mb4";

        try {
            $this->pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die("Erreur BDD : " . $e->getMessage());
        }
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->pdo;
    }
}
