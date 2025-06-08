<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Favorite
{
    public static function isFavorited(int $userId, int $productId): bool
    {
        $stmt = Database::getInstance()->prepare("
            SELECT 1 FROM favorites
             WHERE user_id = :uid AND product_id = :pid
        ");
        $stmt->execute(['uid' => $userId, 'pid' => $productId]);
        return (bool)$stmt->fetchColumn();
    }

    public static function toggle(int $userId, int $productId): void
    {
        $db = Database::getInstance();
        if (self::isFavorited($userId, $productId)) {
            $db->prepare("
                DELETE FROM favorites
                 WHERE user_id = :uid AND product_id = :pid
            ")->execute(['uid' => $userId, 'pid' => $productId]);
        } else {
            $db->prepare("
                INSERT INTO favorites (user_id, product_id)
                VALUES (:uid, :pid)
            ")->execute(['uid' => $userId, 'pid' => $productId]);
        }
    }

    public static function findByUser(int $userId): array
    {
        $stmt = Database::getInstance()->prepare("
            SELECT product_id FROM favorites
             WHERE user_id = :uid
        ");
        $stmt->execute(['uid' => $userId]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'product_id');
    }
}
