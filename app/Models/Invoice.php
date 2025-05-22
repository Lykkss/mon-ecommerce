<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Invoice
{
    /** Retourne toutes les commandes d’un user */
    public static function findByUser(int $userId): array
    {
        $stmt = Database::getInstance()
            ->prepare("SELECT * FROM invoices WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /** Retourne les détails d’une facture (items) */
    public static function findItems(int $invoiceId): array
    {
        $db = Database::getInstance();
        // On suppose qu’on a lié items + articles dans la DB
        $stmt = $db->prepare("
            SELECT a.id, a.title, a.price, ci.quantity
            FROM cart_items ci
            JOIN articles a ON a.id = ci.article_id
            WHERE ci.user_id = (
                SELECT user_id FROM invoices WHERE id = ?
            )
            AND ci.created_at <= (
                SELECT created_at FROM invoices WHERE id = ?
            )
        ");
        $stmt->execute([$invoiceId, $invoiceId]);
        return $stmt->fetchAll();
    }
}
