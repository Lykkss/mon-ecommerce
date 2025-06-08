<?php
// app/Controllers/Admin/ProductController.php

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Models\Product;
use App\Models\Stock;

class ProductController
{
    /**
     * GET /admin/products
     * Affiche la liste des produits avec stock, nb commentaires et nb favoris
     */
    public function index(): void
    {
        $db = Database::getInstance();
        $products = $db->query(
            'SELECT 
                p.id,
                p.name,
                p.description,
                p.price,
                p.image,
                COALESCE(s.quantity,0) AS stock,
                COALESCE(c.comments_count,0) AS comments_count,
                COALESCE(f.favorites_count,0) AS favorites_count
             FROM products p
             LEFT JOIN stock s ON s.article_id = p.id
             LEFT JOIN (
                 SELECT product_id, COUNT(*) AS comments_count
                 FROM comments GROUP BY product_id
             ) c ON c.product_id = p.id
             LEFT JOIN (
                 SELECT product_id, COUNT(*) AS favorites_count
                 FROM favorites GROUP BY product_id
             ) f ON f.product_id = p.id
             ORDER BY p.created_at DESC'
        )->fetchAll(\PDO::FETCH_ASSOC);

        $adminProducts = $products;
        require __DIR__ . '/../../Views/layout.php';
    }

    /**
     * GET /admin/products/create
     */
    public function createForm(): void
    {
        $adminProductsCreate = true;
        $product = ['name' => '', 'description' => '', 'price' => '', 'image' => '', 'stock' => 0];
        require __DIR__ . '/../../Views/layout.php';
    }

    /**
     * POST /admin/products/create
     */
    public function createSubmit(): void
    {
        // Validation
        $name  = trim($_POST['name'] ?? '');
        $desc  = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $stock = (int)($_POST['stock'] ?? 0);

        $errors = [];
        if ($name === '')   $errors[] = 'Le nom est requis.';
        if ($price <= 0)    $errors[] = 'Le prix doit être positif.';
        if ($stock < 0)     $errors[] = 'Le stock doit être ≥ 0.';

        // Upload image
        $imagePath = '';
        if (!empty($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $errors[] = 'Erreur lors de l’upload de l’image.';
            } else {
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg','jpeg','png'], true)) {
                    $errors[] = 'Format image invalide (jpg/jpeg/png).';
                } else {
                    $dir = __DIR__ . '/../../../public/assets/products/';
                    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
                        $errors[] = 'Impossible de créer le dossier products.';
                    }
                    $filename = 'prod_' . time() . '.' . $ext;
                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $dir . $filename)) {
                        $errors[] = "Échec de l'upload de l'image.";
                    } else {
                        $imagePath = 'assets/products/' . $filename;
                    }
                }
            }
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            header('Location: /admin/products/create');
            exit;
        }

        // Création produit
        $productId = Product::create([
            'name'        => $name,
            'description' => $desc,
            'price'       => $price,
            'image'       => $imagePath,
            // author_id géré par le modèle ou ajouté ici si besoin
        ]);

        // Stock initial
        Database::getInstance()
            ->prepare('INSERT INTO stock (article_id, quantity) VALUES (?, ?)')
            ->execute([$productId, $stock]);

        $_SESSION['success'] = 'Produit créé avec succès.';
        header('Location: /admin/products');
        exit;
    }

    /**
     * GET /admin/products/edit/{id}
     */
    public function editForm(int $id): void
    {
        $product = Product::find($id);
        if (!$product) {
            $_SESSION['errors'] = ["Produit #{$id} introuvable."];
            header('Location: /admin/products');
            exit;
        }
        $stockData = Stock::findByArticle($id);
        $product['stock'] = $stockData['quantity'] ?? 0;

        $adminProductsEdit = true;
        require __DIR__ . '/../../Views/layout.php';
    }

    /**
     * POST /admin/products/edit/{id}
     */
    public function editSubmit(int $id): void
    {
        $product = Product::find($id);
        if (!$product) {
            $_SESSION['errors'] = ["Produit #{$id} introuvable."];
            header('Location: /admin/products');
            exit;
        }

        $name  = trim($_POST['name'] ?? '');
        $desc  = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $stock = (int)($_POST['stock'] ?? 0);

        $errors = [];
        if ($name === '')   $errors[] = 'Le nom est requis.';
        if ($price <= 0)    $errors[] = 'Le prix doit être positif.';
        if ($stock < 0)     $errors[] = 'Le stock doit être ≥ 0.';

        // Gestion de l'image
        $imagePath = $product['image'] ?? '';
        if (!empty($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $errors[] = 'Erreur lors de l’upload de l’image.';
            } else {
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg','jpeg','png'], true)) {
                    $errors[] = 'Format image invalide (jpg/jpeg/png).';
                } else {
                    $dir = __DIR__ . '/../../../public/assets/products/';
                    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
                        $errors[] = 'Impossible de créer le dossier products.';
                    }
                    $filename = "prod_{$id}_" . time() . ".{$ext}";
                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $dir . $filename)) {
                        $errors[] = "Échec de l'upload de l'image.";
                    } else {
                        $imagePath = 'assets/products/' . $filename;
                    }
                }
            }
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            header("Location: /admin/products/edit/{$id}");
            exit;
        }

        // Mise à jour produit
        Product::update($id, [
            'name'        => $name,
            'description' => $desc,
            'price'       => $price,
            'image'       => $imagePath,
        ]);

        // Mise à jour du stock
        Stock::updateQuantity($id, $stock);

        $_SESSION['success'] = 'Produit mis à jour.';
        header('Location: /admin/products');
        exit;
    }

    /**
     * POST /admin/products/delete/{id}
     */
    public function delete(int $id): void
    {
        // Suppression du stock en amont
        Database::getInstance()
            ->prepare('DELETE FROM stock WHERE article_id = ?')
            ->execute([$id]);

        // Suppression du produit
        Product::delete($id);

        $_SESSION['success'] = 'Produit supprimé.';
        header('Location: /admin/products');
        exit;
    }
}
