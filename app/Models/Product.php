<?php
namespace App\Models;
use App\Core\Database;

class Product
{
    public static function all(): array
    {
        $stmt = Database::getInstance()->query("SELECT * FROM products");
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::getInstance()
            ->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
}
