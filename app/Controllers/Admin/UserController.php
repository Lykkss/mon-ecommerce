<?php
namespace App\Controllers\Admin;

use App\Models\User;

class UserController
{
    // 1) Liste tous les utilisateurs
    public function index(): void
    {
        $users      = User::all();
        $adminUsers = $users;
        require __DIR__ . '/../../Views/layout.php';
    }

    // 2) Formulaire de création
    public function createForm(): void
    {
        $adminUsersCreate = true;
        require __DIR__ . '/../../Views/layout.php';
    }

    // 3) Traitement de la création
    public function createSubmit(): void
    {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $role     = $_POST['role'] ?? 'user';
        $password = $_POST['password'] ?? '';
        $errors   = [];

        // validation de base…
        if ($username === '') {
            $errors[] = "Le nom d'utilisateur est requis.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide.";
        }
        if (!in_array($role, ['user','admin'], true)) {
            $errors[] = "Rôle invalide.";
        }
        if (strlen($password) < 6) {
            $errors[] = "Le mot de passe doit faire au moins 6 caractères.";
        }

        // --- gestion de l'avatar ---
        $avatarPath = null;
        if (!empty($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['avatar'];
            if ($file['size'] > 2_000_000) {
                $errors[] = "L’avatar ne doit pas dépasser 2 Mo.";
            }
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime  = $finfo->file($file['tmp_name']);
            if (!in_array($mime, ['image/jpeg','image/png'], true)) {
                $errors[] = "Format de l’avatar invalide (jpeg ou png).";
            }
            if (empty($errors)) {
                $dir = __DIR__ . '/../../../public/assets/avatars/';
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                $ext       = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $uniq      = uniqid("avatar_", true);
                $filename  = "{$uniq}.{$ext}";
                $destination = $dir . $filename;
                if (!move_uploaded_file($file['tmp_name'], $destination)) {
                    $errors[] = "Impossible de sauvegarder l’avatar.";
                } else {
                    $avatarPath = 'assets/avatars/' . $filename;
                }
            }
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            header('Location: /admin/users/create');
            exit;
        }

        // hash du mot de passe
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // création
        $data = [
            'username' => $username,
            'email'    => $email,
            'role'     => $role,
            'password' => $hash,
            'fullname' => $_POST['fullname'] ?? null,
        ];
        if ($avatarPath !== null) {
            $data['avatar'] = $avatarPath;
        }

        User::create($data);
        $_SESSION['success'] = 'Utilisateur créé.';
        header('Location: /admin/users');
        exit;
    }

    public function editSubmit(int $id): void
    {
        $user = User::findById($id);
        if (!$user) {
            $_SESSION['errors'] = ["Utilisateur #{$id} introuvable."];
            header('Location: /admin/users');
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $role     = $_POST['role'] ?? 'user';
        $password = $_POST['password'] ?? '';
        $errors   = [];

        // validation de base…
        if ($username === '') {
            $errors[] = "Le nom d'utilisateur est requis.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide.";
        }
        if (!in_array($role, ['user','admin'], true)) {
            $errors[] = "Rôle invalide.";
        }
        if ($password !== '' && strlen($password) < 6) {
            $errors[] = "Le mot de passe doit faire au moins 6 caractères.";
        }

        // --- gestion de l'avatar ---
        $avatarPath = null;
        if (!empty($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['avatar'];
            if ($file['size'] > 2_000_000) {
                $errors[] = "L’avatar ne doit pas dépasser 2 Mo.";
            }
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime  = $finfo->file($file['tmp_name']);
            if (!in_array($mime, ['image/jpeg','image/png'], true)) {
                $errors[] = "Format de l’avatar invalide (jpeg ou png).";
            }
            if (empty($errors)) {
                // suppression de l’ancien
                if (!empty($user['avatar']) && file_exists(__DIR__ . '/../../../public/'.$user['avatar'])) {
                    @unlink(__DIR__ . '/../../../public/'.$user['avatar']);
                }
                $dir       = __DIR__ . '/../../../public/assets/avatars/';
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                $ext       = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $uniq      = uniqid("avatar_", true);
                $filename  = "{$uniq}.{$ext}";
                $destination = $dir . $filename;
                if (!move_uploaded_file($file['tmp_name'], $destination)) {
                    $errors[] = "Impossible de sauvegarder l’avatar.";
                } else {
                    $avatarPath = 'assets/avatars/' . $filename;
                }
            }
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            header("Location: /admin/users/edit/{$id}");
            exit;
        }

        // préparation des données
        $data = [
            'username' => $username,
            'email'    => $email,
            'role'     => $role,
            'fullname' => $_POST['fullname'] ?? null,
        ];
        if ($password !== '') {
            $data['password'] = $password; // le modèle hachera
        }
        if ($avatarPath !== null) {
            $data['avatar'] = $avatarPath;
        }

        User::update($id, $data);
        $_SESSION['success'] = 'Utilisateur mis à jour.';
        header('Location: /admin/users');
        exit;
    }

    // 6) Suppression
    public function delete(int $id): void
    {
        User::delete($id);
        $_SESSION['success'] = 'Utilisateur supprimé.';
        header('Location: /admin/users');
        exit;
    }
}
