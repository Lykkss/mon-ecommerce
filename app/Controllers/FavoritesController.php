<?php
namespace App\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use App\Models\Stock;

class FavoritesController
{
    /**
     * GET /favoris
     */
    public function index(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = (int)$_SESSION['user_id'];
        // Récupère la liste des product_id favoris
        $favIds = Favorite::findByUser($userId);

        // Charge les produits
        $products = [];
        foreach ($favIds as $pid) {
            if ($p = Product::find($pid)) {
                // quantité de stock pour info
                $stockData = Stock::findByArticle($pid);
                $p['stock'] = $stockData['quantity'] ?? 0;
                $products[] = $p;
            }
        }

        $favorites = true;  // flag pour layout.php
        require __DIR__ . '/../Views/layout.php';
    }
}
