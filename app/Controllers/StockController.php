<?php
namespace App\Controllers;

use App\Models\Stock;
use App\Models\Product;

class StockController
{
    /**
     * Augmente la quantité en stock d’un produit.
     */
    public function increase(int $id): void
    {
        // (1) Protection — ici on pourrait limiter aux admins ou à l’auteur
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // (2) Récupère la quantité demandée (au moins 1)
        $add = max(1, (int)($_POST['quantity'] ?? 0));

        // (3) Lit l’ancien stock
        $row = Stock::findByArticle($id);
        $old = $row['quantity'] ?? 0;

        // (4) Calcule et écrit le nouveau stock
        $new = $old + $add;
        Stock::updateQuantity($id, $new);

        // (5) Redirection vers la fiche produit
        header('Location: /produit/' . $id);
        exit;
    }
}
