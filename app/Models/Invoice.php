<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Invoice
{
    /** Crée une facture et retourne son ID */
    public static function create(int $userId, float $total, array $billing): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO invoices 
              (user_id, total_amount, billing_address, billing_city, billing_zip)
            VALUES 
              (:u, :ta, :a, :c, :z)
        ");
        $stmt->execute([
            'u'  => $userId,
            'ta' => $total,
            'a'  => $billing['address'],
            'c'  => $billing['city'],
            'z'  => $billing['zip'],
        ]);
        return (int)$db->lastInsertId();
    }
    /**
     * Retourne toutes les commandes d’un user
     */
    public static function findByUser(int $userId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT * FROM invoices
            WHERE user_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // app/Models/Invoice.php
    public static function findById(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM invoices WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }


    /**
     * Retourne les lignes d’une facture
     */
    public static function findItems(int $invoiceId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT 
            ii.product_id,
            p.name        AS product_name,
            ii.quantity,
            ii.unit_price
            FROM invoice_items ii
            JOIN products p ON p.id = ii.product_id
            WHERE ii.invoice_id = ?
        ");
        $stmt->execute([$invoiceId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
