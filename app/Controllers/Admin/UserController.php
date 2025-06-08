<?php
// app/Controllers/Admin/UserController.php

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Models\User;
use PDO;
use PDOException;

class UserController
{
    /**
     * GET /admin/users
     * Liste tous les utilisateurs
     */
    public function index(): void
    {
        $db    = Database::getInstance();
        $users = $db->query('SELECT id, username, email, role, fullname FROM users ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);

        $adminUsers = $users;
        require __DIR__ . '/../../Views/layout.php';
    }

    /**
     * GET /admin/users/create
     * Affiche le formulaire de création
     */
    public function createForm(): void
    {
        $adminUsersCreate = true;
        // on fournit un tableau vide pour préremplir le formulaire
        $userToEdit = ['username'=>'', 'email'=>'', 'role'=>'user', 'fullname'=>'', 'avatar'=>null];
        require __DIR__ . '/../../Views/layout.php';
    }

    /**
     * POST /admin/users/create
     * Traite la création
     */
    public function createSubmit(): void
    {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email']    ?? '');
        $role     = $_POST['role'] ?? 'user';
        $fullname = trim($_POST['fullname'] ?? '');
        $password = $_POST['password'] ?? '';

        $errors = [];
        if ($username === '') $errors[] = 'Le username est requis.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide.';
        if (strlen($password) < 6) $errors[] = 'Le mot de passe doit faire au moins 6 caractères.';

        // Gestion de l'avatar si upload
        $avatarPath = null;
        if (!empty($_FILES['avatar']['tmp_name']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png'], true)) {
                $errors[] = 'Avatar : format invalide.';
            } else {
                $dir = __DIR__ . '/../../../public/assets/avatars/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $filename = 'user_'.time().'_'.uniqid().'.'.$ext;
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dir.$filename)) {
                    $avatarPath = 'assets/avatars/'.$filename;
                }
            }
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            header('Location: /admin/users/create');
            exit;
        }

        // Insère en base
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'INSERT INTO users (username, email, role, fullname, avatar, password_hash)
             VALUES (:u, :e, :r, :f, :a, :p)'
        );
        $stmt->execute([
            'u' => $username,
            'e' => $email,
            'r' => $role,
            'f' => $fullname,
            'a' => $avatarPath,
            'p' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        $_SESSION['success'] = 'Utilisateur créé.';
        header('Location: /admin/users');
        exit;
    }

    /**
     * GET /admin/users/edit/{id}
     * Affiche le formulaire d'édition
     */
    public function editForm(int $id): void
    {
        $user = User::findById($id);
        if (!$user) {
            $_SESSION['errors'] = ["Utilisateur #{$id} introuvable."];
            header('Location: /admin/users');
            exit;
        }

        $adminUsersEdit = true;
        $userToEdit     = $user;
        require __DIR__ . '/../../Views/layout.php';
    }

    /**
     * POST /admin/users/edit/{id}
     * Traite la modification
     */
    public function editSubmit(int $id): void
    {
        $user = User::findById($id);
        if (!$user) {
            $_SESSION['errors'] = ["Utilisateur #{$id} introuvable."];
            header('Location: /admin/users');
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email']    ?? '');
        $role     = $_POST['role'] ?? 'user';
        $fullname = trim($_POST['fullname'] ?? '');
        $password = $_POST['password'] ?? '';

        $errors = [];
        if ($username === '') $errors[] = 'Le username est requis.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide.';

        // Si le mot de passe est renseigné, on le met à jour
        $updatePwd = false;
        if ($password !== '') {
            if (strlen($password) < 6) {
                $errors[] = 'Le mot de passe doit faire au moins 6 caractères.';
            } else {
                $updatePwd = true;
            }
        }

        // Avatar
        $avatarPath = $user['avatar'];
        if (!empty($_FILES['avatar']['tmp_name']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png'], true)) {
                $errors[] = 'Avatar : format invalide.';
            } else {
                $dir = __DIR__ . '/../../../public/assets/avatars/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $filename = 'user_'.$id.'_'.time().'.'.$ext;
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dir.$filename)) {
                    $avatarPath = 'assets/avatars/'.$filename;
                }
            }
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            header("Location: /admin/users/edit/{$id}");
            exit;
        }

        // Update
        $db = Database::getInstance();
        $sqlFields = 'username = :u, email = :e, role = :r, fullname = :f, avatar = :a';
        $params    = [
            'u' => $username,
            'e' => $email,
            'r' => $role,
            'f' => $fullname,
            'a' => $avatarPath,
            'i' => $id,
        ];
        if ($updatePwd) {
            $sqlFields .= ', password_hash = :p';
            $params['p'] = password_hash($password, PASSWORD_DEFAULT);
        }
        $stmt = $db->prepare("UPDATE users SET {$sqlFields} WHERE id = :i");
        $stmt->execute($params);

        $_SESSION['success'] = 'Utilisateur mis à jour.';
        header('Location: /admin/users');
        exit;
    }

    /**
     * POST /admin/users/delete/{id}
     * Supprime un utilisateur
     */
    public function delete(int $id): void
    {
        Database::getInstance()
            ->prepare('DELETE FROM users WHERE id = ?')
            ->execute([$id]);

        $_SESSION['success'] = 'Utilisateur supprimé.';
        header('Location: /admin/users');
        exit;
    }
}
