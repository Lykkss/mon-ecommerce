<?php
namespace App\Controllers;

use App\Core\Database;
use App\Models\Product;
use App\Models\Stock;

class SellController
{
    /**
     * GET /sell
     * Affiche le formulaire de publication d’un nouveau produit.
     */
    public function createForm(): void
    {
        // 1) Protection
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // 2) Mode création
        $sell    = true;  // flag pour la vue
        $edit    = false; // pas en édition
        $product = [
            'id'          => 0,
            'title'       => '',
            'description' => '',
            'price'       => '',
            'image'       => null,
        ];
        $stock   = 0;

        // 3) Affichage
        require __DIR__ . '/../Views/layout.php';
    }

    /**
     * POST /sell
     * Traite le formulaire de création.
     */
    public function createSubmit(): void
    {
        // 1) Protection
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // 2) Récupération + validation
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

        // 3) Traitement de l’image (optionnel)
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
                $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $dir      = __DIR__ . '/../../public/assets/products/';
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                $filename = 'prod_' . time() . '_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $dir . $filename)) {
                    $imgPath = 'assets/products/' . $filename;
                } else {
                    $errors[] = 'Impossible de sauvegarder l’image.';
                }
            }
        }

        // 4) Erreurs → redirection
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: /sell');
            exit;
        }

        // 5) Insertion en base
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

        // 6) Stock initial
        $stmt2 = $db->prepare('INSERT INTO stock (article_id, quantity) VALUES (:a, :q)');
        $stmt2->execute([
            'a' => $prodId,
            'q' => $qty,
        ]);

        $_SESSION['success'] = 'Produit publié avec succès.';
        header('Location: /compte');
        exit;
    }

    /**
     * GET /edit/{id}
     * Affiche le formulaire d’édition pour un produit existant.
     */
    public function editForm(int $id): void
    {
        // 1) Protection
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // 2) Chargement du produit
        $product = Product::find($id);
        if (!$product || $product['author_id'] !== $_SESSION['user_id']) {
            header('Location: /');
            exit;
        }

        // 3) Mode édition
        $edit  = true;
        $sell  = false;
        $stock = Stock::findByArticle($id)['quantity'] ?? 0;

        // 4) Affichage
        require __DIR__ . '/../Views/layout.php';
    }

    /**
     * POST /edit/{id}
     * Traite la soumission du formulaire d’édition.
     */
    public function editSubmit(int $id): void
    {
        // 1) Protection
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        $prod = Product::find($id);
        if (!$prod || $prod['author_id'] !== $_SESSION['user_id']) {
            header('Location: /');
            exit;
        }

        // 2) Récupération + validation
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

        // 3) Gestion d’une nouvelle image
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
                // Supprime l’ancienne si existante
                if (!empty($prod['image']) && file_exists(__DIR__ . '/../../public/' . $prod['image'])) {
                    @unlink(__DIR__ . '/../../public/' . $prod['image']);
                }
                $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $dir      = __DIR__ . '/../../public/assets/products/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $filename = 'prod_' . time() . '_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $dir . $filename)) {
                    $imgPath = 'assets/products/' . $filename;
                } else {
                    $errors[] = 'Impossible de sauvegarder l’image.';
                }
            }
        }

        // 4) Erreurs → redirection
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: /edit/' . $id);
            exit;
        }

        // 5) Mise à jour du produit
        $fields = ['name = :n', 'description = :d', 'price = :p'];
        $params = ['n' => $name, 'd' => $description, 'p' => $price, 'id' => $id];
        if ($imgPath !== null) {
            $fields[]    = 'image = :i';
            $params['i'] = $imgPath;
        }
        $sql = 'UPDATE products SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $db  = Database::getInstance();
        $db->prepare($sql)->execute($params);

        // 6) Mise à jour du stock
        $db->prepare('UPDATE stock SET quantity = :q WHERE article_id = :a')
           ->execute(['q' => $qty, 'a' => $id]);

        $_SESSION['success'] = 'Produit mis à jour avec succès.';
        header('Location: /compte');
        exit;
    }

    /**
     * POST /delete/{id}
     * Supprime un produit publié par l’utilisateur.
     */
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
