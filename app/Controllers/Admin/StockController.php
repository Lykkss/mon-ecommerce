<?php
namespace App\Controllers\Admin;

use App\Models\Product;
use App\Models\Stock;

class StockController
{
    /**
     * Affiche la page “Gestion du stock” (GET /admin/stock)
     */
    public function index(): void
    {
        // S'assurer que la session est démarrée pour afficher messages
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1) On récupère tous les produits
        $products = Product::all();

        // 2) On construit un tableau associatif [product_id => quantité]
        $stocks = [];
        foreach ($products as $p) {
            $row = Stock::findByArticle((int)$p['id']);
            $stocks[$p['id']] = $row['quantity'] ?? 0;
        }

        // 3) Flag pour la vue
        $adminStock = true;

        // 4) On inclut le layout, qui chargera Views/admin/stock.php
        require __DIR__ . '/../../Views/layout.php';
    }

    /**
     * Reçoit le POST /admin/stock/update et met à jour la base
     */
    public function updateSubmit(): void
    {
        // S'assurer que la session est démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $articleId = (int)($_POST['article_id'] ?? 0);
        $newQty    = (int)($_POST['quantity'] ?? -1);

        // Debug minimal si rien ne se passe
        if (!isset($_POST['article_id'])) {
            $_SESSION['errors'][] = 'Aucune donnée envoyée pour article_id.';
            header('Location: /admin/stock');
            exit;
        }

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
