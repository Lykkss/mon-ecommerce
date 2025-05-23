<?php
namespace App\Models;

use App\Core\Database;

class Stock
{
    public static function findByArticle(int $articleId): ?array
    {
        $stmt = Database::getInstance()
            ->prepare("SELECT * FROM stock WHERE article_id = ?");
        $stmt->execute([$articleId]);
        return $stmt->fetch() ?: null;
    }

    public static function updateQuantity(int $articleId, int $quantity): void
    {
        $stmt = Database::getInstance()
            ->prepare("UPDATE stock SET quantity = ? WHERE article_id = ?");
        $stmt->execute([$quantity, $articleId]);
    }
}