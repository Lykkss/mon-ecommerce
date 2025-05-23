<?php
namespace App\Controllers;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Controllers\MailController;

class CheckoutController
{
    public function form(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $items = [];
        $total = 0.0;
        foreach ($_SESSION['cart'] ?? [] as $id => $qty) {
            if ($p = Product::find((int)$id)) {
                $sub = $qty * (float)$p['price'];
                $items[] = [
                    'product'  => $p,
                    'quantity' => $qty,
                    'subtotal' => $sub,
                ];
                $total += $sub;
            }
        }

        $checkout = true;
        require __DIR__ . '/../Views/layout.php';
    }

    public function submit(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Validation
        $email   = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $address = trim($_POST['address'] ?? '');
        $city    = trim($_POST['city']    ?? '');
        $zip     = trim($_POST['zip']     ?? '');
        if (!$email || !$address || !$city || !$zip) {
            $_SESSION['errors'] = ['Tous les champs de facturation sont requis.'];
            header('Location: /commande');
            exit;
        }

        // Reconstitu­tion du panier
        $items = [];
        $total = 0.0;
        foreach ($_SESSION['cart'] ?? [] as $id => $qty) {
            if ($p = Product::find((int)$id)) {
                $sub = $qty * (float)$p['price'];
                $items[] = ['product'=>$p, 'quantity'=>$qty, 'subtotal'=>$sub];
                $total += $sub;
            }
        }

        // 1) Création de la facture
        $invoiceId = Invoice::create(
            (int)$_SESSION['user_id'],
            $total,
            ['address'=>$address, 'city'=>$city, 'zip'=>$zip]
        );

        // 2) Lignes + stock
        foreach ($items as $it) {
            InvoiceItem::create(
                $invoiceId,
                (int)$it['product']['id'],
                $it['quantity'],
                (float)$it['product']['price']
            );
            Stock::decrement($it['product']['id'], $it['quantity']);
        }

        // 3) Mail + vidage du panier
        (new MailController())->sendOrderConfirmation($email, [
            'items' => array_map(fn($i)=>[
                'name'     => $i['product']['title'],
                'quantity' => $i['quantity'],
                'price'    => $i['product']['price']
            ], $items),
            'total' => $total,
            'ref'   => 'INV_'.$invoiceId
        ]);
        unset($_SESSION['cart']);

        $orderSuccess = true;
        require __DIR__ . '/../Views/layout.php';
    }
}
