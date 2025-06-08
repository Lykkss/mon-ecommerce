<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Category
{
    public static function all(): array
    {
        return Database::getInstance()
            ->query("SELECT * FROM categories ORDER BY name")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(string $name): int
    {
        $stmt = Database::getInstance()
            ->prepare("INSERT INTO categories (name) VALUES (:n)");
        $stmt->execute(['n' => $name]);
        return (int) Database::getInstance()->lastInsertId();
    }

    public static function delete(int $id): void
    {
        Database::getInstance()
            ->prepare("DELETE FROM categories WHERE id = ?")
            ->execute([$id]);
    }

    public static function countAll(): int
    {
        $stmt = Database::getInstance()
            ->query("SELECT COUNT(*) AS cnt FROM categories");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['cnt'] ?? 0);
    }
}
