<?php
namespace App\Controllers;
use App\Models\Product;

class HomeController
{
    public function index()
    {
        $products = Product::all();
        require __DIR__ . '/../Views/layout.php';
    }

    public function show(int $id)
    {
        $product = Product::find($id);
        if (!$product) {
            header("HTTP/1.0 404 Not Found");
            exit("Produit introuvable");
        }
        require __DIR__ . '/../Views/layout.php';
    }
}
