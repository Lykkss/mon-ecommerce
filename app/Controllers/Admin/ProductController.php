<?php
namespace App\Controllers\Admin;

use App\Core\Database;
use App\Models\Product;
use App\Models\Stock;

class ProductController
{
    public function index(): void
    {
        $db = Database::getInstance();
        // Récupère produits + stock
        $products = $db->query(
            'SELECT p.*, COALESCE(s.quantity,0) AS stock
             FROM products p
             LEFT JOIN stock s ON s.article_id = p.id'
        )->fetchAll(\PDO::FETCH_ASSOC);

        $adminProducts = $products;
        require __DIR__ . '/../../Views/layout.php';
    }

    public function createForm(): void
    {
        $adminProductsCreate = true;
        require __DIR__ . '/../../Views/layout.php';
    }

    public function createSubmit(): void
    {
        // Validation minimale
        $name  = trim($_POST['name'] ?? '');
        $desc  = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $stock = (int)($_POST['stock'] ?? 0);

        $errors = [];
        if ($name === '')   $errors[] = 'Le nom est requis.';
        if ($price <= 0)    $errors[] = 'Le prix doit être positif.';
        if ($stock < 0)     $errors[] = 'Le stock doit être ≥ 0.';

        // Upload d’image
        $imagePath = null;
        if (!empty($_FILES['image']['tmp_name'])) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png'], true)) {
                $errors[] = 'Image invalide (jpg/png).';
            } else {
                $dir = __DIR__ . '/../../public/assets/products/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $filename = 'prod_' . time() . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], $dir . $filename);
                $imagePath = 'assets/products/' . $filename;
            }
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            header('Location: /admin/products/create');
            exit;
        }

        // Création du produit
        $productId = Product::create([
            'name'        => $name,
            'description' => $desc,
            'price'       => $price,
            'image'       => $imagePath,
        ]);

        // Initialisation du stock
        $db = Database::getInstance();
        $db->prepare(
            'INSERT INTO stock (article_id, quantity) VALUES (?, ?)' 
        )->execute([$productId, $stock]);

        $_SESSION['success'] = 'Produit créé avec succès.';
        header('Location: /admin/products');
        exit;
    }

    public function editForm(int $id): void
    {
        $product = Product::find($id);
        if (!$product) {
            $_SESSION['errors'] = ["Produit #{$id} introuvable."];
            header('Location: /admin/products');
            exit;
        }
        $stockData = Stock::findByArticle($id);
        $stock = $stockData['quantity'] ?? 0;

        $adminProductsEdit = true;
        require __DIR__ . '/../../Views/layout.php';
    }

    public function editSubmit(int $id): void
    {
        $product = Product::find($id);
        if (!$product) {
            $_SESSION['errors'] = ["Produit #{$id} introuvable."];
            header('Location: /admin/products');
            exit;
        }

        // Validation
        $name  = trim($_POST['name'] ?? '');
        $desc  = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $stock = (int)($_POST['stock'] ?? 0);

        $errors = [];
        if ($name === '')   $errors[] = 'Le nom est requis.';
        if ($price <= 0)    $errors[] = 'Le prix doit être positif.';
        if ($stock < 0)     $errors[] = 'Le stock doit être ≥ 0.';

        // Upload éventuel
        $imagePath = $product['image'];
        if (!empty($_FILES['image']['tmp_name'])) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png'], true)) {
                $errors[] = 'Image invalide (jpg/png).';
            } else {
                $dir = __DIR__ . '/../../public/assets/products/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $filename = 'prod_'.$id.'_'.time().'.'.$ext;
                move_uploaded_file($_FILES['image']['tmp_name'], $dir . $filename);
                $imagePath = 'assets/products/' . $filename;
            }
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            header("Location: /admin/products/edit/{$id}");
            exit;
        }

        // Mise à jour
        Product::update($id, [
            'name'        => $name,
            'description' => $desc,
            'price'       => $price,
            'image'       => $imagePath,
        ]);
        Stock::updateQuantity($id, $stock);

        $_SESSION['success'] = 'Produit mis à jour.';
        header('Location: /admin/products');
        exit;
    }

    public function delete(int $id): void
    {
        $db = Database::getInstance();
        $db->prepare('DELETE FROM stock WHERE article_id = ?')
           ->execute([$id]);
        Product::delete($id);

        $_SESSION['success'] = 'Produit supprimé.';
        header('Location: /admin/products');
        exit;
    }
}
