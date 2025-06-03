<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Stock
{
    /**
     * Retourne la ligne de stock pour un produit donné
     */
    public static function findByArticle(int $articleId): ?array
    {
        $stmt = Database::getInstance()
            ->prepare("SELECT * FROM stock WHERE article_id = ?");
        $stmt->execute([$articleId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Remplace la quantité par une valeur donnée
     */
    public static function updateQuantity(int $articleId, int $quantity): void
    {
        $stmt = Database::getInstance()
            ->prepare("UPDATE stock SET quantity = ? WHERE article_id = ?");
        $stmt->execute([$quantity, $articleId]);
    }

      /**
     * Décrémente la quantité en stock de $qty, sans passer en dessous de zéro.
     */
    public static function decrement(int $articleId, int $qty): void
    {
        $row = self::findByArticle($articleId);
        $current = $row['quantity'] ?? 0;
        $newQty  = max(0, $current - $qty);
        self::updateQuantity($articleId, $newQty);
    }

    /**
     * Incrémente la quantité en stock
     */
    public static function increment(int $articleId, int $qty): void
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            UPDATE stock
            SET quantity = quantity + :q
            WHERE article_id = :id
        ");
        $stmt->execute([
            'q'  => $qty,
            'id' => $articleId,
        ]);
    }
}
