<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Stock
{
    /**
     * Retourne la ligne de stock pour un produit donné.
     */
    public static function findByArticle(int $articleId): ?array
    {
        $stmt = Database::getInstance()
            ->prepare("SELECT article_id, quantity FROM stock WHERE article_id = ?");
        $stmt->execute([$articleId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Met à jour la quantité d’un article.
     * La ligne doit déjà exister, créée lors de la création du produit.
     */
    public static function updateQuantity(int $articleId, int $quantity): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE stock SET quantity = :q WHERE article_id = :id");
        $stmt->execute([
            'q'  => $quantity,
            'id' => $articleId,
        ]);
    }

    /**
     * Décrémente la quantité en stock de $qty, sans passer en dessous de zéro.
     */
    public static function decrement(int $articleId, int $qty): void
    {
        $row     = self::findByArticle($articleId);
        $current = $row['quantity'] ?? 0;
        $newQty  = max(0, $current - $qty);
        self::updateQuantity($articleId, $newQty);
    }

    /**
     * Incrémente la quantité en stock de $qty.
     */
    public static function increment(int $articleId, int $qty): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE stock SET quantity = quantity + :q WHERE article_id = :id");
        $stmt->execute([
            'q'  => $qty,
            'id' => $articleId,
        ]);
    }
}
