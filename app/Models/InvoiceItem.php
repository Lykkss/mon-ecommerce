<?php
namespace App\Models;

use App\Core\Database;

class InvoiceItem
{
    /**
     * Crée une ligne de facture.
     *
     * @param int   $invoiceId
     * @param int   $productId
     * @param int   $qty
     * @param float $price
     */
    public static function create(int $invoiceId, int $productId, int $qty, float $price): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO invoice_items
               (invoice_id, product_id, quantity, unit_price)
            VALUES
               (:inv,        :prd,        :q,        :p)
        ");
        $stmt->execute([
            'inv' => $invoiceId,
            'prd' => $productId,
            'q'   => $qty,
            'p'   => $price,
        ]);
    }
}
