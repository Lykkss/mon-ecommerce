<?php
namespace App\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Dompdf\Dompdf;

class InvoiceController
{
    // Liste toutes les factures de l’utilisateur
    public function index(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location:/login'); exit;
        }
        $invoices = Invoice::findByUser((int)$_SESSION['user_id']);
        require __DIR__.'/../Views/layout.php';
    }

    // Affiche le détail d’une facture
    public function show(int $id): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location:/login'); exit;
        }
        $invoice = Invoice::findById($id);
        if (!$invoice || $invoice['user_id'] != $_SESSION['user_id']) {
            header('Location:/compte'); exit;
        }
        $items   = Invoice::findItems($id);
        require __DIR__.'/../Views/layout.php';
    }

    // Génère et renvoie le PDF de la facture
    public function pdf(int $id): void
    {
        // Même contrôle d’accès que show()
        if (empty($_SESSION['user_id'])) {
            header('Location:/login'); exit;
        }
        $invoice = Invoice::findById($id);
        if (!$invoice || $invoice['user_id'] != $_SESSION['user_id']) {
            header('Location:/compte'); exit;
        }
        $items = Invoice::findItems($id);

        // Prépare le HTML (vue dédiée)
        ob_start();
        include __DIR__.'/../Views/invoice_pdf.php';
        $html = ob_get_clean();

        // Dompdf
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4','portrait');
        $dompdf->render();
        $dompdf->stream("facture_{$id}.pdf", ["Attachment" => true]);
    }
}
