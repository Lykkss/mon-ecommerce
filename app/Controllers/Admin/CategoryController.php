<?php
// app/Controllers/Admin/CategoryController.php
namespace App\Controllers\Admin;

use App\Core\Database;
use App\Models\Category;

class CategoryController
{
    public function index(): void
    {
        // Protection admin
        if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('HTTP/1.0 403 Forbidden'); exit;
        }

        $categories = Category::all();
        $adminCategories = true;
        require __DIR__ . '/../../Views/layout.php';
    }

    public function create(): void
    {
        if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('HTTP/1.0 403 Forbidden'); exit;
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            $_SESSION['errors'] = ['Le nom de la catégorie ne peut pas être vide.'];
        } else {
            Category::create($name);
            $_SESSION['success'] = "Catégorie « {$name} » créée.";
        }
        header('Location: /admin/categories'); exit;
    }

    public function delete(): void
    {
        if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('HTTP/1.0 403 Forbidden'); exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            Category::delete($id);
            $_SESSION['success'] = "Catégorie supprimée.";
        }
        header('Location: /admin/categories'); exit;
    }
}
