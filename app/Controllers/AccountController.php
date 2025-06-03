<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Invoice;
use App\Core\Database;

class AccountController
{
    public function index(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId   = (int) $_SESSION['user_id'];
        $user     = User::findById($userId);
        $invoices = Invoice::findByUser($userId);

        // === NOUVEAU : on récupère aussi les produits “à vendre” de ce user ===
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT p.id,
                    p.name AS title,
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

        // Si on vient de mettre à jour l’avatar, on peut avoir mis à jour $_SESSION['user_avatar'] + 'user_fullname'
        if (!empty($_SESSION['user_avatar'])) {
            $user['avatar']   = $_SESSION['user_avatar'];
        }
        if (!empty($_SESSION['user_fullname'])) {
            $user['fullname'] = $_SESSION['user_fullname'];
        }

        // Pour forcer le bust-cache dans la vue
        $ts = $_SESSION['avatar_ts'] ?? null;

        $account = true;
        require __DIR__ . '/../Views/layout.php';
    }

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

        // 1) Validation du nom
        if ($fullname === '') {
            $errors[] = "Le nom complet ne peut pas être vide.";
        } elseif (mb_strlen($fullname) > 100) {
            $errors[] = "Le nom complet ne peut pas dépasser 100 caractères.";
        }

        // 2) Valeur actuelle de l’avatar
        $avatarPath = $user['avatar'] ?? null;

        // 3) Traitement de l’upload
        if ($avatar && $avatar['error'] === UPLOAD_ERR_OK) {
            if ($avatar['size'] > 2_000_000) {
                $errors[] = "L’avatar ne doit pas dépasser 2 Mo.";
            }
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime  = $finfo->file($avatar['tmp_name']);
            if (!in_array($mime, ['image/jpeg','image/png'], true)) {
                $errors[] = "Avatar : format invalide (jpeg ou png).";
            }

            if (empty($errors)) {
                $dir = __DIR__ . '/../../public/assets/avatars/';
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                // Supprime l’ancien avatar
                if (!empty($user['avatar']) && file_exists(__DIR__ . '/../../public/' . $user['avatar'])) {
                    @unlink(__DIR__ . '/../../public/' . $user['avatar']);
                }

                // Génère un nom de fichier unique + cache buster
                $ext      = strtolower(pathinfo($avatar['name'], PATHINFO_EXTENSION));
                $uniq     = uniqid('', true);
                $filename = "avatar_{$userId}_{$uniq}.{$ext}";
                $dest     = $dir . $filename;

                if (!move_uploaded_file($avatar['tmp_name'], $dest)) {
                    $errors[] = "Impossible de sauvegarder l’avatar.";
                } else {
                    $avatarPath             = 'assets/avatars/' . $filename;
                    $_SESSION['user_avatar'] = $avatarPath;
                    // On stocke un timestamp pour bust-cache
                    $_SESSION['avatar_ts']   = time();
                }
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: /compte');
            exit;
        }

        // 4) Mise à jour en base
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

        // 5) Message de succès + fullname en session
        $_SESSION['success']        = "Profil mis à jour avec succès.";
        $_SESSION['user_fullname']  = $fullname;

        header('Location: /compte');
        exit;
    }
}
