<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Invoice;
use App\Core\Database;

class AccountController
{
    /**
     * GET /compte
     */
    public function index(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId   = (int) $_SESSION['user_id'];
        $user     = User::findById($userId);
        $invoices = Invoice::findByUser($userId);

        // Produits à vendre par cet utilisateur
        $db   = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT p.id,
                    p.name  AS title,
                    p.price,
                    p.image,
                    COALESCE(s.quantity, 0) AS stock
             FROM products p
             LEFT JOIN stock s ON s.article_id = p.id
             WHERE p.author_id = :uid
             ORDER BY p.created_at DESC"
        );
        $stmt->execute(['uid' => $userId]);
        $myProducts = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Solde
        $balance = $user['balance'] ?? 0.0;

        // Si on vient de changer avatar/fullname
        if (!empty($_SESSION['user_avatar'])) {
            $user['avatar']   = $_SESSION['user_avatar'];
        }
        if (!empty($_SESSION['user_fullname'])) {
            $user['fullname'] = $_SESSION['user_fullname'];
        }

        // Cache-buster avatar
        $ts = $_SESSION['avatar_ts'] ?? null;

        // Flag pour la vue
        $account = true;
        require __DIR__ . '/../Views/layout.php';
    }

    /**
     * POST /compte/mettre-a-jour
     * Mise à jour du profil (fullname + avatar)
     */
    public function update(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = (int) $_SESSION['user_id'];
        $user   = User::findById($userId);

        $fullname = trim($_POST['fullname'] ?? '');
        $avatar   = $_FILES['avatar'] ?? null;
        $errors   = [];

        // Validation du nom
        if ($fullname === '') {
            $errors[] = "Le nom complet ne peut pas être vide.";
        } elseif (mb_strlen($fullname) > 100) {
            $errors[] = "Le nom complet ne peut pas dépasser 100 caractères.";
        }

        // Chemin actuel de l’avatar
        $avatarPath = $user['avatar'] ?? null;

        // Traitement de l’upload
        if ($avatar && $avatar['error'] === UPLOAD_ERR_OK) {
            if ($avatar['size'] > 2_000_000) {
                $errors[] = "L’avatar ne doit pas dépasser 2 Mo.";
            }
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime  = $finfo->file($avatar['tmp_name']);
            if (!in_array($mime, ['image/jpeg','image/png'], true)) {
                $errors[] = "Format invalide : jpeg ou png attendu.";
            }

            if (!$errors) {
                $dir = __DIR__ . '/../../public/assets/avatars/';
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                // Supprime l’ancien
                if (!empty($user['avatar']) && file_exists(__DIR__ . '/../../public/' . $user['avatar'])) {
                    @unlink(__DIR__ . '/../../public/' . $user['avatar']);
                }
                // Nouveau nom + déplacement
                $ext      = strtolower(pathinfo($avatar['name'], PATHINFO_EXTENSION));
                $uniq     = uniqid("avatar_{$userId}_", true);
                $filename = "{$uniq}.{$ext}";
                $dest     = $dir . $filename;
                if (!move_uploaded_file($avatar['tmp_name'], $dest)) {
                    $errors[] = "Impossible de sauvegarder l’avatar.";
                } else {
                    $avatarPath             = 'assets/avatars/' . $filename;
                    $_SESSION['user_avatar'] = $avatarPath;
                    $_SESSION['avatar_ts']    = time();
                }
            }
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            header('Location: /compte');
            exit;
        }

        // Mise à jour en base
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            UPDATE users
               SET fullname = :f,
                   avatar   = :a
             WHERE id       = :id
        ");
        $stmt->execute([
            'f'  => $fullname,
            'a'  => $avatarPath,
            'id' => $userId,
        ]);

        $_SESSION['success']       = "Profil mis à jour avec succès.";
        $_SESSION['user_fullname'] = $fullname;

        header('Location: /compte');
        exit;
    }

    /**
     * POST /compte/deposer
     * Créditer le solde utilisateur
     */
    public function depositSubmit(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $amount = (float)($_POST['amount'] ?? 0);
        if ($amount <= 0) {
            $_SESSION['errors'] = ['Montant invalide.'];
            header('Location: /compte');
            exit;
        }

        $db   = Database::getInstance();
        $stmt = $db->prepare("
            UPDATE users
               SET balance = balance + :amt
             WHERE id      = :uid
        ");
        $stmt->execute([
            'amt' => $amount,
            'uid' => $_SESSION['user_id'],
        ]);

        $_SESSION['success'] = "Votre solde a été crédité de " 
            . number_format($amount, 2, ',', ' ') . " €.";

        header('Location: /compte');
        exit;
    }

    /**
     * POST /compte/payer
     * Débiter le solde pour payer une facture
     */
    public function payInvoice(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $invoiceId = (int)($_POST['invoice_id'] ?? 0);
        // Récupération de la facture
        $invoice = Invoice::findById($invoiceId);
        if (!$invoice || $invoice['user_id'] !== $_SESSION['user_id']) {
            $_SESSION['errors'] = ['Facture introuvable.'];
            header('Location: /compte');
            exit;
        }
        if (!empty($invoice['paid_at'])) {
            $_SESSION['errors'] = ['Cette facture est déjà payée.'];
            header('Location: /compte');
            exit;
        }

        $amount = $invoice['total_amount'];
        $user   = User::findById($_SESSION['user_id']);
        if ($user['balance'] < $amount) {
            $_SESSION['errors'] = ['Solde insuffisant.'];
            header('Location: /compte');
            exit;
        }

        // Transaction : débit et marquage payé
        $db = Database::getInstance();
        $db->beginTransaction();
        $db->prepare("
            UPDATE users
               SET balance = balance - :amt
             WHERE id      = :uid
        ")->execute([
            'amt' => $amount,
            'uid' => $_SESSION['user_id'],
        ]);
        $db->prepare("
            UPDATE invoices
               SET paid_at = NOW()
             WHERE id = :inv
        ")->execute(['inv' => $invoiceId]);
        $db->commit();

        $_SESSION['success'] = "Facture #{$invoiceId} payée (−" 
            . number_format($amount, 2, ',', ' ') . " €).";

        header('Location: /compte');
        exit;
    }
}
