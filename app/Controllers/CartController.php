<?php
namespace App\Controllers;

use App\Models\Product;

class CartController
{
    // Ajoute un item au panier (session)
    public function add(): void
    {
        // Protection : redirige vers login si non connecté
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $id  = (int) ($_POST['product_id'] ?? 0);
        $qty = max(1, (int) ($_POST['quantity'] ?? 1));

        if ($id > 0) {
            if (!isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id] = 0;
            }
            $_SESSION['cart'][$id] += $qty;
        }

        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
        exit;
    }

    // Affiche le panier
    public function index(): void
    {
        // Protection : redirige vers login si non connecté
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $items = [];
        $total = 0.0;

        foreach ($_SESSION['cart'] ?? [] as $id => $qty) {
            if ($prod = Product::find((int) $id)) {
                $prod['quantity'] = $qty;
                $prod['subtotal'] = $qty * (float) $prod['price'];
                $total += $prod['subtotal'];
                $items[] = $prod;
            }
        }

        require __DIR__ . '/../Views/layout.php';
    }

    // Supprime un item du panier
    public function remove(): void
    {
        // Protection : redirige vers login si non connecté
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $id = (int) ($_POST['product_id'] ?? 0);
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
        header('Location: /panier');
        exit;
    }
}
