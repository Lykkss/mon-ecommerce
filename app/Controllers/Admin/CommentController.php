<?php
namespace App\Controllers\Admin;

use App\Core\Database;

class CommentController
{
    public function index(): void
    {
        $comments = Database::getInstance()
            ->query("
                SELECT c.id, c.content, c.created_at,
                       u.username, p.name AS product_name
                  FROM comments c
                  JOIN users u ON u.id = c.user_id
                  JOIN products p ON p.id = c.product_id
                 ORDER BY c.created_at DESC
            ")
            ->fetchAll(\PDO::FETCH_ASSOC);

        $adminComments = $comments;
        require __DIR__ . '/../../Views/layout.php';
    }

    public function delete(int $id): void
    {
        Database::getInstance()
            ->prepare("DELETE FROM comments WHERE id = ?")
            ->execute([$id]);
        $_SESSION['success'] = "Commentaire supprimé.";
        header('Location: /admin/comments');
        exit;
    }
}
