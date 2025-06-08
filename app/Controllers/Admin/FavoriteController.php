<?php
namespace App\Controllers\Admin;

use App\Core\Database;

class FavoriteController
{
    public function index(): void
    {
        $favs = Database::getInstance()
            ->query("
                SELECT f.user_id, f.product_id, f.created_at,
                       u.username, p.name AS product_name
                  FROM favorites f
                  JOIN users u ON u.id = f.user_id
                  JOIN products p ON p.id = f.product_id
                 ORDER BY f.created_at DESC
            ")
            ->fetchAll(\PDO::FETCH_ASSOC);

        $adminFavorites = $favs;
        require __DIR__ . '/../../Views/layout.php';
    }

    public function delete(int $user_id, int $product_id): void
    {
        Database::getInstance()
            ->prepare("DELETE FROM favorites WHERE user_id = ? AND product_id = ?")
            ->execute([$user_id, $product_id]);
        $_SESSION['success'] = "Favori retiré.";
        header('Location: /admin/favorites');
        exit;
    }
}
