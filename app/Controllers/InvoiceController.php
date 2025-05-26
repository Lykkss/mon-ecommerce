<?php
namespace App\Controllers;

use App\Models\Invoice;
use Dompdf\Dompdf;

class InvoiceController
{
    // GET /compte/factures
    public function index(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        // Récupère toutes les factures de l'utilisateur
        $invoices = Invoice::findByUser((int) $_SESSION['user_id']);

        // expose $invoices à la vue
        extract(compact('invoices'));

        // on inclut le layout — le layout détectera la présence de $invoices
        require __DIR__ . '/../Views/layout.php';
    }

    // GET /compte/facture/{id}
    public function show(int $id): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        $invoice = Invoice::findById($id);
        if (!$invoice || $invoice['user_id'] != $_SESSION['user_id']) {
            header('Location: /compte');
            exit;
        }
        // récupère les lignes de la facture
        $items = Invoice::findItems($id);

        // expose $invoice et $items à la vue
        extract(compact('invoice','items'));

        // le layout détectera la présence de $invoice + $items
        require __DIR__ . '/../Views/layout.php';
    }

    // GET /compte/facture/{id}/pdf
    public function pdf(int $id): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        $invoice = Invoice::findById($id);
        if (!$invoice || $invoice['user_id'] != $_SESSION['user_id']) {
            header('Location: /compte');
            exit;
        }
        $items = Invoice::findItems($id);

        // Génère le HTML du PDF
        ob_start();
        include __DIR__ . '/../Views/invoice_pdf.php';
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        // Attachment false pour affichage inline
        $dompdf->stream("facture_{$id}.pdf", ['Attachment' => false]);
    }
}
