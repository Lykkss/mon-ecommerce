<?php
namespace App\Controllers;

class CartController
{
    public function add()
    {
        session_start();
        $id    = $_POST['product_id'] ?? null;
        $qty   = max(1, (int)($_POST['quantity'] ?? 1));
        if ($id) {
            if (!isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id] = 0;
            }
            $_SESSION['cart'][$id] += $qty;
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
}
