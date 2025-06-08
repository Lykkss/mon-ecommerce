<?php
namespace App\Controllers;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use App\Core\Database;
use App\Controllers\MailController;

class CheckoutController
{
    /**
     * Affiche le formulaire de validation de la commande
     * GET /commande
     */
    public function form(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Reconstruction du panier
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

        // Solde de l'utilisateur
        $user    = User::findById((int)$_SESSION['user_id']);
        $balance = $user['balance'] ?? 0.0;

        // Flag pour la vue
        $checkout = true;
        require __DIR__ . '/../Views/layout.php';
    }

    /**
     * Traite la confirmation de commande
     * POST /commande/valider
     */
    public function submit(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // 1) Validation des informations de facturation
        $email   = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $address = trim($_POST['address'] ?? '');
        $city    = trim($_POST['city']    ?? '');
        $zip     = trim($_POST['zip']     ?? '');
        if (!$email || !$address || !$city || !$zip) {
            $_SESSION['errors'] = ['Tous les champs de facturation sont requis.'];
            header('Location: /commande');
            exit;
        }

        // 2) Reconstruction du panier
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

        // 3) Vérification du stock avant création de la commande
        foreach ($items as $it) {
            $stockData = Stock::findByArticle((int)$it['product']['id']);
            if (!$stockData || $stockData['quantity'] < $it['quantity']) {
                $_SESSION['errors'] = ["Stock insuffisant pour le produit #{$it['product']['id']}."];
                header('Location: /commande');
                exit;
            }
        }

        // 4) Création de la facture
        $invoiceId = Invoice::create(
            (int)$_SESSION['user_id'],
            $total,
            ['address' => $address, 'city' => $city, 'zip' => $zip]
        );

        // 5) Lignes de facture et mise à jour du stock
        foreach ($items as $it) {
            InvoiceItem::create(
                $invoiceId,
                (int)$it['product']['id'],
                $it['quantity'],
                (float)$it['product']['price']
            );
            Stock::decrement((int)$it['product']['id'], $it['quantity']);
        }

        // 6) Paiement
        $method  = $_POST['payment_method'] ?? '';
        $db      = Database::getInstance();
        if ($method === 'balance') {
            // a) Vérifier le solde
            $user = User::findById((int)$_SESSION['user_id']);
            if ($user['balance'] < $total) {
                $_SESSION['errors'] = ['Solde insuffisant pour payer cette commande.'];
                header('Location: /commande');
                exit;
            }
            // b) Débiter le solde
            $db->prepare("
                UPDATE users
                   SET balance = balance - :amt
                 WHERE id      = :uid
            ")->execute([
                'amt' => $total,
                'uid' => $_SESSION['user_id'],
            ]);
            // c) Marquer la facture payée
            $db->prepare("
                UPDATE invoices
                   SET paid_at = NOW()
                 WHERE id = :id
            ")->execute(['id' => $invoiceId]);

        } elseif ($method === 'credit_card') {
            // TODO: intégrez votre passerelle CB ici
        } elseif ($method === 'paypal') {
            // TODO: intégrez votre passerelle PayPal ici
        } else {
            $_SESSION['errors'] = ['Méthode de paiement invalide.'];
            header('Location: /commande');
            exit;
        }

        // 7) Envoi de l’e-mail de confirmation
        (new MailController())->sendOrderConfirmation($email, [
            'items' => array_map(fn($i) => [
                'name'     => $i['product']['name'],
                'quantity' => $i['quantity'],
                'price'    => $i['product']['price'],
            ], $items),
            'total' => $total,
            'ref'   => 'INV_'.$invoiceId
        ]);

        // 8) Vidage du panier et affichage de la page de succès
        unset($_SESSION['cart']);
        $orderSuccess = true;
        require __DIR__ . '/../Views/layout.php';
    }
}
