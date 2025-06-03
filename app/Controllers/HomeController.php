<?php
namespace App\Controllers;

use App\Models\Product;
use App\Models\Stock;

class HomeController
{
    public function index(): void
    {
        // Récupérer tous les produits (avec quantité de stock si besoin)
        // Ici on choisit la version “a” : on ne ramène pas le stock au controller
        $products = Product::all();
        require __DIR__ . '/../Views/layout.php';
    }

    public function show(int $id): void
    {
        $product = Product::find($id);
        if (!$product) {
            header("HTTP/1.0 404 Not Found");
            exit("Produit introuvable");
        }
        $stockData = Stock::findByArticle($id);
        $stock = $stockData['quantity'] ?? 0;
        require __DIR__ . '/../Views/layout.php';
    }
}
