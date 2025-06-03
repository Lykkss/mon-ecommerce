<?php
namespace App\Controllers;

use App\Core\Database;
use App\Models\Product;
use App\Models\Stock;

class SellController
{
    public function createForm(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login'); exit;
        }
        $sell = true;
        require __DIR__ . '/../Views/layout.php';
    }

    public function createSubmit(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login'); exit;
        }
        $title = trim($_POST['title'] ?? '');
        $desc  = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $qty   = (int)($_POST['stock'] ?? 0);
        $errors = [];
        if (!$title) $errors[] = 'Titre requis';
        if ($price <= 0) $errors[] = 'Prix invalide';
        if ($qty < 0) $errors[] = 'Stock invalide';
        if ($errors) {
            $_SESSION['errors'] = $errors;
            header('Location: /sell'); exit;
        }
        // Image upload
        $imgPath = null;
        if (!empty($_FILES['image']['tmp_name'])) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imgDir = __DIR__ . '/../../public/assets/products/';
            if (!is_dir($imgDir)) mkdir($imgDir, 0755, true);
            $filename = 'prod_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $imgDir . $filename);
            $imgPath = 'assets/products/' . $filename;
        }

        $db = Database::getInstance();
        // Insertion produit
        $stmt = $db->prepare(
            "INSERT INTO products (title, description, price, image, author_id) " .
            "VALUES (:t, :d, :p, :i, :u)"
        );
        $stmt->execute([
            't' => $title,
            'd' => $desc,
            'p' => $price,
            'i' => $imgPath,
            'u' => $_SESSION['user_id'],
        ]);
        $prodId = (int)$db->lastInsertId();

        // Insertion stock
        $stmt2 = $db->prepare("INSERT INTO stock (article_id, quantity) VALUES (?, ?)");
        $stmt2->execute([$prodId, $qty]);

        header('Location: /'); exit;
    }

    public function editForm(int $id): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login'); exit;
        }
        $prod = Product::find($id);
        if (!$prod || $prod['author_id'] != $_SESSION['user_id']) {
            header('Location: /'); exit;
        }
        $stock = Stock::findByArticle($id)['quantity'] ?? 0;
        $edit  = true;
        require __DIR__ . '/../Views/layout.php';
    }

    public function editSubmit(int $id): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login'); exit;
        }
        $prod = Product::find($id);
        if (!$prod || $prod['author_id'] != $_SESSION['user_id']) {
            header('Location: /'); exit;
        }
        $title = trim($_POST['title'] ?? '');
        $desc  = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $qty   = (int)($_POST['stock'] ?? 0);

        $db = Database::getInstance();
        // Update produit
        $stmt = $db->prepare(
            "UPDATE products SET title=:t, description=:d, price=:p" .
            " WHERE id=:id"
        );
        $stmt->execute(['t' => $title, 'd' => $desc, 'p' => $price, 'id' => $id]);
        // Update stock
        $stmt2 = $db->prepare(
            "UPDATE stock SET quantity=? WHERE article_id=?"
        );
        $stmt2->execute([$qty, $id]);

        header('Location: /produit/' . $id); exit;
    }

    public function delete(int $id): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login'); exit;
        }
        $prod = Product::find($id);
        if (!$prod || $prod['author_id'] != $_SESSION['user_id']) {
            header('Location: /'); exit;
        }
        $db = Database::getInstance();
        $db->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);

        header('Location: /'); exit;
    }
}