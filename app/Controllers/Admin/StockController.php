<?php 

namespace App\Controllers\Admin;

use App\Models\Product;
use App\Models\Stock;

class StockController
{
    public function index(): void
    {
        $products = Product::all();
        $stocks   = [];
        foreach ($products as $p) {
            $stocks[$p['id']] = Stock::findByArticle((int)$p['id'])['quantity'] ?? 0;
        }

        $adminStock = true;
        require __DIR__ . '/../../Views/layout.php';
    }

    public function updateSubmit(): void
    {
        $articleId = (int)($_POST['article_id'] ?? 0);
        $newQty    = (int)($_POST['quantity']   ?? 0);

        if ($articleId <= 0 || $newQty < 0) {
            $_SESSION['errors'][] = 'Données de stock invalides.';
        } else {
            Stock::updateQuantity($articleId, $newQty);
            $_SESSION['success'] = "Stock du produit #{$articleId} mis à jour à {$newQty}.";
        }

        header('Location: /admin/stock');
        exit;
    }
}
