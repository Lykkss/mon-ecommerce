<?php
// app/Controllers/CartController.php
namespace App\Controllers;

use App\Models\Product;

class CartController
{
    public function index(): void
    {
        $items = [];
        $total = 0.0;

        // Parcours du panier en session
        foreach ($_SESSION['cart'] ?? [] as $id => $qty) {
            if ($p = Product::find((int)$id)) {
                // Si $p est un objet, adaptez en ->price, ->name, ...
                $price    = is_array($p) ? (float)$p['price'] : (float)$p->price;
                $name     = is_array($p) ? $p['name']      : $p->name;
                $productId= is_array($p) ? $p['id']        : $p->id;

                $subtotal = $qty * $price;
                $items[] = [
                    'id'       => $productId,
                    'name'     => $name,
                    'price'    => $price,
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                ];
                $total += $subtotal;
            }
        }

        // Expose $items et $total à la vue
        extract(compact('items', 'total'));

        require __DIR__ . '/../Views/layout.php';
    }

    public function add(): void
    {
        $id  = (int) ($_POST['product_id'] ?? 0);
        $qty = max(1, (int) ($_POST['quantity'] ?? 1));
        $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + $qty;
        header('Location: /panier');
        exit;
    }

    public function remove(): void
    {
        $id = (int) ($_POST['product_id'] ?? 0);
        unset($_SESSION['cart'][$id]);
        header('Location: /panier');
        exit;
    }
}
