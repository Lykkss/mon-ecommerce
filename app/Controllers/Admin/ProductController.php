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
        $products = $db->query(
            'SELECT p.id, p.name, p.description, p.price, p.image, COALESCE(s.quantity,0) AS stock
             FROM products p
             LEFT JOIN stock s ON s.article_id = p.id
             ORDER BY p.created_at DESC'
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
    
        // 1) Validation des champs textes / numériques
        $name  = trim($_POST['name'] ?? '');
        $desc  = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $stock = (int)($_POST['stock'] ?? 0);
    
        $errors = [];
        if ($name === '')   $errors[] = 'Le nom est requis.';
        if ($price <= 0)    $errors[] = 'Le prix doit être positif.';
        if ($stock < 0)     $errors[] = 'Le stock doit être ≥ 0.';
    
        // 2) Upload image
        // → on initialise toujours la variable comme chaîne (pas NULL)
        $imagePath = '';
    
        // Vérifier qu’un fichier a été soumis
        if (isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
            // Erreur PHP ? (taille, etc.)
            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $errors[] = 'Erreur upload n°'.$_FILES['image']['error'];
            } else {
                // Extension autorisée ?
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg','jpeg','png'], true)) {
                    $errors[] = 'Format image invalide (jpg/jpeg/png).';
                } else {
                    // Création du dossier s’il n’existe pas
                    $dir = __DIR__ . '/../../public/assets/products/';
                    if (!is_dir($dir)) {
                        if (!mkdir($dir, 0755, true)) {
                            $errors[] = 'Impossible de créer le dossier de destination.';
                        }
                    }
    
                    // Déplacement du fichier
                    $filename = 'prod_' . time() . '.' . $ext;
                    $dest     = $dir . $filename;
                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                        $errors[] = "Échec de l'upload de l'image vers $dest.";
                        error_log("move_uploaded_file failed: $dest");
                    } else {
                        // chemin relatif à stocker en base
                        $imagePath = 'assets/products/' . $filename;
                    }
                }
            }
        } else {
            // Si vous souhaitez rendre l’image obligatoire, décommentez :
            // $errors[] = 'L’image est requise.';
        }
    
        // 3) Si erreurs, on stocke et on redirige
        if ($errors) {
            $_SESSION['errors'] = $errors;
            header('Location: /admin/products/create');
            exit;
        }
    
        // 4) Création en base
        $userId = $_SESSION['user_id'] ?? 1;
        $productId = Product::create([
            'name'        => $name,
            'description' => $desc,
            'price'       => $price,
            'image'       => $imagePath,
            'author_id'   => $userId,
        ]);
    
        // 5) Stock initial
        Database::getInstance()
            ->prepare('INSERT INTO stock (article_id, quantity) VALUES (?, ?)')
            ->execute([$productId, $stock]);
    
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
        $stock     = $stockData['quantity'] ?? 0;

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

        // 1) Validation
        $name  = trim($_POST['name'] ?? '');
        $desc  = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $stock = (int)($_POST['stock'] ?? 0);

        $errors = [];
        if ($name === '')   $errors[] = 'Le nom est requis.';
        if ($price <= 0)    $errors[] = 'Le prix doit être positif.';
        if ($stock < 0)     $errors[] = 'Le stock doit être ≥ 0.';

        // 2) Upload éventuel
        $imagePath = $product['image'];
        if (!empty($_FILES['image']['tmp_name'])) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png'], true)) {
                $errors[] = 'Format image invalide (jpg/jpeg/png).';
            } else {
                $dir      = __DIR__ . '/../../public/assets/products/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $filename = 'prod_' . $id . '_' . time() . '.' . $ext;
                $dest     = $dir . $filename;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                    $errors[] = "Échec de l'upload de l'image.";
                } else {
                    $imagePath = 'assets/products/' . $filename;
                }
            }
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            header("Location: /admin/products/edit/{$id}");
            exit;
        }

        // 3) Update produit
        Product::update($id, [
            'name'        => $name,
            'description' => $desc,
            'price'       => $price,
            'image'       => $imagePath,
        ]);

        // 4) Update stock
        Stock::updateQuantity($id, $stock);

        $_SESSION['success'] = 'Produit mis à jour.';
        header('Location: /admin/products');
        exit;
    }

    public function delete(int $id): void
    {
        Database::getInstance()
            ->prepare('DELETE FROM stock WHERE article_id = ?')
            ->execute([$id]);
        Product::delete($id);

        $_SESSION['success'] = 'Produit supprimé.';
        header('Location: /admin/products');
        exit;
    }
}
