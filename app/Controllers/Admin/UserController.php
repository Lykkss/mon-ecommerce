<?php
namespace App\Controllers\Admin;

use App\Core\Database;
use App\Models\User;

class UserController
{
    // Liste tous les utilisateurs
    public function index(): void
    {
        $users = User::findAll();
        $adminUsers = $users;
        require __DIR__ . '/../../Views/layout.php';
    }

    // Formulaire de création
    public function createForm(): void
    {
        $adminUsersCreate = true;
        require __DIR__ . '/../../Views/layout.php';
    }

    // Création d'un utilisateur
    public function createSubmit(): void
    {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $role     = $_POST['role'] ?? 'user';
        $password = $_POST['password'] ?? '';

        $errors = [];
        if ($username === '')     $errors[] = 'Le nom d\'utilisateur est requis.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide.';
        if (!in_array($role, ['user','admin'], true))   $errors[] = 'Rôle invalide.';
        if (strlen($password) < 6)                      $errors[] = 'Le mot de passe doit faire au moins 6 caractères.';

        if ($errors) {
            $_SESSION['errors'] = $errors;
            header('Location: /admin/users/create');
            exit;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        User::create([
            'username' => $username,
            'email'    => $email,
            'role'     => $role,
            'password' => $hash,
        ]);

        $_SESSION['success'] = 'Utilisateur créé.';
        header('Location: /admin/users');
        exit;
    }

    // Formulaire d'édition
    public function editForm(int $id): void
    {
        $user = User::findById($id);
        if (!$user) {
            $_SESSION['errors'] = ["Utilisateur #{$id} introuvable."];
            header('Location: /admin/users');
            exit;
        }
        $adminUsersEdit = true;
        require __DIR__ . '/../../Views/layout.php';
    }

    // Traitement de l'édition
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

        $errors = [];
        if ($username === '')     $errors[] = 'Le nom d\'utilisateur est requis.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide.';
        if (!in_array($role, ['user','admin'], true))   $errors[] = 'Rôle invalide.';
        if ($password !== '' && strlen($password) < 6)   $errors[] = 'Le mot de passe doit faire au moins 6 caractères.';

        if ($errors) {
            $_SESSION['errors'] = $errors;
            header("Location: /admin/users/edit/{$id}");
            exit;
        }

        $data = [
            'username' => $username,
            'email'    => $email,
            'role'     => $role,
        ];
        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        User::update($id, $data);
        $_SESSION['success'] = 'Utilisateur mis à jour.';
        header('Location: /admin/users');
        exit;
    }

    // Suppression
    public function delete(int $id): void
    {
        User::delete($id);
        $_SESSION['success'] = 'Utilisateur supprimé.';
        header('Location: /admin/users');
        exit;
    }
}