<?php
namespace App\Controllers;

use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use App\Models\Comment;
use App\Models\Favorite;

class HomeController
{
    /**
     * GET /
     * Liste tous les produits
     */
    public function index(): void
    {
        $products = Product::all();
        require __DIR__ . '/../Views/layout.php';
    }

    /**
     * GET /produit/{id}
     * Affiche le détail d’un produit
     */
    public function show(int $id): void
    {
        $product = Product::find($id);
        if (!$product) {
            header("HTTP/1.0 404 Not Found");
            exit("Produit introuvable");
        }

        // Stock
        $stockData = Stock::findByArticle($id);
        $stock     = $stockData['quantity'] ?? 0;

        // Auteur
        $seller = User::findById((int)$product['author_id']);

        // Commentaires
        $comments = Comment::findByProduct($id);

        // Favori ?
        $isFav = false;
        if (!empty($_SESSION['user_id'])) {
            $isFav = Favorite::isFavorited((int)$_SESSION['user_id'], $id);
        }

        $productView = true;
        require __DIR__ . '/../Views/layout.php';
    }

    /**
     * POST /produit/{id}/comment
     * Ajoute un commentaire au produit
     */
    public function addComment(int $productId): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        $content = trim($_POST['content'] ?? '');
        if ($content !== '') {
            Comment::create((int)$_SESSION['user_id'], $productId, $content);
        }
        header("Location: /produit/{$productId}#comments");
        exit;
    }

    /**
     * POST /produit/{id}/favorite
     * Bascule le statut favori pour ce produit
     */
    public function toggleFavorite(int $productId): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        Favorite::toggle((int)$_SESSION['user_id'], $productId);
        header("Location: /produit/{$productId}");
        exit;
    }
}
