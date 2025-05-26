<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Product;
use App\Models\Stock;

// 1) Récupère tous les produits
$products = Product::all();

// 2) Parcours chaque produit et vérifie le stock
foreach ($products as $p) {
    $id  = (int)$p['id'];
    $qty = Stock::findByArticle($id)['quantity'] ?? 0;

    echo sprintf(
        "Produit #%d — %s : stock %d\n",
        $id,
        $p['title'] ?? $p['name'] ?? '–',
        $qty
    );

    if ($id === 3 && $qty <= 0) {
        echo "→ Stock insuffisant pour le produit #3.\n";
    }
}
