<?php
namespace App\Controllers;

use App\Models\Product;
use App\Models\Stock;

class HomeController
{
    /**
     * Affiche la liste des produits
     */
    public function index(): void
    {
        $products = Product::all();
        require __DIR__ . '/../Views/layout.php';
    }

    /**
     * Affiche le détail d'un produit
     *
     * @param int $id
     */
    public function show(int $id): void
    {
        $product = Product::find($id);
        if (!$product) {
            header("HTTP/1.0 404 Not Found");
            exit("Produit introuvable");
        }

        // Récupération du stock pour ce produit
        $stockData = Stock::findByArticle($id);
        $stock = $stockData['quantity'] ?? 0;

        require __DIR__ . '/../Views/layout.php';
    }
}
