<?php
namespace App\Controllers;

use App\Core\Database;
use App\Models\Category;
use App\Models\User;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Stock;

class HomeController
{
       /**
     * GET /
     * Liste tous les produits, avec filtres par q, price_min, price_max, in_stock et category.
     */
    public function index(): void
    {
        $db = Database::getInstance();

        // 1) Lecture des GET
        $categoryId = isset($_GET['category_id'])
        && ctype_digit($_GET['category_id'])
        && (int)$_GET['category_id'] > 0
        ? (int)$_GET['category_id']
        : null;

        // 2) Clause WHERE dynamique
        $wheres = [];
        $params = [];

        if ($categoryId !== null) {
        $wheres[]         = 'p.category_id = :cat';
        $params['cat']    = $categoryId;
        }

        $whereSql = $wheres ? 'WHERE '.implode(' AND ', $wheres) : '';

        // 3) Requête
        $sql = "
        SELECT
        p.id, p.name, p.description, p.price, p.image,
        COALESCE(s.quantity,0) AS stock,
        p.category_id
        FROM products p
        LEFT JOIN stock s ON s.article_id = p.id
        {$whereSql}
        ORDER BY p.created_at DESC
        ";
        $stmt    = Database::getInstance()->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // 4) Charge les catégories
        $categories = Category::all();

        // 5) Envoie à la vue
        require __DIR__ . '/../Views/layout.php';

    }

    /**
     * GET /produit/{id}
     * Affiche le détail d’un produit
     */
    public function show(int $id): void
    {
        $product = \App\Models\Product::find($id);
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
