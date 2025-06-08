<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Comment
{
    public static function findByProduct(int $productId): array
    {
        $stmt = Database::getInstance()->prepare("
            SELECT c.*, u.fullname 
              FROM comments c
              JOIN users u ON u.id = c.user_id
             WHERE c.product_id = :pid
             ORDER BY c.created_at DESC
        ");
        $stmt->execute(['pid' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(int $userId, int $productId, string $content): void
    {
        $stmt = Database::getInstance()->prepare("
            INSERT INTO comments (user_id, product_id, content)
            VALUES (:uid, :pid, :c)
        ");
        $stmt->execute([
            'uid' => $userId,
            'pid' => $productId,
            'c'   => $content,
        ]);
    }
}
