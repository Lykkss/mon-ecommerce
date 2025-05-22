<?php
namespace App\Controllers;

use App\Models\Product;
use App\Controllers\MailController;

class CheckoutController
{
    public function form(): void
    {
        // Protection : redirige vers login si non connecté
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Récupère le contenu du panier
        $items = [];
        foreach ($_SESSION['cart'] ?? [] as $id => $qty) {
            if ($p = Product::find((int) $id)) {
                $p['quantity'] = $qty;
                $p['subtotal'] = $qty * (float) $p['price'];
                $items[] = $p;
            }
        }

        // Flag pour savoir qu’on est en mode checkout
        $checkout = true;
        require __DIR__ . '/../Views/layout.php';
    }

    public function submit(): void
    {
        // Protection : redirige vers login si non connecté
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Valide l'email
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        if (!$email) {
            header('Location: /commande');
            exit;
        }

        // Reconstruit le panier
        $items = [];
        $total = 0.0;
        foreach ($_SESSION['cart'] ?? [] as $id => $qty) {
            if ($p = Product::find((int) $id)) {
                $p['quantity'] = $qty;
                $p['subtotal'] = $qty * (float) $p['price'];
                $total += $p['subtotal'];
                $items[] = $p;
            }
        }

        // Génère une référence unique
        $ref = uniqid('CMD_');

        // Envoie le mail
        (new MailController())->sendOrderConfirmation($email, [
            'items' => $items,
            'total' => $total,
            'ref'   => $ref,
        ]);

        // Vide le panier
        unset($_SESSION['cart']);

        // Flag pour afficher la page de succès
        $orderSuccess = true;
        require __DIR__ . '/../Views/layout.php';
    }
}
