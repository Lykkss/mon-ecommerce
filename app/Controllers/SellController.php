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
            header('Location: /login');
            exit;
        }
        $sell = true;
        require __DIR__ . '/../Views/layout.php';
    }

    public function createSubmit(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Récupération et validation des champs
        $name        = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price       = (float)($_POST['price'] ?? 0);
        $qty         = (int)($_POST['stock'] ?? 0);
        $errors      = [];

        if ($name === '') {
            $errors[] = 'Titre requis.';
        }
        if ($price <= 0) {
            $errors[] = 'Prix invalide.';
        }
        if ($qty < 0) {
            $errors[] = 'Stock invalide.';
        }

        // Traitement de l'image
        $imgPath = null;
        if (!empty($_FILES['image']['tmp_name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            if ($file['size'] > 2_000_000) {
                $errors[] = 'L’image ne doit pas dépasser 2 Mo.';
            }
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime  = $finfo->file($file['tmp_name']);
            if (!in_array($mime, ['image/jpeg','image/png'], true)) {
                $errors[] = 'Format d’image invalide (jpeg ou png).';
            }
            if (empty($errors)) {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $dir = __DIR__ . '/../../public/assets/products/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $filename = 'prod_' . time() . '_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $dir . $filename)) {
                    $imgPath = 'assets/products/' . $filename;
                } else {
                    $errors[] = 'Impossible de sauvegarder l’image.';
                }
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: /sell');
            exit;
        }

        // Insertion du produit
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'INSERT INTO products (name, description, price, image, author_id)
             VALUES (:n, :d, :p, :i, :u)'
        );
        $stmt->execute([
            'n' => $name,
            'd' => $description,
            'p' => $price,
            'i' => $imgPath,
            'u' => $_SESSION['user_id'],
        ]);
        $prodId = (int)$db->lastInsertId();

        // Insertion du stock
        $stmt2 = $db->prepare('INSERT INTO stock (article_id, quantity) VALUES (:a, :q)');
        $stmt2->execute([
            'a' => $prodId,
            'q' => $qty,
        ]);

        $_SESSION['success'] = 'Produit publié avec succès.';
        header('Location: /compte');
        exit;
    }

    public function editForm(int $id): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        $prod = Product::find($id);
        if (!$prod || $prod['author_id'] !== $_SESSION['user_id']) {
            header('Location: /');
            exit;
        }
        $stock = Stock::findByArticle($id)['quantity'] ?? 0;
        $edit  = true;
        require __DIR__ . '/../Views/layout.php';
    }

    public function editSubmit(int $id): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        $prod = Product::find($id);
        if (!$prod || $prod['author_id'] !== $_SESSION['user_id']) {
            header('Location: /');
            exit;
        }

        // Récupération et validation
        $name        = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price       = (float)($_POST['price'] ?? 0);
        $qty         = (int)($_POST['stock'] ?? 0);
        $errors      = [];
        if ($name === '') {
            $errors[] = 'Titre requis.';
        }
        if ($price <= 0) {
            $errors[] = 'Prix invalide.';
        }
        if ($qty < 0) {
            $errors[] = 'Stock invalide.';
        }

        // Gestion de la nouvelle image
        $imgPath = null;
        if (!empty($_FILES['image']['tmp_name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            if ($file['size'] > 2_000_000) {
                $errors[] = 'L’image ne doit pas dépasser 2 Mo.';
            }
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime  = $finfo->file($file['tmp_name']);
            if (!in_array($mime, ['image/jpeg','image/png'], true)) {
                $errors[] = 'Format d’image invalide (jpeg ou png).';
            }
            if (empty($errors)) {
                if (!empty($prod['image']) && file_exists(__DIR__ . '/../../public/' . $prod['image'])) {
                    @unlink(__DIR__ . '/../../public/' . $prod['image']);
                }
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $dir = __DIR__ . '/../../public/assets/products/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $filename = 'prod_' . time() . '_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $dir . $filename)) {
                    $imgPath = 'assets/products/' . $filename;
                } else {
                    $errors[] = 'Impossible de sauvegarder l’image.';
                }
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: /edit/' . $id);
            exit;
        }

        // Construction de la requête de mise à jour
        $fields = ['name = :n', 'description = :d', 'price = :p'];
        $params = [
            'n'  => $name,
            'd'  => $description,
            'p'  => $price,
            'id' => $id,
        ];
        if ($imgPath !== null) {
            $fields[] = 'image = :i';
            $params['i'] = $imgPath;
        }
        $sql = 'UPDATE products SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $db = Database::getInstance();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        // Mise à jour du stock
        $stmt2 = $db->prepare('UPDATE stock SET quantity = :q WHERE article_id = :a');
        $stmt2->execute([
            'q' => $qty,
            'a' => $id,
        ]);

        $_SESSION['success'] = 'Produit mis à jour avec succès.';
        header('Location: /compte');
        exit;
    }

    public function delete(int $id): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        $prod = Product::find($id);
        if (!$prod || $prod['author_id'] !== $_SESSION['user_id']) {
            header('Location: /');
            exit;
        }
        $db = Database::getInstance();
        $db->prepare('DELETE FROM stock WHERE article_id = ?')->execute([$id]);
        $db->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);

        $_SESSION['success'] = 'Produit supprimé.';
        header('Location: /compte');
        exit;
    }
}
