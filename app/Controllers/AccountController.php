<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Invoice;
use App\Core\Database;

class AccountController
{
    /**
     * Affiche l’espace “Mon compte”
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

        // Flag pour layout.php
        $account = true;

        require __DIR__ . '/../Views/layout.php';
    }

    /**
     * Traite la mise à jour du profil (fullname + avatar)
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

        if ($fullname === '') {
            $errors[] = "Le nom complet ne peut pas être vide.";
        }

        // On conserve l’ancien avatar si pas de nouvel upload
        $avatarPath = $user['avatar'] ?? null; // ex: "assets/avatars/avatar_1.png"

        // Si l’utilisateur upload un nouveau fichier validé
        if ($avatar && $avatar['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($avatar['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
                $errors[] = "Avatar : format invalide (jpg, jpeg ou png seulement).";
            } else {
                $targetDir = __DIR__ . '/../../public/assets/avatars/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                $filename = 'avatar_' . $userId . '.' . $ext;
                move_uploaded_file($avatar['tmp_name'], $targetDir . $filename);
                // On stocke **sans** slash initial
                $avatarPath = 'assets/avatars/' . $filename;
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
            WHERE id = :id
        ");
        $stmt->execute([
            'f'   => $fullname,
            'a'   => $avatarPath,
            'id'  => $userId,
        ]);

        header('Location: /compte');
        exit;
    }
}
